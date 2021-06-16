<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Jchedev\Laravel\Classes\Pagination\ByOffsetLengthAwarePaginator;
use Jchedev\Laravel\Classes\Selectors\Selector;

class Controller extends BaseController
{
    const ALL_INCLUDES = 'all';

    const NO_INCLUDES = 'none';

    protected $pageSize = 25;

    protected $pageSizeMax = 100;

    protected $responseModifiers = [];

    /**
     * This is a placeholder method that should be overwritten at a child level to add logic that needs be executed BEFORE every method
     *
     * @param $method
     * @param $parameters
     */
    protected function before($method, $parameters)
    {
    }

    /**
     * This is a placeholder method that should be overwritten at a child level to add logic that needs be executed AFTER every method
     *
     * @param $method
     * @param $parameters
     * @param $response
     */
    protected function after($method, $parameters, $response)
    {
    }

    /**
     * Overwrite the Laravel method to allow the usage of before() and after()
     *
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $this->before($method, $parameters);

        $return = parent::callAction($method, $parameters);

        $this->after($method, $parameters, $return);

        return $return;
    }

    /**
     * Small update from the default validate() method to accept an array of $data instead of a request object only
     *
     * @param \Illuminate\Http\Request|array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($data, array $rules, array $messages = [], array $customAttributes = [])
    {
        // Why does the method doesnt accept a simple array instead of a request?
        if (is_array($data)) {
            return Validator::make($data, $rules, $messages, $customAttributes)->validate();
        }

        return parent::validate($data, $rules, $messages, $customAttributes);
    }

    /**
     * @param $collection
     * @param $class
     * @param array|null|string $includes
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function collection($collection, $class, $includes = null)
    {
        if (is_null($includes)) {
            $includes = $this->requestIncludes();
        } elseif ($includes == self::ALL_INCLUDES) {
            $includes = $this->getIncludesOptionsKeys();
        } elseif ($includes == self::NO_INCLUDES) {
            $includes = [];
        }

        $this->applyIncludes($collection, $includes);

        /**
         * @var \Illuminate\Http\Resources\Json\JsonResource $class
         */

        return $this->modifyResponse($class::collection($collection));
    }

    /**
     * @param $resource
     * @param $class
     * @param string $includes
     * @return mixed
     */
    public function resource($resource, $class, $includes = self::ALL_INCLUDES)
    {
        if ($includes == self::ALL_INCLUDES) {
            $includes = $this->getIncludesOptionsKeys();
        } elseif (is_null($includes) || $includes == self::NO_INCLUDES) {
            $includes = [];
        }

        $this->applyIncludes($resource, $includes);

        /**
         * @var \Illuminate\Http\Resources\Json\JsonResource $class
         */
        return $this->modifyResponse($class::make($resource));
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function modifyResponse($response)
    {
        foreach ($this->responseModifiers as $modifier) {
            $modifier($response);
        }

        $this->responseModifiers = [];

        return $response;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    protected function addResponseModifier(\Closure $closure)
    {
        $this->responseModifiers[] = $closure;

        return $this;
    }

    /**
     * Create a configured Selector object based on the parameters
     *
     * @param $builder
     * @return \Jchedev\Laravel\Classes\Selectors\Selector
     */
    protected function selector($builder): Selector
    {
        $selector = new Selector($builder, $this->getFilteringOptions(), $this->getSortingOptions());

        $selector->setFilters($this->requestFilters());

        $selector->setSorts($this->requestSorts());

        if (!is_null($limit = $this->requestLimit())) {
            $selector->setLimit($limit);

            if (!is_null($offset = $this->requestOffset())) {
                $selector->setOffset($offset);
            }
        }

        return $selector;
    }

    /**
     * Return the filters passed to the request.
     * Filters values will try to decode value which are in JSON
     *
     * @return array
     */
    protected function requestFilters()
    {
        if (!is_null($filters = request()->filters) && (is_array($filters) || is_array($filters = json_decode($filters, true)))) {
            foreach ($filters as $key => $value) {
                if (empty($value) && $value !== false) {
                    unset ($filters[$key]);
                }
            }

            return $filters;
        }

        return [];
    }

    /**
     * @return array|mixed
     */
    protected function requestSorts()
    {
        if (!is_null($sort = request()->sort)) {
            $sortCopy = $sort;

            if (is_array($sort) || is_array($sort = @json_decode($sort, true))) {
                return $sort;
            }

            return [$sortCopy => 'asc'];
        }

        return [];
    }

    /**
     * @return int|mixed
     */
    protected function requestLimit()
    {
        $pageSize = $this->pageSize;

        if (!is_null($limit = request()->limit)) {
            if ($limit == 'max') {
                $pageSize = $this->pageSizeMax;
            } elseif ($limit > 0) {
                if (is_null($this->pageSizeMax) || $limit <= $this->pageSizeMax) {
                    $pageSize = $limit;
                } else {
                    $pageSize = $this->pageSizeMax;
                }
            }
        }

        return $pageSize;
    }

    /**
     * @return mixed
     */
    protected function requestOffset()
    {
        return request()->offset;
    }

    /**
     * Return the list of includes that are requested for this endpoint
     *
     * @return array|mixed
     */
    protected function requestIncludes()
    {
        if (!is_null($includes = request()->includes)) {
            if (!is_array($includes)) {
                $includes = explode(',', $includes);
            }

            return $includes;
        }

        return [];
    }

    /**
     * This is where we would define the list of includes available for a specific controller
     *
     * @return array
     */
    protected function getIncludesOptions(): array
    {
        return [];
    }

    /**
     * Return all the Includes options keys available
     *
     * @return array
     */
    protected function getIncludesOptionsKeys(): array
    {
        return array_keys($this->getIncludesOptions());
    }

    /**
     * @param $on
     * @param array $includes
     */
    protected function applyIncludes($on, array $includes)
    {
        if ($on instanceof ByOffsetLengthAwarePaginator) {
            $on = $on->getCollection();
        }

        if (!is_a($on, Collection::class)) {
            if ($on instanceof Model) {
                $on = $on->newCollection([$on]);
            } else {
                $on = new Collection([$on]);
            }
        }

        foreach ($this->getIncludesOptions() as $key => $closure) {
            if (in_array($key, $includes)) {
                $closure($on);
            }
        }
    }

    /**
     * @return array
     */
    protected function getFilteringOptions(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getSortingOptions(): array
    {
        return [];
    }
}