<?php

namespace Vshapovalov\Pages\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Vshapovalov\Pages\PagesServiceProvider;

class InstallCommand extends Command
{
	protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install crud admin panel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

	/**
	 * Get the composer command for the environment.
	 *
	 * @return string
	 */
	protected function findComposer()
	{
		if (file_exists(getcwd().'/composer.phar')) {
			return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
		}

		return 'composer';
	}

	public function seed($class)
	{
		if (!class_exists($class)) {
			require_once $this->seedersPath.$class.'.php';
		}

		with(new $class())->run();
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
	    $this->info('Publishing the crud assets, database and config files');
	    $this->call('vendor:publish', ['--provider' => PagesServiceProvider::class]);

	    $this->info('Migrating the database tables into your application');
	    $this->call('migrate');

	    $this->info('Dumping the autoloaded files and reloading all new files');

	    $composer = $this->findComposer();

	    $process = new Process($composer.' dump-autoload');
	    $process->setWorkingDirectory(base_path())->run();

	    $this->info('Adding Voyager routes to routes/web.php');

	    $routes_contents = $filesystem->get(base_path('routes/web.php'));

	    if (false === strpos($routes_contents, 'Crud::routes()')) {
		    $filesystem->append(
			    base_path('routes/web.php'),
			    "\n\nCrud::routes();\n"
		    );
	    }

	    $this->info('Seeding data into the database');

	    $this->seed('CrudFieldTypesTableSeeder');
	    $this->seed('AdminSettingGroupsTableSeeder');
	    $this->seed('MediaItemsTableSeeder');

	    $this->info('Adding the storage symlink to public folder');
	    $this->call('storage:link');

	    $this->info('Crud successfully installed!');
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(Filesystem $filesystem)
	{
		$this->handle($filesystem);
	}
}
