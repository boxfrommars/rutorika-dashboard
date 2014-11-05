<?php namespace Rutorika\Dashboard;

use Illuminate\Support\ServiceProvider;

use Rutorika\Dashboard\HTML\FormBuilder;
use Illuminate\Html\HtmlServiceProvider as IlluminateHtmlServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('rutorika/dashboard');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('flash', function () {
            return $this->app->make('Rutorika\Dashboard\Notifications\FlashNotifier');
        });

        $this->app->bindShared('formbuilder', function($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());
            return $form->setSessionStore($app['session.store']);
        });
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
