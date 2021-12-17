<?php

namespace Modules\WooCommerce\Http\Controllers;

use App\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WooCommerceController extends Controller
{
    /**
     * Edit ratings.
     * @return Response
     */
    public function mailboxSettings($id)
    {
        $mailbox = Mailbox::findOrFail($id);

        $settings = \WooCommerce::getMailboxWcSettings($mailbox);

        return view('woocommerce::mailbox_settings', [
            'settings' => [
                'woocommerce.url' => $settings['url'] ?? '',
                'woocommerce.key' => $settings['key'] ?? '',
                'woocommerce.secret' => $settings['secret'] ?? '',
                'woocommerce.version' => $settings['version'] ?? '',
            ],
            'mailbox' => $mailbox
        ]);
    }

    public function mailboxSettingsSave($id, Request $request)
    {
        $mailbox = Mailbox::findOrFail($id);

        $settings = $request->settings ?: [];

        if (!empty($settings)) {
            foreach ($settings as $key => $value) {
                $settings[str_replace('woocommerce.', '', $key)] = $value;
                unset($settings[$key]);
            }
        }

        $mailbox->wc = json_encode($settings);
        $mailbox->save();

        if (!empty($settings['url']) && !empty($settings['key']) && !empty($settings['secret']) && !empty($settings['version'])) {
            // Check API credentials.
            $result = \WooCommerce::apiGetOrders('test@example.org', $mailbox);

            if (!empty($result['error'])) {
                \Session::flash('flash_error', __('Error occurred connecting to the API').': '.$result['error']);
            } else {
                \Session::flash('flash_success', __('Successfully connected to the API.'));
            }
        } else {
            \Session::flash('flash_success_floating', __('Settings updated'));
        }

        return redirect()->route('mailboxes.woocommerce', ['id' => $id]);
    }

    /**
     * Ajax controller.
     */
    public function ajax(Request $request)
    {
        $response = [
            'status' => 'error',
            'msg'    => '', // this is error message
        ];

        switch ($request->action) {

            case 'orders':
                $response['html'] = '';

                $mailbox = null;
                if ($request->mailbox_id) {
                    $mailbox = Mailbox::find($request->mailbox_id);
                }

                $mailbox_api_enabled = \WooCommerce::isMailboxApiEnabled($mailbox);
                $orders = [];
                
                if (\WooCommerce::isApiEnabled() || $mailbox_api_enabled) {

                    $result = \WooCommerce::apiGetOrders($request->customer_email, $mailbox);

                    if (!empty($result['error'])) {
                        \Log::error('[WooCommerce] '.$result['error']);
                    } elseif (is_array($result['data'])) {
                        $orders = $result['data'];

                        // Cache orders for an hour.
                        $cache_key = 'wc_orders_'.$request->customer_email;
                        if ($mailbox_api_enabled) {
                            $cache_key = 'wc_orders_'.$request->mailbox_id.'_'.$request->customer_email;
                        }

                        \Cache::put($cache_key, $orders, now()->addMinutes(60));
                    }
                }
                $response['html'] = \View::make('woocommerce::partials/orders_list', [
                    'orders'         => $orders,
                    'customer_email' => $request->customer_email,
                    'load'           => false,
                    'url'            => \WooCommerce::getSanitizedUrl(),
                ])->render();

                $response['status'] = 'success';
                break;

            default:
                $response['msg'] = 'Unknown action';
                break;
        }

        if ($response['status'] == 'error' && empty($response['msg'])) {
            $response['msg'] = 'Unknown error occured';
        }

        return \Response::json($response);
    }
}
