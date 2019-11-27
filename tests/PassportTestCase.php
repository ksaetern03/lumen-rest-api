<?php

use App\Models\User;
use Laravel\Passport\ClientRepository;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PassportTestCase extends \TestCase
{
    use DatabaseTransactions;

    protected $headers = [];
    protected $scopes = [];
    protected $user;

    /**
     * Create access token since our api uses Authentication with Passport.
     */
    public function setUp()
    {
        parent::setUp();
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', $this->baseUrl
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);
        $this->user = factory(User::class)->create();
        $token = $this->user->createToken('TestToken', $this->scopes)->accessToken;

        $this->headers['Accept'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.$token;
    }

    /**
     * GET method with pre-defined headers
     */
    public function get($uri, array $headers = [])
    {
        return parent::get($uri, array_merge($this->headers, $headers));
    }

    /**
     * POST method with pre-defined headers
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        return parent::post($uri, $data, array_merge($this->headers, $headers));
    }
    
    /**
     * POST method with pre-defined headers
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        return parent::put($uri, $data, array_merge($this->headers, $headers));
    }
    
    /**
     * PATCH method with pre-defined headers
     */  
    public function patch($uri, array $data = [], array $headers = [])
    {
        return parent::patch($uri, $data, array_merge($this->headers, $headers));
    }
    
    /**
     * DELETE method with pre-defined headers
     */   
    public function delete($uri, array $data = [], array $headers = [])
    {
        return parent::delete($uri, $data, array_merge($this->headers, $headers));
    }
}