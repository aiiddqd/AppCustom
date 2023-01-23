<?php

namespace Modules\AppCustom\Providers;

use Illuminate\Support\ServiceProvider;

class AppCustomServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->hooks();
    }

    public function hooks(){
        \Eventy::addAction('menu.append', function() {
            echo '
            <li class="nav-link">
                <a href="https://m.ddev.app/system/cron/cdb831cab0ae630b541de8c0ddd34d15" target="_blank">Check mail</a>
            </li>
            ';

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
