<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\BaseInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

abstract class AbstractRepository implements BaseInterface
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model
     *
     * @var Model
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $loggedInUser
     */
    protected $loggedInUser;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->loggedInUser = $this->getLoggedInUser();
    }

    /**
     * Get Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria)
    {
        return $this->model->where($criteria)->first();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [])
    {   
        if(!empty($searchCriteria['paginate']) && $searchCriteria['paginate'] == 'false') {
            $count = $this->model->count();
            $limit = $count;
        } else if (empty($searchCriteria['paginate'])) {
            $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 30; // it's needed for pagination
        }

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        }
        );

        return $queryBuilder->paginate($limit);
    }


    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, ['page', 'per_page', 'paginate', 'include', 'sort'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(',', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = '=';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        // generate uid
        //$data['uid'] = Uuid::uuid4();

        // get logged username
        $data['created_by'] = $this->loggedInUser->username;

        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function createOrUpdate(array $checkData, array $updateData)
    {
        // get logged username
        return $this->model->updateOrCreate($checkData, $updateData);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // get logged username
        $model->updated_by = $this->loggedInUser->username;  
        
        // update the model
        $model->save();

        // get updated model from database
        $model = $this->findOne($model->id);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findIn($key, array $values)
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * get loggedIn user
     *
     * @return User
     */
    public function getLoggedInUser()
    {
        $user = \Auth::user();

        if ($user instanceof User) {
            return $user;
        } else {
            return new User();
        }
    }

    /**
     * Add condition to the query
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     *
     * @return $this
     */
    public function where($column, $operator, $value)
    {
        $this->model = $this->model->where($column, $operator, $value);
        
        return $this;
    }

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);
        
        return $this;
    }
}