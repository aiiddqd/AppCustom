<?php

namespace Modules\AppCustom\Providers;

use Illuminate\Support\ServiceProvider;

class AppCustomServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->hooks();
    }

    public function hooks(){
        \Eventy::addAction('menu.append', function () {
            $cron_url = route('system.cron', ['hash' => \Helper::getWebCronHash()]);
            printf( '
            <li class="nav-link">
                <a href="#" class="check-email">Check mail</a>
            </li>
            <li class="nav-link">
                <a href="%s" target="_blank" class="cron-start">Cron start</a>
            </li>
            ', $cron_url);

        }, 20, 2);

        \Eventy::addAction('conversation.action_buttons', function($conversation, $mailbox) {
            echo '
                <span class="hidden-xs conv-action glyphicon glyphicon-ok conv-close" data-toggle="tooltip" data-placement="bottom" title="Close" aria-label="Close" role="button"></span>
            ';

        }, 20, 2);

        \Eventy::addFilter('javascripts', function($javascripts) {
            $javascripts[] = \Module::getPublicPath('appcustom').'/module.js';
            return $javascripts;
        });
    }
}
