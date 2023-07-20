<?php

namespace App\Http\Controllers\Api;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Jchedev\Laravel\Classes\Pagination\ByOffsetLengthAwarePaginator;
use Jchedev\Laravel\Classes\Selectors\Selector;

class Controller extends \App\Http\Controllers\Controller
{
    const ALL_INCLUDES = 'all';

    const NO_INCLUDES = 'none';

    protected $defaultSort = null;

    protected $defaultFilters = [];

    protected $pageSize = 30;

    protected $pageSizeMax = 100;

    protected $responseModifiers = [];

    /**
     * Placeholder for logic that needs be executed BEFORE every method
     *
     * @param $method
     * @param $parameters
     */
    protected function before($method, $parameters)
    {
        // To be overwritten at a child level
    }

    /**
     * Placeholder for logic that needs be executed AFTER every method
     *
     * @param $method
     * @param $parameters
     * @param $response
     */
    protected function after($method, $parameters, $response)
    {
        // To be overwritten at a child level
    }

    /**
     * Add the concept of before () and after() as an alternative of a middleware
     *
     * @param $method
     * @param $parameters
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function callAction($method, $parameters)
    {
        $this->before($method, $parameters);

        $return = parent::callAction($method, $parameters);

        $this->after($method, $parameters, $return);

        return $return;
    }

    /**
     * Add ability to validate an array instead of a request
     *
     * @param mixed $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(mixed $request, array $rules, array $messages = [], array $customAttributes = []): array
    {
        // Why doesn't the method  accept a simple array instead of a request?
        if (is_array($request)) {
            return validator($request, $rules, $messages, $customAttributes)->validate();
        }

        // Otherwise, run the default/parent logic
        return parent::validate($request, $rules, $messages, $customAttributes);
    }

    /**
     * Return a collection resource
     *
     * @param $collection
     * @param $class
     * @param $includes
     * @return mixed
     */
    public function returnCollection($collection, $class, $includes = null)
    {
        // Check which includes (all, none, set list) should be applied to the response
        if (is_null($includes)) {
            $includes = $this->requestIncludes();
        } elseif ($includes == self::ALL_INCLUDES) {
            $includes = array_keys($this->getIncludesOptions());
        } elseif ($includes == self::NO_INCLUDES) {
            $includes = [];
        }

        // Apply includes to the collection. By default, only the "requested" ones are used
        $this->applyIncludes($collection, $includes);

        // Create a Response object (through Resource::collection)
        $response = $class::collection($collection);

        return $this->applyResponseModifiers($response);
    }

    /**
     * @param $model
     * @param $class
     * @param $includes
     * @return mixed
     */
    public function returnResource($model, $class, $includes = self::ALL_INCLUDES)
    {
        if ($includes == self::ALL_INCLUDES) {
            $includes = array_keys($this->getIncludesOptions());
        } elseif (is_null($includes) || $includes == self::NO_INCLUDES) {
            $includes = [];
        }

        $this->applyIncludes($model, $includes);

        $response = $class::make($model);

        return $this->applyResponseModifiers($response);
    }

    /**
     * Return a selector object for easy use by the controller
     *
     * @param mixed $builder
     * @return \Jchedev\Laravel\Classes\Selectors\Selector
     */
    public function selector(mixed $builder): Selector
    {
        $selector = new Selector($builder, $this->getFilteringOptions(), $this->getSortingOptions());

        $filters = array_merge($this->defaultFilters, $this->requestFilters());

        $selector->setFilters($filters);

        if (count($sort = $this->requestSorts()) === 0 && !is_null($this->defaultSort)) {
            $sort = (array)$this->defaultSort;
        }

        $selector->setSorts($sort);

        return $selector;
    }

    /**
     * Return ALL results from a selector object without pagination
     *
     * @param $builder
     * @return mixed
     */
    public function queryAll($builder)
    {
        return $this->selector($builder)->get();
    }

    /**
     * Paginate a selector results by offset. Need to use custom ByOffsetLengthAwarePaginator
     *
     * @param $builder
     * @return \Jchedev\Laravel\Classes\Pagination\ByOffsetLengthAwarePaginator
     */
    public function paginate($builder): ByOffsetLengthAwarePaginator
    {
        $selector = $this->selector($builder);

        if (!is_null($limit = $this->requestLimit())) {
            $selector->setLimit($limit);

            if (!is_null($offset = $this->requestOffset())) {
                $selector->setOffset($offset);
            }
        }

        return $selector->paginateByOffset();
    }

    /**
     * This is where we would define the list of includes available for a specific controller
     *
     * @return array
     */
    public function getFilteringOptions(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSortingOptions(): array
    {
        return [];
    }

    /**
     * This is where we would define the list of includes available for a specific controller
     *
     * @return array
     */
    public function getIncludesOptions(): array
    {
        return [];
    }

    /**
     * Return the filters passed to the request.
     * Filter values will try to decode value which are in JSON
     *
     * @return array
     */
    protected function requestFilters(): array
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
     * Return the sorting passed to the request.
     * Format should be ["key" => asc|desc]
     *
     * @return array|string[]
     */
    protected function requestSorts(): array
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
     * Return the requested limit (based on pageSize and pageSizeMax)
     *
     * @return int
     */
    protected function requestLimit(): int
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
     * Return the requested offset
     *
     * @return mixed
     */
    protected function requestOffset()
    {
        return request()->offset;
    }

    /**
     * Return the list of includes that we want to load on the response
     *
     * @return string[]
     */
    protected function requestIncludes(): array
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
     * Add a ResponseModifier (simple closure) to the queue to be added later
     *
     * @param \Closure $closure
     * @return $this
     */
    protected function addResponseModifier(\Closure $closure): static
    {
        $this->responseModifiers[] = $closure;

        return $this;
    }

    /**
     * Apply a set of includes (pre-defined callbacks) to the collection
     *
     * @param $on
     * @param array $includes
     */
    private function applyIncludes($on, array $includes)
    {
        $asCollection = $this->asCollection($on);

        foreach ($this->getIncludesOptions() as $key => $closure) {
            if (in_array($key, $includes)) {
                $closure($asCollection);
            }
        }
    }

    /**
     * Apply "queued" response modifiers to a response object
     *
     * @param $response
     * @return mixed
     */
    private function applyResponseModifiers($response): mixed
    {
        foreach ($this->responseModifiers as $modifier) {
            $modifier($response);
        }

        $this->responseModifiers = [];

        return $response;
    }

    /**
     * @param $on
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function asCollection($on): Collection
    {
        if ($on instanceof ByOffsetLengthAwarePaginator) {
            $on = $on->getCollection();
        } elseif ($on instanceof Model) {
            $on = $on->newCollection([$on]);
        } elseif (is_array($on)) {
            $on = new Collection($on);
        }

        return $on;
    }
}
