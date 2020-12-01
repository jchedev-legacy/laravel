<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;

class Resource extends \Jchedev\Laravel\Http\Resources\Resource
{
    /**
     * @param $id
     * @param array $attributes
     * @param array $relations
     * @return array
     */
    public function modelToArray($id, array $attributes = [], array $relations = [])
    {
        if (is_subclass_of($id, Model::class)) {
            $id = $id->getRouteKey();
        } elseif (is_subclass_of($id, Resource::class)) {
            $id = $id->resource->getRouteKey();
        }

        $relationsFiltered = array_filter($relations, function ($value) {
            return !is_a($value, MissingValue::class);
        });

        return [
            'id'         => (string)$id,
            'attributes' => $attributes,
            'related'    => array_map(function ($related) {
                return $this->relatedToArray($related);
            }, $relationsFiltered),
        ];
    }

    /**
     * @param $data
     * @return array
     */
    public function relatedToArray($data)
    {
        return [
            'data' => is_callable($data) ? $data() : $data
        ];
    }

    /**
     * @param \Carbon\Carbon $date
     * @return string
     */
    protected function dateFormat(Carbon $date)
    {
        return $date->format('Y-m-d');
    }

    /**
     * @param \Carbon\Carbon $date
     * @return string
     */
    protected function datetimeFormat(Carbon $date = null)
    {
        return !is_null($date) ? $date->toIso8601String() : null;
    }
}