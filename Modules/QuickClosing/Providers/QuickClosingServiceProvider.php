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
        \Eventy::addAction('bulk_actions.before_delete', function ($mailbox) {
            if (!$mailbox) {
                return;
            }

?>
            <button type="button" class="btn btn-default conv-close" title="Close" data-status="3">
                <span class="glyphicon glyphicon-ok"></span>
            </button>

        <?php
        }, 50, 1);

        \Eventy::addAction('conversation.action_buttons', function ($conversation, $mailbox) {
        ?>
            <span class="hidden-xs conv-action glyphicon glyphicon-ok app-conv-close" data-toggle="tooltip" data-status="3" data-placement="bottom" title="" aria-label="Close" role="button" data-original-title="Close"></span>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var btn = document.querySelector('.app-conv-close');

                    btn.addEventListener('click', function() {
                        var url = "https://hd.bizio.site/conversation/ajax?folder_id=37";
                        const formData = new FormData();
                        formData.append('action', 'conversation_change_status');
                        formData.append('status', '3');
                        formData.append('conversation_id', '7766');
                        formData.append('folder_id', '37');

                        // let response = fetch(url, {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json;charset=utf-8'
                        //     },
                        //     body: formData
                        // })
                        // .then(response => response.json())
                        // .then(commits => alert(commits));

                        // let result = await response.json();
                        // alert(result.message);

                    });

                });
            </script>
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
            __DIR__ . '/../Config/config.php' => config_path('quickclosing.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'quickclosing'
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

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

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
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
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
