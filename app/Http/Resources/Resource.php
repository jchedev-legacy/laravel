<?php

namespace App\Http\Resources;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Http\Resources\MissingValue;

class Resource extends \Jchedev\Laravel\Http\Resources\Resource
{
    protected array|bool $withRelated = true;

    protected array|null $onlyAttributes = null;

    protected array $exceptAttributes = [];

    /**
     * @param $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable|string[]
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Model) {
            return $this->modelToArray(
                $this->resource,
                $this->modelAttributes(),
                $this->withRelated ? $this->modelRelated() : []
            );
        }

        return parent::toArray($request);
    }

    /**
     * @return array
     */
    protected function modelAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function modelRelated(): array
    {
        return [];
    }

    /**
     * @param $id
     * @param array $attributes
     * @param array $relations
     * @return array
     */
    public function modelToArray($id, array $attributes = [], array $relations = [])
    {
        if (is_subclass_of($id, Resource::class)) {
            $id = $id->resource;
        }

        if (is_subclass_of($id, Model::class)) {
            $id = $id->getRouteKey();
        }

        $finalAttributes = [];

        foreach ($attributes as $key => $value) {
            if (!in_array($key, $this->exceptAttributes) && (is_null($this->onlyAttributes) || in_array($key, $this->onlyAttributes))) {
                $finalAttributes[$key] = ($value instanceof \Closure) ? $value() : $value;
            }
        }

        $finalRelations = [];

        if ($this->withRelated) {
            foreach ($relations as $key => $relation) {
                if (!is_a($relation, MissingValue::class) && (!is_array($this->withRelated) || in_array($key, $this->withRelated))) {
                    $finalRelations[$key] = ['data' => ($relation instanceof \Closure) ? $relation() : $relation];
                }
            }
        }

        return [
            'id'         => (string)$id,
            'attributes' => $finalAttributes,
            'related'    => $finalRelations
        ];
    }

    /**
     * @param $keys
     * @param $includeRelated
     * @return $this
     */
    public function only($keys, $includeRelated = false)
    {
        $this->onlyAttributes = !is_array($keys) ? [$keys] : $keys;

        $this->withRelated = $includeRelated;

        return $this;
    }

    /**
     * @param $keys
     * @return $this
     */
    public function except($keys)
    {
        $this->exceptAttributes = array_merge($this->exceptAttributes, (array)$keys);

        return $this;
    }

    /**
     * @return $this
     */
    public function withRelated($value = true)
    {
        $this->withRelated = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutRelated()
    {
        $this->withRelated = false;

        return $this;
    }

    /**
     * @param \Carbon\Carbon|null $date
     * @return string|null
     */
    protected function dateFormat(Carbon $date = null)
    {
        return !is_null($date) ? $date->format('Y-m-d') : null;
    }

    /**
     * @param \Carbon\Carbon|null $date
     * @return string|null
     */
    protected function datetimeFormat(Carbon $date = null)
    {
        return !is_null($date) ? $date->toIso8601String() : null;
    }
}
