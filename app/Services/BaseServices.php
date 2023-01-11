<?php

namespace App\Services;

class BaseServices {
    protected $model;

    /**
     * Constructor
     * 
     * @return Model
     */
    public function __construct()
    {
        $this->model = new $this->model();
    }

    /**
     * Get all records
     * 
     * @return Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get a record by id
     * 
     * @param int $id this is the id of the record to be retrieved
     * 
     * @return Model
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record
     * 
     * @param array $data this is the payload to be used to create the record
     * 
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     * 
     * @param int   $id   this is the id of the record to be updated
     * @param array $data this is the payload to be used to update the record
     * 
     * @return Model
     */
    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        return $record->update($data);
    }

    /**
     * Delete a record
     * 
     * @param int $id this is the id of the record to be deleted
     * 
     * @return bool
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Get a record by where clause
     * 
     * @param $key   this is the column of the record to be retrieved
     * @param $value this is the value of the record to be retrieved
     * 
     * @return Model
     */
    public function findByWhere($key, $value)
    {
        return $this->model->where($key, $value)->first();
    }
}