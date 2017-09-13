<?php
//namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


// use Illuminate\Console\Command;
// use Illuminate\Foundation\Application;
// use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\Console\Input\InputArgument;

class Compile extends AbstractModuleCommand {
	// *
	//  * IoC
	//  *
	//  * @var Illuminate\Foundation\Application
	 
	// protected $app;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'compile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';


	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{

		$this->info('Publishing module assets');

		// Get all modules or 1 specific
		if ($moduleName = $this->input->getArgument('module')) $modules = array(app('modules')->module($moduleName));
		else                                                   $modules = app('modules')->modules();

		// $destination = app()->make('path.public') . '/modules/assets/';
		$widget_location = app()->make('path.public') . '/assets/js/widgets/';
		$widget_template_location = app()->make('path') . '/views/widgets/';
		// $success = File::deleteDirectory($destination.'js/', true);
		// $success = File::deleteDirectory($destination.'css/', true);

		foreach ($modules as $module)
		{
			if ($module)
			{
//views
//widgets

//styles/js - compile for layouts

					// if($this->app['files']->exists($module->path('assets/js/'))){
					// 	foreach (new DirectoryIterator($module->path('assets/js/')) as $fileInfo) {
					// 		if($fileInfo->isDot()) continue;
					// 		if($fileInfo->getFilename() == 'resource.js'){
					// 			if(!File::exists($destination.'js/'.$module->name())){
					// 				mkdir($destination.'js/'.$module->name());
					// 			}
					// 			file_put_contents($destination.'js/'.$module->name().'/'.$fileInfo->getFilename(), file_get_contents($module->path('assets/js/').$fileInfo->getFilename()), FILE_APPEND | LOCK_EX);
					// 			continue;
					// 		}
					// 		file_put_contents($destination.'js/'.$fileInfo->getFilename(), file_get_contents($module->path('assets/js/').$fileInfo->getFilename()), FILE_APPEND | LOCK_EX);
					// 	}
					// }

					// if($this->app['files']->exists($module->path('assets/css/'))){
					// 	foreach (new DirectoryIterator($module->path('assets/css/')) as $fileInfo) {
					// 		if($fileInfo->isDot()) continue;
					// 		file_put_contents($destination.'css/'.$fileInfo->getFilename(), file_get_contents($module->path('assets/css/').$fileInfo->getFilename()), FILE_APPEND | LOCK_EX);
					// 	}
					// }

					if($this->app['files']->exists($module->path('widgets/'))){
						foreach (new DirectoryIterator($module->path('widgets/')) as $fileInfo) {
							if($fileInfo->isDot()) continue;
							if(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION) == 'mustache'){
								file_put_contents($widget_template_location.$fileInfo->getFilename(), file_get_contents($module->path('widgets/').$fileInfo->getFilename()), LOCK_EX);
							}else{
								file_put_contents($widget_location.'/'.$fileInfo->getFilename(), file_get_contents($module->path('widgets/').$fileInfo->getFilename()), LOCK_EX);
							}
						}
					}




			}
			else
			{
				$this->error("Module '" . $moduleName . "' does not exist.");
			}
		}
		include_once(app()->make('path').'/views/common_includes.blade.php');

		$this->info(exec ('hulk '.implode(' ', Assets::list_templates()).' > '.app()->make('path.public').'/assets/js/common_includes.js'));
		
		// include_once(app()->make('path').'/views/common_includes.blade.php');

		// $this->info(exec ('hulk '.implode(' ', Assets::list_templates()).' > '.app()->make('path.public').'/assets/js/common_includes.js'));
		
		
//hulk ./src/js/themes/bootstrap/*.mustache > ./bin/bootstrap.berry.js


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::OPTIONAL, 'The name of module being published.'),
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
