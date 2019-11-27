<?php

namespace Tests\Endpoints;

use App\Models\User;
use Laravel\Passport\ClientRepository;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UsersTest extends \PassportTestCase
{
    use DatabaseTransactions;

    protected $scopes = ['admin'];

    /**
     * Get all users
     */
    public function testGetAllUsers()
    {
        $this->get('/v1/users')
            ->assertResponseStatus(200);

        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'username',
                    'is_active',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                ]
            ],
            'meta' => [
                '*' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'links',
                ]
            ]
        ]);
    }

    /**
     * Get specific user
     */
    public function testGetSpecificUser()
    {

        $user = factory(User::class)->create();

        $this->get('/v1/users/'.$user->uid);
        $this->assertResponseStatus(200);

        $this->seeJson(['username' => $user->username]);

        $this->get('/v1/users/13232323');
        $this->assertResponseStatus(404);
    }


    /**
     * Create new user
     */
    public function testCreateUser()
    {
        $this->post('/v1/users', []);
        $this->assertResponseStatus(400);

        $this->post('/v1/users', []);
        $this->assertResponseStatus(400);

        $this->post('/v1/users', [
            'username'     => 'test@test.com',
            'is_active' => true,
        ]);
        $this->assertResponseStatus(201);

        $this->post('/v1/users', [
            'username'     => 'test@test.com',
            'is_active' => true,
        ]);
        $this->assertResponseStatus(400);
    }

    public function testUpdateUser()
    {
        $user = factory(User::class)->create();

        $this->put('/v1/users/'.$user->id, [
            'username' => 'updated_first'
        ]);
        $this->assertResponseOk();

        $this->put('/v1/users/234324', [
            'username' => 'updated_first'
        ]);
        $this->assertResponseStatus(404);
    }

    public function testDeleteUser()
    {
        $user = factory(User::class)->create();

        $this->delete('/v1/users/'.$user->id);
        $this->assertResponseStatus(204);

        $this->get('/v1/users/13232323');
        $this->assertResponseStatus(404);
    }
}