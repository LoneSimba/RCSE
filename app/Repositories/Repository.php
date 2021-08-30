<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\Repository as RepositoryContract;

abstract class Repository implements RepositoryContract
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function makeModel(array $attr): Model
    {
        return $this->getModel()->newInstance($attr);
    }

    public function makeCollection($data)
    {
        return new Collection($data);
    }

    public function getPureObjectById(string $id): ?Model
    {
        return $this->getPureObjectBy('id', $id);
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Метод возвращает легкий объект для использования в связях
     *
     * @param string $column
     * @param string $value
     * @return Model|null
     */
    protected function getPureObjectBy(string $column, string $value): ?Model
    {
        return $this->getModel()->select('id')->where($column, $value)->first();
    }

    /**
     * Метод возращает модель по id, либо модель переданную в качестве аргумента
     *
     * @param $object
     * @return Model|null
     */
    protected function makeObject($object): ?Model
    {
        if ($this->isCorrectObject($object)) {
            return $object;
        }

        if (is_string($object)) {
            return $this->getPureObjectById($object);
        }

        return null;
    }

    protected function isCorrectObject($object): bool
    {
        return is_object($object) && is_a($object, get_class($this->getModel())) && $object->exists;
    }

    protected function makeExistingModel(array $attrs, bool $wasRecentlyCreated = false): Model
    {
        $model = $this->getModel()
                      ->newInstance([], true)
                      ->setRawAttributes($attrs, true);

        if ($wasRecentlyCreated) {
            $model->wasRecentlyCreated = true;
        }

        return $model;
    }

    /**
     * @param string $logPrefix
     * @param \Closure $callback
     * @param int $attempts
     * @return mixed|null
     */
    protected function transaction(string $logPrefix, \Closure $callback, int $attempts = 1)
    {
        try {
            return DB::transaction($callback, $attempts);
        } catch (\Exception $e) {
            Log::error($logPrefix . ': ' . $e->getMessage());
            return null;
        }
    }

}
