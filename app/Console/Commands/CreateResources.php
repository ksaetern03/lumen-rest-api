<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Resources\BaseResource;
use App\Resources\DeleteResource;

class CreateResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:resources {name} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create resources. Usage: php artisan create:resources customer';


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
        $name = '';
        $name = $this->argument('name');
        $delete = $this->option('d');
        $task = ['createController','createModel', 'createInterface', 'createRepository', 'createTransformer', 'createRegister', 'createProvides', 'createRoutes'];
        $deleteTask = ['deleteController','deleteModel', 'deleteInterface', 'deleteRepository', 'deleteTransformer'];

        if($name !== '' && $name !== null && $delete == false){
            
            $template = new BaseResource($name);

            // create a new progress bar
            $progress = $this->output->createProgressBar(count($task));

            // start and displays the progress bar
            $progress->start();

            // Call function by looping through $task array
            for($i=0; $i<count($task); $i++)
            {
                // call each function stored in $task array
                call_user_func(array($template, (string)$task[$i]));

                // advance the progress bar 1 unit
                $progress->advance();
                $this->info(" ".ucfirst($name).' '.strtolower(substr($task[$i],6))." created.");
            }

            $progress->finish();
            $this->info(" Done"."\n");

        // Delete resources when --d option is presented
        } else {
            
            $delete = new DeleteResource($name);

            // create a new progress bar
            $progress = $this->output->createProgressBar(count($deleteTask));

            // start and displays the progress bar
            $progress->start();

            // Call function by looping through $deleteTask array
            for($i=0; $i<count($deleteTask); $i++)
            {
                // call each function stored in $deleteTask array
                call_user_func(array($delete, (string)$deleteTask[$i]));

                // advance the progress bar 1 unit
                $progress->advance();
                $this->info(" ".ucfirst($name).' '.strtolower(substr($deleteTask[$i],6))." deleted.");
            }

            $progress->finish();
            $this->info(" Done"."\n");
        }

    }
}