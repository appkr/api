<?php

namespace Appkr\Fractal\Http;

use Appkr\Fractal\Transformers\SimpleArrayTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Serializer\JsonApiSerializer;

class Response
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Laravel\Lumen\Http\ResponseFactory|\Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * @var integer Http status code
     */
    protected $statusCode = 200;

    /**
     * @var array Http response headers
     */
    protected $headers = [];

    /**
     * @var string List of includes
     */
    protected $includes;

    /**
     * @var array List of meta data to append to the response body
     */
    protected $meta = [];

    /**
     * Create new Response class.
     *
     * @param \League\Fractal\Manager             $fractal
     * @param \Illuminate\Http\Request            $request
     * @param \Appkr\Fractal\Http\ResponseFactory $response
     */
    public function __construct(Fractal $fractal, Request $request, ResponseFactory $response)
    {
        $this->fractal  = $fractal;
        $this->request  = $request;
        $this->response = $response->make();
    }

    /**
     * Generic response.
     *
     * @api
     * @param array|null $payload
     * @return \Illuminate\Contracts\Http\Response
     */
    public function respond($payload = [])
    {
        if ($meta = $this->getMeta()) {
            $payload = array_merge(
                $payload,
                ['meta' => $meta]
            );
        }

        return (! $callback = $this->request->input('callback'))
            ? $this->response->json(
                $payload,
                $this->getStatusCode(),
                $this->getHeaders()
            )
            : $this->response->jsonp(
                $callback,
                $payload,
                $this->getStatusCode(),
                $this->getHeaders()
            );
    }

    /* RESOURCE RESPONSES - Fractal transformed */

    /**
     * Respond collection of resources.
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param null                                     $transformer
     * @param string|null                              $resourceKey
     * @return \Illuminate\Contracts\Http\Response
     */
    public function withCollection(EloquentCollection $collection, $transformer = null, $resourceKey = null)
    {
        return $this->respond(
            $this->getCollection($collection, $transformer, $resourceKey)
        );
    }

    /**
     * Create FractalCollection payload.
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param null                                     $transformer
     * @param string|null                              $resourceKey
     * @return mixed
     */
    public function getCollection(EloquentCollection $collection, $transformer = null, $resourceKey = null)
    {
        $resource = new FractalCollection(
            $collection,
            $this->getTransformer($transformer),
            $this->getResourceKey($resourceKey)
        );

        if ($meta = $this->getMeta()) {
            $resource->setMeta($meta);
            $this->meta = [];
        }

        if ($includes = $this->getIncludes()) {
            $this->fractal->parseIncludes($this->request->input('include'));
            $this->includes = null;
        }

        return $this->fractal->createData($resource)->toArray();
    }

    /**
     * Respond single item.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param                                     $transformer
     * @param string|null                         $resourceKey
     * @return \Illuminate\Contracts\Http\Response
     */
    public function withItem(EloquentModel $model, $transformer = null, $resourceKey = null)
    {
        return $this->respond(
            $this->getItem($model, $transformer, $resourceKey)
        );
    }

    /**
     * Create FractalItem payload.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param null                                $transformer
     * @param string|null                         $resourceKey
     * @return mixed
     */
    public function getItem(EloquentModel $model, $transformer = null, $resourceKey = null)
    {
        $resource = new FractalItem(
            $model,
            $this->getTransformer($transformer),
            $this->getResourceKey($resourceKey)
        );

        if ($meta = $this->getMeta()) {
            $resource->setMeta($meta);
            $this->meta = [];
        }

        if ($includes = $this->getIncludes()) {
            $this->fractal->parseIncludes($this->request->input('include'));
            $this->includes = null;
        }

        return $this->fractal->createData($resource)->toArray();
    }

    /**
     * Respond collection of resources with pagination.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param                                                       $transformer
     * @param string|null                                           $resourceKey
     * @return \Illuminate\Contracts\Http\Response
     */
    public function withPagination(LengthAwarePaginator $paginator, $transformer = null, $resourceKey = null)
    {
        return $this->respond(
            $this->getPagination($paginator, $transformer, $resourceKey)
        );
    }

    /**
     * Create FractalCollection payload with pagination.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param null                                                  $transformer
     * @param string|null                                           $resourceKey
     * @return mixed
     */
    public function getPagination(LengthAwarePaginator $paginator, $transformer = null, $resourceKey = null)
    {
        // Append existing query parameter to pagination link
        // @see http://fractal.thephpleague.com/pagination/#including-existing-query-string-values-in-pagination-links
        $queryParams = array_diff_key($_GET, array_flip(['page']));

        foreach ($queryParams as $key => $value) {
            $paginator->addQuery($key, $value);
        }

        $collection = $paginator->getCollection();

        $resource = new FractalCollection(
            $collection,
            $this->getTransformer($transformer),
            $this->getResourceKey($resourceKey)
        );
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        if ($meta = $this->getMeta()) {
            $resource->setMeta($meta);
            $this->meta = [];
        }

        if ($includes = $this->getIncludes()) {
            $this->fractal->parseIncludes($this->request->input('include'));
            $this->includes = null;
        }

        return $this->fractal->createData($resource)->toArray();
    }

    /* SIMPLE RESPONSES - Simple json responses for exchange of simple messages */

    /**
     * Respond json formatted success message.
     *
     * @api
     * @param string $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function success($message = 'Success')
    {
        return $this->respond(
            $this->format($message, config('fractal.successFormat'))
        );
    }

    /**
     * Respond 201.
     *
     * @param mixed $primitive
     * @return $this
     */
    public function created($primitive = 'Created')
    {
        $payload = null;

        if ($primitive instanceof EloquentModel) {
            // If an Eloquent Model was passed as the $primitive argument,
            // it just defer the job to respondItem() method.
            // On receiving the job, respondItem() method does its best
            // to transform the given Elequent Model with SimpleArrayTransformer.
            return $this->setStatusCode(201)->withItem($primitive);
        }

        return $this->setStatusCode(201)->respond(
            $this->format($primitive, config('fractal.successFormat'))
        );
    }

    /**
     * Respond 204.
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function noContent()
    {
        return $this->setStatusCode(204)->respond(null);
    }

    /**
     * Generic error response.
     *
     * @api
     * @param mixed $message
     * @return $this
     */
    public function error($message = 'Unknown Error')
    {
        $format = config('fractal.errorFormat');

        if ($message instanceof \Exception) {
            if (env('APP_DEBUG')) {
                $format['debug'] = [
                    'line'  => $message->getLine(),
                    'file'  => $message->getFile(),
                    'class' => get_class($message),
                    'trace' => explode("\n", $message->getTraceAsString())
                ];
            }

            $this->statusCode = $this->translateExceptionCode($message);
            $message          = $message->getMessage();
        }

        return $this->respond(
            $this->format($message, $format)
        );
    }

    /**
     * Respond 400.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function badRequestError($message = 'Bad Request')
    {
        return $this->setStatusCode(400)->error($message);
    }

    /**
     * Respond 401.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function unauthorizedError($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->error($message);
    }

    /**
     * Respond 403.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function forbiddenError($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->error($message);
    }

    /**
     * Respond 404.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function notFoundError($message = 'Not Found')
    {
        return $this->setStatusCode(404)->error($message);
    }

    /**
     * Respond 406.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function notAcceptableError($message = 'Not Acceptable')
    {
        return $this->setStatusCode(406)->error($message);
    }

    /**
     * Respond 409.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function conflictError($message = 'Conflict')
    {
        return $this->setStatusCode(409)->error($message);
    }

    /**
     * Respond 422.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function unprocessableError($message = 'Unprocessable Entity')
    {
        return $this->setStatusCode(422)->error($message);
    }

    /**
     * Respond 500.
     *
     * @param mixed $message
     * @return \Illuminate\Contracts\Http\Response
     */
    public function internalError($message = 'Internal Server Error')
    {
        return $this->setStatusCode(500)->error($message);
    }

    /* Public getter and setters */

    /**
     * Getter for statusCode property.
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode property.
     *
     * @param mixed $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Getter for headers property.
     *
     * @return array
     */
    public function getHeaders()
    {
        $defaultHeaders = config('fractal.defaultHeaders');

        return $defaultHeaders
            ? array_merge($defaultHeaders, $this->headers)
            : $this->headers;
    }

    /**
     * Setter for headers property.
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        if ($headers) {
            $this->headers = array_merge($this->headers, $headers);
        }

        return $this;
    }

    /**
     * Setter for includes property.
     * This is a wrapper for Fractal::parseInclude()
     *
     * @since 0.3
     * @param array|string $includes Array or csv string of resources to include
     * @return $this
     */
    public function setIncludes($includes)
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * Getter for includes property.
     *
     * @return string
     */
    public function getIncludes()
    {
        if ($includes = $this->request->input('include')) {
            $this->setIncludes($includes);
        }

        return $this->includes;
    }

    /**
     * Getter for meta property.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Setter for meta property.
     *
     * @since 0.2
     * @param $meta
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get the singleton instance of Fractal Manager.
     *
     * @return \League\Fractal\Manager
     */
    public function getFractal()
    {
        return $this->fractal;
    }

    /**
     * Build response payload array based on configured format.
     *
     * @param mixed $message
     * @param array $format
     * @return array
     */
    public function format($message, array $format)
    {
        $replace = [
            ':message' => $message,
            ':code'    => $this->getStatusCode()
        ];

        array_walk_recursive($format, function (&$value, $key) use ($replace) {
            if (isset($replace[$value])) {
                $value = $replace[$value];
            }
        });

        return $format;
    }

    /* Protected and private methods for this class to be working */

    /**
     * Calculate transformer.
     * Replace transformer to SimpleArrayTransformer
     * if nothing/null is passed
     *
     * @param $transformer
     * @return \Appkr\Fractal\Transformers\SimpleArrayTransformer|mixed
     */
    private function getTransformer($transformer)
    {
        return $transformer ?: app(SimpleArrayTransformer::class);
    }

    /**
     * Calculate the resourceKey.
     * If configured serializer is not an instance of JsonApiSerializer
     * the resourceKey is useless and null will be returned
     *
     * @param string $resourceKey
     * @return string|null
     */
    private function getResourceKey($resourceKey)
    {
        return ($this->fractal->getSerializer() instanceof JsonApiSerializer)
            ? $resourceKey : null;
    }

    /**
     * Translate http status code based on the given exception.
     *
     * @param \Exception $e
     * @return int
     */
    private function translateExceptionCode($e)
    {
        if (! in_array($e->getCode(), [0, -1, null, ''])) {
            return $e->getCode();
        }

        if (method_exists($e, 'getStatusCode')) {
            if (! in_array($e->getStatusCode(), [0, -1, null, ''])) {
                return $e->getStatusCode();
            }

            if (($statusCode = $this->getStatusCode()) != 200) {
                return $statusCode;
            }
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
            or $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
        ) {
            return 404;
        }

        return 400;
    }

    /* Magic methods */

    /**
     * Dynamically call all other methods on the response object.
     *
     * @param string $method
     * @param array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->response, $method], $parameters);
    }
}
