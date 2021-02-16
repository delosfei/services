<?php

namespace Delosfei\Services;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{


	public function boot()
	{

	}


	public function register()
	{
        $this->registerScaffoldGenerator();
	}


	private function registerScaffoldGenerator()
	{
		$this->app->singleton('command.larascaf.services', function ($app) {
			return $app['Delosfei\Services\Commands\MakeServicesCommand'];
		});

		$this->commands('command.larascaf.services');
	}
}