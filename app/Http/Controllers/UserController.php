<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Helpers\Helper;
use Cache;

class UserController extends Controller
{
    private $userRepository;

    private $userTransformer;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param UserTransformer $userTransformer
     */
    public function __construct(UserInterface $userRepository, UserTransformer $userTransformer)
    {
        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;
        
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $search = (isset($_GET['search']) && $_GET['search'] === 'true') ? true : false;

        $minutes = 1000;  //duration of cache

        if(array_key_exists('sort', $request->all())){
            $sort = explode(':', $request->sort);
            $column = $sort[0];
            $order = $sort[1];

            if($search){
                $user = $this->userRepository->orderBy($column,$order)->findUsers($request->all());
            } else {              
                // create keys
                $key = 'cache_sort_users';
                $users = Cache::tags(['users'])->get($key); //search for keys

                if($users === null){
                    $user = $this->userRepository->orderBy($column,$order)->findBy($request->all());
                    // create a tag so we can flush tag when model is updated
                    Cache::tags(['users'])->put($key, $users, $minutes);
                }
            }
        } else {
            if($search){
                $user = $this->userRepository->orderBy('id','desc')->findUsers($request->all());
            } else {              
                // create keys
                $key = 'cache_default_users';
                $users = Cache::tags(['users'])->get($key); //search for keys

                if($users === null){
                    $user = $this->userRepository->orderBy('id','desc')->findBy($request->all());
                    // create a tag so we can flush tag when model is updated
                    Cache::tags(['users'])->put($key, $users, $minutes);
                }
            }
        }

        return $this->respondWithCollection($user, $this->userTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        return $this->respondWithItem($user, $this->userTransformer);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }
       
        $user = $this->userRepository->save($request->all());

        // Flush cache
        Helper::flushUsers();

        if (!$user instanceof User) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }

        return $this->setStatusCode(201)->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        $user = $this->userRepository->update($user, $request->all());

        // Flush cache
        Helper::flushUsers();

        return $this->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findOne($id);

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        $this->userRepository->delete($user);

        return response()->json(null, 204);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'username'              => 'required|max:50|unique:users',
            'password'              => 'min:5'
        ];

        $requestUser = $request->user();

        // Only admin user can set admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function updateRequestValidationRules(Request $request)
    {
        $userId = $request->segment(2);
        $rules = [
            //'role'                  => 'required',
            //'is_active'             => 'required',
            'password'              => 'min:5'
        ];

        $requestUser = $request->user();

        // Only admin user can update admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }
}