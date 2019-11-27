<?php

namespace App\Resources;
use Illuminate\Support\Facades\DB;

class BaseResource 
{
    protected $name;
    protected $nameUpper;
    protected $tableName;
    protected $db;

    /**
     * Create a new instance.
     *
     * @param 
     * @return void
     */
    public function __construct($name = null)
    {
        $nameArray = explode('_', $name);

        $this->tableName = substr($name, -1) === 's' ? strtolower($name) : strtolower($name).'s';

        if(count($nameArray) > 1){
            $this->name = $nameArray[0].ucfirst($nameArray[1]);
            $this->nameUpper = ucfirst($nameArray[0]).ucfirst($nameArray[1]);
        } else {
            $this->name = $name;
            $this->nameUpper = ucfirst($name);
        }

    }

    /**
     * Create controller
     * @param none
     * @return void
     */
    public function createController()
    {
        $controllerName = $this->nameUpper.'Controller.php';
        $resourceName = 'ControllerResource.php';

        $controllerPath = base_path("app/Http/Controllers/".$controllerName);
        $resourceOld = base_path("app/Resources/".$resourceName);
        $resourceNew = base_path("app/Resources/".$controllerName);

        // Create a copy of controller template
        if (!copy($resourceOld, $resourceNew)) {
            echo "Failed to copy $resourceOld...\n";
        }

        // Move the copied controller template to Controllers directory
        rename($resourceNew, $controllerPath);

        // Get the content inside the copied controller template and replace variables
        $str = file_get_contents($controllerPath);
        $str = str_replace('$nameUpper', $this->nameUpper, $str);
        $str = str_replace('$nameLower', $this->name, $str);

        // Put the new content back in the file.
        file_put_contents($controllerPath, $str);
    }

    /**
     * Create model
     * @param none
     * @return void
     */
    public function createModel()
    {
        $modelName = $this->nameUpper.'.php';
        $resourceName = 'ModelResource.php';

        $modelPath = base_path("app/Models/".$modelName);
        $resourceOld = base_path("app/Resources/".$resourceName);
        $resourceNew = base_path("app/Resources/".$modelName);

        // Create a copy of model template
        if (!copy($resourceOld, $resourceNew)) {
            echo "Failed to copy $resourceOld...\n";
        }

        // Move the copied model template to Models directory
        rename($resourceNew, $modelPath);

        // Get the content inside the copied model template and replace variables
        $str = file_get_contents($modelPath);
        $str = str_replace('$nameUpper', $this->nameUpper, $str);
        $str = str_replace('$tableName', $this->tableName, $str);

        // get table column names
        $columnNames = $this->getColumnNames($this->tableName, 'model');

        $str = str_replace('$tableColumnNames', $columnNames, $str);

        // Put the new content back in the file.
        file_put_contents($modelPath, $str);
    }

    /**
     * Create interface
     * @param none
     * @return void
     */
    public function createInterface()
    {
        $interfaceName = $this->nameUpper.'Interface.php';
        $resourceName = 'InterfaceResource.php';

        $interfacePath = base_path("app/Repositories/Contracts/".$interfaceName);
        $resourceOld = base_path("app/Resources/".$resourceName);
        $resourceNew = base_path("app/Resources/".$interfaceName);

        // Create a copy of interface template
        if (!copy($resourceOld, $resourceNew)) {
            echo "Failed to copy $resourceOld...\n";
        }

        // Move the copied interface template to Repositories/Contracts directory
        rename($resourceNew, $interfacePath);

        // Get the content inside the copied interface template and replace variables
        $str = file_get_contents($interfacePath);
        $str = str_replace('$nameUpper', $this->nameUpper, $str);

        // Put the new content back in the file.
        file_put_contents($interfacePath, $str);
    }

    /**
     * Create repository
     * @param none
     * @return void
     */
    public function createRepository()
    {
        $repositoryName = $this->nameUpper.'Repository.php';
        $resourceName = 'RepositoryResource.php';

        $repositoryPath = base_path("app/Repositories/Eloquents/".$repositoryName);
        $resourceOld = base_path("app/Resources/".$resourceName);
        $resourceNew = base_path("app/Resources/".$repositoryName);

        // Create a copy of repository template
        if (!copy($resourceOld, $resourceNew)) {
            echo "Failed to copy $resourceOld...\n";
        }

        // Move the copied repository template to Repositories/Eloquents directory
        rename($resourceNew, $repositoryPath);

        // Get the content inside the copied repository template and replace variables
        $str = file_get_contents($repositoryPath);
        $str = str_replace('$nameUpper', $this->nameUpper, $str);

        // Put the new content back in the file.
        file_put_contents($repositoryPath, $str);
    }

