<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserInterface;

class AccessTokenController extends Controller
{

    private $userRepository;

    /**
     * Constructor
     *
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Since, with Laravel|Lumen passport doesn't restrict
     * a client requesting any scope. we have to restrict it.
     * http://stackoverflow.com/questions/39436509/laravel-passport-scopes
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createAccessToken(Request $request)
    {
        $inputs = $request->all();

        $user = null;
        if (isset($inputs['username']) && $inputs['grant_type'] == 'password') {
            $user = $this->userRepository->findOneBy(['username' => $inputs['username']]);
        }

        // only allow active user to login
        if (($user instanceof User) && $user->is_active === true) {
            if ($user->role === User::ADMIN_ROLE) {
                $inputs['scope'] = 'admin';
            }
            else if ($user->role === User::BASIC_ROLE)
            {
                $inputs['scope'] = 'basic';

            }
        } else {
            // no access
            $inputs['scope'] = 'no_access';
        }

        $tokenRequest = $request->create('/oauth/token', 'post', $inputs);

        // forward the request to the oauth token request endpoint
        return app()->dispatch($tokenRequest);
    }
}