<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;

class Resource extends \Jchedev\Laravel\Http\Resources\Resource
{
    protected $withRelated = true;

    protected $onlyAttributes = null;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Model) {
            return $this->modelToArray($this->resource, $this->modelAttributes(), $this->withRelated ? $this->modelRelated() : []);
        }

        return parent::toArray($request);
    }

    /**
     * @return array
     */
    protected function modelAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function modelRelated()
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
            $type = get_class_basename($id);
            $id = $id->getRouteKey();
        }

        $finalAttributes = [];

        foreach ($attributes as $key => $value) {
            if (is_null($this->onlyAttributes) || in_array($key, $this->onlyAttributes)) {
                $finalAttributes[$key] = ($value instanceof \Closure) ? $value() : $value;
            }
        }

        $finalRelations = [];

        if ($this->withRelated) {
            foreach ($relations as $key => $relation) {
                if (!is_a($relation, MissingValue::class)) {
                    $finalRelations[$key] = [
                        'data' => is_callable($relation) ? $relation() : $relation
                    ];
                }
            }
        }

        return [
            'id'         => (string)$id,
            'type'       => isset($type) ? Str::snake($type) : null,
            'attributes' => $finalAttributes,
            'related'    => $finalRelations
        ];
    }

    /**
     * @param $keys
     * @param bool $includeRelated
     * @return $this
     */
    public function only($keys, $includeRelated = false)
    {
        $this->onlyAttributes = !is_array($keys) ? [$keys] : $keys;

        $this->withRelated = $includeRelated;

        return $this;
    }

    /**
     * @return $this
     */
    public function withRelated()
    {
        $this->withRelated = true;

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
     * @param \Carbon\Carbon $date
     * @return string
     */
    protected function dateFormat(Carbon $date = null)
    {
        return !is_null($date) ? $date->format('Y-m-d') : null;
    }

    /**
     * @param \Carbon\Carbon $date
     * @return string
     */
    protected function datetimeFormat(Carbon $date = null)
    {
        return !is_null($date) ? $date->toIso8601String() : null;
    }

    /**
     * @param $key
     * @return \Closure
     */
    protected function count($key)
    {
        return function () use ($key) {
            return $this->resource->count($key);
        };
    }
}