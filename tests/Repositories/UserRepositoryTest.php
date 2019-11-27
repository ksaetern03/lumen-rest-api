<?php

namespace Tests\Repositories;

use App\Models\User;
use App\Repositories\Eloquents\UserRepository;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepositoryTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @var UserRepository
     */
    protected $userRepository;
    
    public function setup()
    {
        parent::setUp();
        $this->userRepository = new UserRepository(new User());
    }

    public function testCreateUser()
    {
        $testUserArray = factory(User::class)->make()->toArray();
        $user = $this->userRepository->save($testUserArray);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($testUserArray['username'], $user->username);
    }

    public function testFindOne()
    {
        $testUser = factory(User::class)->create();

        //first, check if it returns valid user
        $user = $this->userRepository->findOne($testUser->id);
        $this->assertInstanceOf(User::class, $user);

        //now check it returns null for gibberish data
        $user = $this->userRepository->findOne('giberish');
        $this->assertNull($user);
    }

    public function testFindOneBy()
    {
        $testUser = factory(User::class)->create();

        //first, check if it returns valid user
        $user = $this->userRepository->findOneBy(['id' => $testUser->id]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($testUser->username, $user->username);

        //check if it returns valid user, for multiple criteria
        $user = $this->userRepository->findOneBy([
            'username'     => $testUser->username
        ]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($testUser->username, $user->username);

        //now check it returns null for gibberish data
        $user = $this->userRepository->findOneBy(['username' => 'Test Last']);
        $this->assertNull($user);
    }

    public function testFindBy()
    {
        // when instantiate the repo, logged in as Admin user. So, that we can search any user
        $adminUser = factory(User::class)->make(['role' => User::ADMIN_ROLE]);
        Auth::shouldReceive('user')->andReturn($adminUser);
        $userRepository = new UserRepository(new User());

        //get total users of this resource
        $totalUsers = User::all()->count();

        //first, check if it returns all users without criteria
        $users = $userRepository->findBy([]);
        $this->assertCount($totalUsers, $users);

        //create a user and findBy that using user's firstName
        factory(User::class)->create(['username' => 'Pappu']);
        $users = $userRepository->findBy(['username' => 'Pappu']);
        //test instanceof
        $this->assertInstanceOf(LengthAwarePaginator::class, $users);
        $this->assertNotEmpty($users);

        //check with multiple criteria
        $searchCriteria = ['role'  => '11121', 'username' => 'jobberAli'];
        $previousTotalUsers = $userRepository->findBy($searchCriteria)->count();
        $this->assertEmpty($previousTotalUsers);

        //create a user and findBy using that username
        factory(User::class)->create(['username' => 'Jobber']);
        $users = $this->userRepository->findBy(['username' => 'Jobber']);
        $this->assertEmpty($users);
    }

    public function testUpdate()
    {
        $testUser = factory(User::class)->create([
            'username' => 'test_first'
        ]);

        // First, test user instance
        $user = $this->userRepository->findOne($testUser->id);
        $this->assertInstanceOf(User::class, $user);

        // Update user
        $this->userRepository->update($testUser, [
            'username' => 'test_first'
        ]);

        // Fetch the user again
        $user = $this->userRepository->findOne($testUser->id);
        $this->assertEquals('test_first', $user->username);
        $this->assertNotEquals('test_first1', $user->username);
    }

    public function testDelete()
    {
        $testUser = factory(User::class)->create();

        $isDeleted = $this->userRepository->delete($testUser);
        $this->assertTrue($isDeleted);

        // confirm deleted
        $user = $this->userRepository->findOne($testUser->id);
        $this->assertNull($user);
    }
}
