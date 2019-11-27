# Lumen 5.8 REST API Boilerplate

 ## About 

REST API using Laravel Lumen 5.8

 ## Clone Repository
First, clone the repo:
```bash
$ git clone git@github.com:kaosaetern/lumen-rest-api.git
```

#### Laravel Homestead
It's recommended that you use Laravel Homestead for local development. Follow the [Installation Guide](https://laravel.com/docs/5.7/homestead#installation-and-setup).

#### Install Dependencies

Go into the new directory and install all the dependencies using Composer. [Get Composer (https://getcomposer.org/)]

```bash
$ cd lumen-rest-api
$ composer install
```

#### Documentation

I recommend reading through the [original documentation](https://github.com/hasib32/rest-api-with-lumen).

#### Redis

Add the following to .env.
```bash
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_DRIVER=database

REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_DATABASE=0
REDIS_PASSWORD=null
```

Create caching keys in controller.
```bash
$key = 'cache_sort_users';
$users = Cache::tags(['users'])->get($key); //search for keys

if($users === null){
    $user = $this->userRepository->orderBy($column,$order)->findBy($request->all());
    // create a tag so we can flush tag when model is updated
    Cache::tags(['users'])->put($key, $users, $minutes);
}
```

Flush cache when new models are created.

```bash
Cache::tags('users')->flush();
```

#### Artisan Command for Automatically Generating Resources

Run the following command to create resource:

```bash
php artisan create:resources {name : name of resource to create} {--d : be careful using this as it will delete the specified resource}
```
This will generate the following:

- Controller (App/Http/Controllers)
- Model (App/Models)
- Interface (App/Repositories/Contracts)
- Repository (App/Repositories/Eloquents)
- Transformer (App/Transformers)

After generating the resource, register your repository in RepositoriesServiceProvider located in App\Repositories\Providers.

```bash
class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\Contracts\UserInterface::class, function (){
            return new \App\Repositories\Eloquents\UserRepository(new \App\Models\User());
        });
    }

    /**
     * Get the services provided by the provider.
     * 
     * @return array
     */
    public function provides()
    {
        return [
            \App\Repositories\Contracts\UserInterface::class,
        ];
    }
}
```