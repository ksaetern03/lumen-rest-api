<?php 

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
    /**
     * Find all resources when paginate=false
     *
     * @param none
     * @return Model|null
     */
    public function findAll();

    /**
     * Find a resource by id
     *
     * @param $id
     * @return Model|null
     */
    public function findOne($id);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @return Model|null
     */
    public function findOneBy(array $criteria);

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @return Collection
     */
    public function findBy(array $searchCriteria = []);

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn($key, array $values);

    /**
     * Save a resource
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data);

    /**
     * Create or Update
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function createOrUpdate(array $checkData, array $updateData);

    /**
     * Update a resource
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data);


    /**
     * Delete a resource
     *
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model);

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc');
}