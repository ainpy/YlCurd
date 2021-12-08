<?php

namespace ainpy\YlCurd;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Str;

class YlMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yl:curd {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = null;
        $name = $this->argument('name');
        if (strstr($name, '/')) {
            $path = explode('/', $name)[0];
            $name = explode('/', $name)[1];
        }
        $this->controller($path, $name);
        $this->model($path, $name);
        $this->request($path, $name);
        $this->resource($path, $name);
//
        $table = strtolower(Str::snake(Str::plural($name)));
        Artisan::call("make:migration create_{$table}_table --create={$table}");
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../stubs/{$type}.stub");
    }

    protected function model($path, $name)
    {
        $namespace = is_null($path) ? "namespace App\Models;" : "namespace App\Models\\{$path};";
        $modelTemplate = str_replace(
            ['{{modelName}}', '{{namespace}}'],
            [$name, $namespace],
            $this->getStub('Model')
        );

        if (! file_exists(app_path('/Models'))) {
            mkdir(app_path('/Models'), 0777, true);
        }

        if (! is_null($path) && ! file_exists(app_path('/Models/'.$path))) {
            mkdir(app_path('/Models/'.$path), 0777, true);
        }

        $filePath = is_null($path) ? "/Models/{$name}.php" : "/Models/{$path}/{$name}.php";

        file_put_contents(app_path($filePath), $modelTemplate);
    }

    protected function controller($path, $name)
    {
        $namespace = is_null($path) ? "namespace App\Http\Controllers;" : "namespace App\Http\Controllers\\{$path};";
        $useModel = is_null($path) ? "use App\Models\\{$name};" : "use App\Models\\{$path}\\{$name};";
        $useRequest = is_null($path) ? "use App\Http\Requests\\{$name}Request;" : "use App\Http\Requests\\{$path}\\{$name}Request;";
        $useResource = is_null($path) ? "use App\Http\Resources\\{$name}Resource;" : "use App\Http\Resources\\{$path}\\{$name}Resource;";
        $controllerTemplate = str_replace(
            [
                '{{namespace}}',
                '{{useModel}}',
                '{{useRequest}}',
                '{{useResource}}',
                '{{modelName}}',
                '{{time}}',
            ],
            [
                $namespace,
                $useModel,
                $useRequest,
                $useResource,
                $name,
                now(),
            ],
            $this->getStub('Controller')
        );

        if (! is_null($path) && ! file_exists(app_path('/Http/Controllers/'.$path))) {
            mkdir(app_path('/Http/Controllers/'.$path), 0777, true);
        }

        $filePath = is_null($path) ? "/Http/Controllers/{$name}Controller.php" : "/Http/Controllers/{$path}/{$name}Controller.php";

        file_put_contents(app_path($filePath), $controllerTemplate);
    }

    protected function request($path, $name)
    {
        $namespace = is_null($path) ? "namespace App\Http\Requests;" : "namespace App\Http\Requests\\{$path};";
        $requestTemplate = str_replace(
            ['{{modelName}}', '{{namespace}}'],
            [$name, $namespace],
            $this->getStub('Request')
        );

        if (! file_exists(app_path('/Http/Requests'))) {
            mkdir(app_path('/Http/Requests'), 0777, true);
        }

        if (! is_null($path) && ! file_exists(app_path('/Http/Requests/'.$path))) {
            mkdir(app_path('/Http/Requests/'.$path), 0777, true);
        }

        $filePath = is_null($path) ? "/Http/Requests/{$name}Request.php" : "/Http/Requests/{$path}/{$name}Request.php";

        file_put_contents(app_path($filePath), $requestTemplate);
    }

    protected function resource($path, $name)
    {
        $namespace = is_null($path) ? "namespace App\Http\Resources;" : "namespace App\Http\Resources\\{$path};";
        $requestTemplate = str_replace(
            ['{{modelName}}', '{{namespace}}'],
            [$name, $namespace],
            $this->getStub('Resource')
        );

        if (! file_exists(app_path('/Http/Resources'))) {
            mkdir(app_path('/Http/Resources'), 0777, true);
        }

        if (! is_null($path) && ! file_exists(app_path('/Http/Resources/'.$path))) {
            mkdir(app_path('/Http/Resources/'.$path), 0777, true);
        }

        $filePath = is_null($path) ? "/Http/Resources/{$name}Resource.php" : "/Http/Resources/{$path}/{$name}Resource.php";

        file_put_contents(app_path($filePath), $requestTemplate);
    }
}
