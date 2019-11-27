<?php

namespace App\Http\Controllers;

use App\Models\$nameUpper;
use App\Repositories\Contracts\$nameUpperInterface;
use Illuminate\Http\Request;
use App\Transformers\$nameUpperTransformer;
use App\Helpers\Helper;
use Cache;

class $nameUpperController extends Controller
{
    private $$nameLowerRepository;

    private $$nameLowerTransformer;

    /**
     * Constructor
     *
     * @param $nameUpperRepository $$nameLowerRepository
     * @param $nameUpperTransformer $$nameLowerTransformer
     */
    public function __construct($nameUpperInterface $$nameLowerRepository, $nameUpperTransformer $$nameLowerTransformer)
    {
        $this->$nameLowerRepository = $$nameLowerRepository;
        $this->$nameLowerTransformer = $$nameLowerTransformer;
        
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
                $$nameLower = $this->$nameLowerRepository->orderBy($column,$order)->find$nameUppers($request->all());
            } else {              
                // create keys
                $key = 'cache_sort_$nameLowers';
                $$nameLowers = Cache::tags(['$nameLowers'])->get($key); //search for keys

                if($$nameLowers === null){
                    $$nameLower = $this->$nameLowerRepository->orderBy($column,$order)->findBy($request->all());
                    // create a tag so we can flush tag when model is updated
                    Cache::tags(['$nameLowers'])->put($key, $$nameLowers, $minutes);
                }
            }
        } else {
            if($search){
                $$nameLower = $this->$nameLowerRepository->orderBy('id','desc')->find$nameUppers($request->all());
            } else {              
                // create keys
                $key = 'cache_default_$nameLowers';
                $$nameLowers = Cache::tags(['$nameLowers'])->get($key); //search for keys

                if($$nameLowers === null){
                    $$nameLower = $this->$nameLowerRepository->orderBy('id','desc')->findBy($request->all());
                    // create a tag so we can flush tag when model is updated
                    Cache::tags(['$nameLowers'])->put($key, $$nameLowers, $minutes);
                }
            }
        }

        return $this->respondWithCollection($$nameLower, $this->$nameLowerTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $$nameLower = $this->$nameLowerRepository->findOne($id);

        if (!$$nameLower instanceof $nameUpper) {
            return $this->sendNotFoundResponse("The $nameLower with id {$id} doesn't exist");
        }

        return $this->respondWithItem($$nameLower, $this->$nameLowerTransformer);
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
       
        $$nameLower = $this->$nameLowerRepository->save($request->all());

        // Flush cache
        Helper::flush$nameUppers();

        if (!$$nameLower instanceof $nameUpper) {
            return $this->sendCustomResponse(500, 'Error occurred on creating $nameUpper');
        }

        return $this->setStatusCode(201)->respondWithItem($$nameLower, $this->$nameLowerTransformer);
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

        $$nameLower = $this->$nameLowerRepository->findOne($id);

        if (!$$nameLower instanceof $nameUpper) {
            return $this->sendNotFoundResponse("The $nameLower with id {$id} doesn't exist");
        }

        $$nameLower = $this->$nameLowerRepository->update($$nameLower, $request->all());

        // Flush cache
        Helper::flush$nameUppers();

        return $this->respondWithItem($$nameLower, $this->$nameLowerTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $$nameLower = $this->$nameLowerRepository->findOne($id);

        if (!$$nameLower instanceof $nameUpper) {
            return $this->sendNotFoundResponse("The $nameLower with id {$id} doesn't exist");
        }

        $this->$nameLowerRepository->delete($$nameLower);

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
        ];

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
        $rules = [
        ];

        return $rules;
    }
}