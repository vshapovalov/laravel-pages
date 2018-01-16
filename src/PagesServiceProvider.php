<?php

namespace Vshapovalov\Pages;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Vshapovalov\Pages\Commands\InstallCommand;
use Vshapovalov\Pages\Facades\Pages as PagesFacade;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

//        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pages');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	    $loader = AliasLoader::getInstance();
	    $loader->alias('Pages', PagesFacade::class);

    	$this->app->singleton('pages', function () {
    		return new Pages();
	    });

    	$this->loadHelpers();

    	if ($this->app->runningInConsole()) {

		    $this->registerPublishableResources();
		    $this->registerConsoleCommands();
	    }
    }

    function registerConsoleCommands(){
    	$this->commands(InstallCommand::class);
    }

	/**
	 * Register the publishable files.
	 */
	private function registerPublishableResources()
	{
		$publishablePath = dirname(__DIR__).'/publishable';
		$assetsPath = '/vendor/vshapovalov/pages/assets';

		$publishable = [
			'crud_assets' => [
				"{$publishablePath}/assets/" => public_path($assetsPath),
			],
			'crud_migrations' => [
				"{$publishablePath}/database/migrations/" => database_path('migrations'),
			],
			'crud_seeds' => [
				"{$publishablePath}/database/seeds/" => database_path('seeds'),
			],
			'crud_scripts' => [
				"{$publishablePath}/database/scripts/" => database_path('scripts'),
			],
			'crud_config' => [
				"{$publishablePath}/config/pages.php" => config_path('pages.php'),
			],
		];

		foreach ($publishable as $group => $paths) {
			$this->publishes($paths, $group);
		}
	}

	public function provides()
	{
		return [
			Pages::class,
		];
	}

	/**
	 * Load helpers.
	 */
	protected function loadHelpers()
	{
		foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
			require_once $filename;
		}
	}


}
