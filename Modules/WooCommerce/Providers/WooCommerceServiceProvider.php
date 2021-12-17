<?php

namespace Modules\WooCommerce\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

// Module alias.
define('WC_MODULE', 'woocommerce');

class WooCommerceServiceProvider extends ServiceProvider
{
    const MAX_ORDERS = 5;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add module's CSS file to the application layout.
        \Eventy::addFilter('stylesheets', function($styles) {
            $styles[] = \Module::getPublicPath(WC_MODULE).'/css/module.css';
            return $styles;
        });

        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath(WC_MODULE).'/js/laroute.js';
            $javascripts[] = \Module::getPublicPath(WC_MODULE).'/js/module.js';
            return $javascripts;
        });

        // Add item to the mailbox menu.
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('woocommerce::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 34);

        // Section settings.
        \Eventy::addFilter('settings.sections', function($sections) {
            $sections[WC_MODULE] = ['title' => 'WooCommerce', 'icon' => 'shopping-cart', 'order' => 550];

            return $sections;
        }, 35);

        // Section parameters.
        \Eventy::addFilter('settings.section_params', function($params, $section) {
           
            if ($section != WC_MODULE) {
                return $params;
            }

            $params['settings'] = [
                'woocommerce.url' => [
                    'env' => 'WOOCOMMERCE_URL',
                ],
                'woocommerce.key' => [
                    'env' => 'WOOCOMMERCE_KEY',
                ],
                'woocommerce.secret' => [
                    'env' => 'WOOCOMMERCE_SECRET',
                ],
                'woocommerce.version' => [
                    'env' => 'WOOCOMMERCE_VERSION',
                ],
            ];

            // Validation.
            // $params['validator_rules'] = [
            //     'settings.woocommerce\.url' => 'required|url',
            // ];

            return $params;
        }, 20, 2);

        // Settings view.
        \Eventy::addFilter('settings.view', function($view, $section) {
            if ($section != WC_MODULE) {
                return $view;
            } else {
                return 'woocommerce::settings';
            }
        }, 20, 2);

        // Section settings.
        \Eventy::addFilter('settings.section_settings', function($settings, $section) {
           
            if ($section != WC_MODULE) {
                return $settings;
            }

            $settings['woocommerce.url'] = config('woocommerce.url');
            $settings['woocommerce.key'] = config('woocommerce.key');
            $settings['woocommerce.secret'] = config('woocommerce.secret');
            $settings['woocommerce.version'] = config('woocommerce.version');

            return $settings;
        }, 20, 2);

        // Before saving settings.
        \Eventy::addFilter('settings.before_save', function($request, $section, $settings) {

            if ($section != WC_MODULE) {
                return $request;
            }

            if (!empty($request->settings['woocommerce.url'])) {
                $settings = $request->settings;

                $settings['woocommerce.url'] = preg_replace("/https?:\/\//i", '', $settings['woocommerce.url']);

                $request->merge([
                    'settings' => $settings,
                ]);
            }

            return $request;
        }, 20, 3);

        // After saving settings.
        \Eventy::addFilter('settings.after_save', function($response, $request, $section, $settings) {

            if ($section != WC_MODULE) {
                return $response;
            }

            if (self::isApiEnabled()) {
                // Check API credentials.
                $result = self::apiGetOrders('test@example.org');

                if (!empty($result['error'])) {
                    $request->session()->flash('flash_error', __('Error occurred connecting to the API').': '.$result['error']);
                } else {
                    $request->session()->flash('flash_success', __('Successfully connected to the API.'));
                }
            }

            return $response;
        }, 20, 4);

        // Show recent orders.
        \Eventy::addAction('conversation.after_prev_convs', function($customer, $conversation, $mailbox) {

            $load = false;
            $orders = [];

            $customer_email = $customer->getMainEmail();

            if (!$customer_email) {
                return;
            }

            if (!\WooCommerce::isApiEnabled() && !\WooCommerce::isMailboxApiEnabled($mailbox)) {
                return;
            }

            $cached_orders = [];
            if (self::isMailboxApiEnabled($mailbox)) {
                $cached_orders_json = \Cache::get('wc_orders_'.$mailbox->id.'_'.$customer_email);
            } else {
                $cached_orders_json = \Cache::get('wc_orders_'.$customer_email);
            }
            if ($cached_orders_json && is_array($cached_orders_json)) {
                $orders = $cached_orders_json;
            } else {
                $load = true;
            }

            // if (self::isApiEnabled()) {
            //     $result = self::apiGetOrders($customer_email);

            //     if (!empty($result['error'])) {
            //         \Log::error('[WooCommerce] '.$result['error']);
            //     } elseif (!empty($result['data'])) {
            //         $orders = $result['data'];

            //         // Cache orders for an hour.
            //         \Cache::put('wc_orders_'.$customer_email, $orders, now()->addMinutes(60));
            //     }
            // }

            echo \View::make('woocommerce::partials/orders', [
                'orders'         => $orders,
                'customer_email' => $customer_email,
                'load'           => $load,
                'url'            => \WooCommerce::getSanitizedUrl(),
            ])->render();

        }, 12, 3);

        // Custom menu in conversation.
        \Eventy::addAction('conversation.customer.menu', function($customer, $conversation) {
            ?>
                <li role="presentation" class="col3-hidden"><a data-toggle="collapse" href=".wc-collapse-orders" tabindex="-1" role="menuitem"><?php echo __("Recent Orders") ?></a></li>
            <?php
        }, 12, 2);

    }

    public static function isApiEnabled()
    {
        return (config('woocommerce.url') && config('woocommerce.key') && config('woocommerce.secret') && config('woocommerce.version'));
    }

    public static function isMailboxApiEnabled($mailbox)
    {
        if (empty($mailbox) || empty($mailbox->wc)) {
            return false;
        }
        $settings = self::getMailboxWcSettings($mailbox);

        return (!empty($settings['url']) && !empty($settings['key']) && !empty($settings['secret']) && !empty($settings['version']));
    }

    public static function getMailboxWcSettings($mailbox)
    {
        return json_decode($mailbox->wc ?: '', true);
    }

    public static function formatDate($date)
    {
        $date_carbon = Carbon::parse($date);

        if (!$date_carbon) {
            return '';
        }

        return $date_carbon->format('M j, Y');
    }

    public static function getSanitizedUrl($url = '')
    {
        if (empty($url)) {
            $url = config('woocommerce.url');
        }

        $url = preg_replace("/https?:\/\//i", '', $url);

        if (substr($url, -1) != '/') {
            $url .= '/';
        }

        return 'https://'.$url;
    }

    /**
     * Retrieve WooCommerce orders for customer.
     */
    public static function apiGetOrders($customer_email, $mailbox = null)
    {
        $response = [
            'error' => '',
            'data' => [],
        ];

        if ($mailbox && \WooCommerce::isMailboxApiEnabled($mailbox)) {
            $settings = self::getMailboxWcSettings($mailbox);

            $url = self::getSanitizedUrl($settings['url']);
            $key = $settings['key'];
            $secret = $settings['secret'];
            $version = $settings['version'];
        } else {
            $url = self::getSanitizedUrl();
            $key = config('woocommerce.key');
            $secret = config('woocommerce.secret');
            $version = config('woocommerce.version');
        }

        $request_url = $url.'wp-json/wc/v'.$version.'/orders';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url.'?consumer_key='.$key.'&consumer_secret='.$secret.'&per_page='.self::MAX_ORDERS.'&search='.$customer_email);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $json = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $json_decoded = json_decode($json, true);

            if ($status_code == 200) {
                if (!empty($json_decoded['data'][0]['currency'])) {
                    $response['data'] = $json_decoded['data'];
                } elseif (!empty($json_decoded[0]['currency'])) {
                    $response['data'] = $json_decoded;
                }
            } else {
                $response['error'] = 'HTTP Status Code: '.$status_code.' ('.self::errorCodeDescr($status_code).')';

                if (!empty($json_decoded['code']) && !empty($json_decoded['message'])) {
                    $response['error'] .= ' | API Status Code: '.$json_decoded['code'].' ('.$json_decoded['message'].')';
                }
            }

        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        if ($response['error']) {
            $response['error'] .= ' | Requested resource: '.$request_url;
        }

        return $response;
    }

    public static function errorCodeDescr($code)
    {
        switch ($code) {
            case 400:
                $descr = __('Bad request');
                break;
            case 401:
                $descr = __('Authentication or permission error, e.g. incorrect API keys or your store is protected with Basic HTTP Authentication');
                break;
            case 0:
            case 404:
                $descr = __('Store not found at the specified URL');
                break;
            case 404:
                $descr = __('Internal store error');
                break;
            default:
                $descr = __('Unknown error');
                break;
        }

        return $descr;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('woocommerce.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'woocommerce'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/woocommerce');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/woocommerce';
        }, \Config::get('view.paths')), [$sourcePath]), 'woocommerce');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
