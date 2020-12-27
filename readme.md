# Lumen 6.0 REST API Boilerplate

 ## About 

REST API using Laravel Lumen 6.0

 ## Clone Repository
First, clone the repo:
```bash
$ git clone git@github.com:kaosaetern/lumen-rest-api.git
```

#### Laravel Homestead
It's recommended that you use Laravel Homestead for local development. Follow the [Installation Guide](https://laravel.com/docs/6.x/homestead).

#### Install Dependencies

Go into the new directory and install all the dependencies using Composer. [Get Composer (https://getcomposer.org/)]

```bash
$ cd lumen-rest-api
$ composer install
```

#### Configure Environment

```bash
$ cat .env.example > .env
```

##### Redis

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

#### Connect to Database

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

#### Users API Routes
| HTTP Method   | Path | Action | Scope | Desciption  |
| ----- | ----- | ----- | ---- |------------- |
| GET      | /users | index | users:list | Get all users
| POST     | /users | store | users:create | Create an user
| GET      | /users/{user_id} | show | users:read |  Fetch an user by id
| PUT      | /users/{user_id} | update | users:write | Update an user by id
| DELETE      | /users/{user_id} | destroy | users:delete | Delete an user by id

Note: ```users/me``` is a special route for getting current authenticated user.
And for all User routes 'users' scope is available if you want to perform all actions.


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

## Creating a New Resource

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