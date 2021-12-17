<?php

namespace Modules\QuickClosing\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class QuickClosingServiceProvider extends ServiceProvider
{
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
        \Eventy::addAction('bulk_actions.before_delete', function($mailbox) {
            if (!$mailbox) {
                // return;
            }
            // $workflows = Workflow::where('mailbox_id', $mailbox->id)
            //     ->where('active', true)
            //     ->where('type', Workflow::TYPE_MANUAL)
            //     ->orderBy('sort_order')
            //     ->get();
            // if (!$workflows) {
            //     return;
            // }
            ?>
            <button type="button" class="btn btn-default conv-close" title="Close">
                <span class="glyphicon glyphicon-ok"></span>
            </button>
            <?php   
        }, 50, 1);

        \Eventy::addAction('conversation.action_buttons', function($conversation, $mailbox) {
            ?>
            <button type="button" class="btn btn-default conv-close" title="Close">
                <span class="glyphicon glyphicon-ok"></span>
            </button>
            <?php   
        }, 20, 2);

        
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
            __DIR__.'/../Config/config.php' => config_path('quickclosing.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'quickclosing'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/quickclosing');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/quickclosing';
        }, \Config::get('view.paths')), [$sourcePath]), 'quickclosing');
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