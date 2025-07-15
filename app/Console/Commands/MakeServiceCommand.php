<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $servicePath = app_path("Services/{$name}.php");

        if (File::exists($servicePath)) {
            $this->error("❌ Service already exists at: {$servicePath}");
            return;
        }

        // Create Services directory if not exists
        if (!File::isDirectory(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }

        // Create the service file
        File::put($servicePath, $this->getServiceStub($name));

        $this->info("✅ Service created at: app/Services/{$name}.php");
    }

    protected function getServiceStub($name)
    {
        return <<<PHP
<?php

namespace App\Services;

class {$name}
{
    public function __construct()
    {
        // Service logic
    }
}
PHP;
    }
}