    /**
     * Create transformer
     * @param none
     * @return void
     */
    public function createTransformer()
    {
        $transformerName = $this->nameUpper.'Transformer.php';
        $resourceName = 'TransformerResource.php';

        $transformerPath = base_path("app/Transformers/".$transformerName);
        $resourceOld = base_path("app/Resources/".$resourceName);
        $resourceNew = base_path("app/Resources/".$transformerName);

        // Create a copy of transformer template
        if (!copy($resourceOld, $resourceNew)) {
            echo "Failed to copy $resourceOld...\n";
        }

        // Move the copied transformer template to Repositories/Eloquents directory
        rename($resourceNew, $transformerPath);

        // Get the content inside the copied transformer template and replace variables
        $str = file_get_contents($transformerPath);
        $str = str_replace('$nameUpper', $this->nameUpper, $str);
        // get table column names
        $columnNames = $this->getColumnNames($this->tableName, 'transformer');

        $str = str_replace('$tableColumnNames', $columnNames, $str);

        $str = str_replace('$nameLower', $this->name, $str);

        // Put the new content back in the file.
        file_put_contents($transformerPath, $str);
    }

    /**
     * Add to register method @ RepositoriesServiceProvider.php
     * @param none
     * @return void
     */
    public function createRegister()
    {
        $serviceProvider = base_path("app/Repositories/Providers/RepositoriesServiceProvider.php");

        $str = file_get_contents($serviceProvider);

        $registerStrings = $this->createRegisterStrings();

        $str = preg_replace('/register\(\)[\n\r]+(\s\s\s\s|\t)\{/', $registerStrings, $str);

        // Put the new content back in the file.
        file_put_contents($serviceProvider, $str);

    }

    /**
     * Add to provides method @RepositoriesServiceProvider.php
     * @param none
     * @return void
     */
    public function createProvides()
    {
        $serviceProvider = base_path("app/Repositories/Providers/RepositoriesServiceProvider.php");

        $str = file_get_contents($serviceProvider);

        $providerStrings = $this->createProvideStrings();

        $str = preg_replace('/provides\(\)[\n\r]+(\s\s\s\s|\t)\{[\n\r]+(\s\s\s\s\s\s\s\s|\t\t)return\s\[/', $providerStrings, $str);

        // Put the new content back in the file.
        file_put_contents($serviceProvider, $str);

    }

    /**
     * Add endpoints to route file (web.php)
     * @param none
     * @return void
     */
    public function createRoutes()
    {
        $routes = base_path("routes/web.php");

        $str = file_get_contents($routes);

        $routeStrings = $this->getRouteStrings();

        $str = preg_replace('/\t\}\)\;[\n\r]+\}\)\;/', $routeStrings, $str);

        // Put the new content back in the file.
        file_put_contents($routes, $str);

    }




    /***************************************************************
     * Helper Methods Go Below here.
     ***************************************************************/

    /**
     * Generate endpoints to insert into route file.
     * @param none
     * @return string
     */
    public function getRouteStrings()
    {
        $string = "\t});\n\n";
        $string .= "\t"."$"."router->group(['prefix' => '".str_replace('_','-',$this->tableName)."'], function () use ("."$"."router) {\n";
        $string .= "\t\t"."$"."router->post('/', '".$this->nameUpper."Controller@store');\n";
        $string .= "\t\t"."$"."router->get('/', '".$this->nameUpper."Controller@index');\n";
        $string .= "\t\t"."$"."router->get('/{id}', '".$this->nameUpper."Controller@show');\n";
        $string .= "\t\t"."$"."router->put('/{id}', '".$this->nameUpper."Controller@update');\n";
        $string .= "\t\t"."$"."router->delete('/{id}', '".$this->nameUpper."Controller@destroy');\n";
        $string .= "\t});\n";
        $string .= "});";

        return $string;

    }


    /**
     * Generate resources text to insert in register method
     * @param none
     * @return string
     */
    public function createRegisterStrings(){
        $string = "register()\n\t{\n";
        $string .= "\t\t"."$"."this->app->bind(\App\Repositories\Contracts\\".$this->nameUpper."Interface::class, function (){\n";
        $string .= "\t\t\treturn new \App\Repositories\Eloquents\\".$this->nameUpper."Repository(new \App\Models\\".$this->nameUpper."());\n";
        $string .= "\t\t});";

        return $string;
    }

    /**
     * Generate provide text to insert in provides method
     * @param none
     * @return string
     */
    public function createProvideStrings(){
        $string = "provides()\n\t{\n";
        $string .= "\t\t"."return [\n";
        $string .= "\t\t\t\App\Repositories\Contracts\\".$this->nameUpper."Interface::class,";

        return $string;
    }

    /**
     * Get column names
     * @param $tableName, $type (options: 'model', 'transformer')
     * @return string
     */
    public function getColumnNames($tableName, $type = 'model')
    {
        $query = "DESCRIBE ".$tableName;

        $columns = DB::select(DB::raw($query));
        $array = array_map(function($object){ return (array) $object;}, $columns);

        $columnNames = '';

        if($type === 'model'){
            // remove 'id' as fillable in model
            foreach(array_slice($array, 1) as $data){
                $columnNames .= "\t\t'".$data['Field']."',\n";
            }    
        } else if($type === 'transformer'){
            foreach($array as $data){
                $columnNames .= "\t\t\t'".$data['Field']."'\t\t\t\t\t => $".$this->name."->".$data['Field'].",\n";
            }   
        }

        return $columnNames;
    }

}
?>