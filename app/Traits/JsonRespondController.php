<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

trait JsonRespondController
{
  /**
   * @var int
   */
  protected $httpStatusCode = 200;

  /**
   * @var int
   */
  protected $errorCode;

  /**
   * @var string
   */
  protected $resource;

  /**
   * Get HTTP status code of the response.
   *
   * @return int
   */
  public function getHTTPStatusCode()
  {
    return $this->httpStatusCode;
  }

  /**
   * Set HTTP status code of the response.
   *
   * @param int $statusCode
   * @return self
   */
  public function setHTTPStatusCode($statusCode)
  {
    $this->httpStatusCode = $statusCode;

    return $this;
  }

  /**
   * Get error code of the response.
   *
   * @return int
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }

  /**
   * Set error code of the response.
   *
   * @param int $errorCode
   * @return self
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;

    return $this;
  }

  /**
   * Get name of resource
   *
   * @return int
   */
  public function getResource()
  {
    return $this->resource;
  }

  /**
   * Set resource name.
   *
   * @param string $resource
   * @return self
   */
  public function setResource($resource)
  {
    $this->resource = $resource;

    return $this;
  }

  /**
   * Sends a success JSON to the consumer.
   *
   * @param  array $data
   * @param  string $message
   * @param  array  $headers [description]
   * @return JsonResponse
   */
  public function successResponse($data, $message = '', $headers = [])
  {
    $response = [
      'success' => true,
      'data'    => $data,
      'message' => $message,
    ];
    return response()->json($response, $this->getHTTPStatusCode(), $headers);
  }

  /**
   * Sends a error JSON to the consumer.
   *
   * @param  array $data
   * @param  string $message
   * @param  array  $headers [description]
   * @return JsonResponse
   */
  public function errorResponse($message = '', $code =  null, $headers = [])
  {
    $response = [
      'success' => false,
      'message' => $message,
    ];
    return response()->json($response, $code ? $code : $this->getHTTPStatusCode(), $headers);
  }

  /**
   * Sends a response not found (404) to the request.
   * Error Code = 31.
   *
   * @return JsonResponse
   */
  public function respondNotFound($message = '')
  {
    $this->setHTTPStatusCode(404);
    return $this->errorResponse($message ? $message : __('response.notfound', ['resource' => $this->getResource()]));
  }

  /**
   * Sends an error when the validator failed.
   * Error Code = 32.
   *
   * @param Validator $validator
   * @return JsonResponse
   */
  public function validatorFailedResponse(Validator $validator)
  {
    return $this->setHTTPStatusCode(400)
      ->errorResponse($validator->errors()->first());
  }

  /**
   * Sends an error when the query didn't have the right parameters for
   * creating an object.
   * Error Code = 33.
   *
   * @param string $message
   * @return JsonResponse
   */
  public function respondNotTheRightParameters($message = null)
  {
    return $this->setHTTPStatusCode(500)
      ->setErrorCode(33)
      ->respondWithError($message);
  }

  /**
   * Sends a response invalid query (http 500) to the request.
   * Error Code = 40.
   *
   * @param string $message
   * @return JsonResponse
   */
  public function respondInvalidQuery($message = null)
  {
    return $this->setHTTPStatusCode(500)
      ->setErrorCode(40)
      ->respondWithError($message);
  }

  /**
   * Sends an error when the query contains invalid parameters.
   * Error Code = 41.
   *
   * @param string $message
   * @return JsonResponse
   */
  public function respondInvalidParameters($message = null)
  {
    return $this->setHTTPStatusCode(422)
      ->setErrorCode(41)
      ->respondWithError($message);
  }

  /**
   * Sends a response unauthorized (401) to the request.
   * Error Code = 42.
   *
   * @param string $message
   * @return JsonResponse
   */
  public function unauthorizedResponse($message = null)
  {
    return $this->setHTTPStatusCode(401)->errorResponse($message ? $message : __('auth.failed'));
  }

  /**
   * Sends a response with error.
   *
   * @param string|array $message
   * @return JsonResponse
   */
  public function respondWithError($message = null)
  {
    return $this->setHTTPStatusCode(500)->errorResponse($message ? $message : 'Something went wrong');
    // return $this->respond([
    //   'error' => [
    //     'message' => $message ?? config('api.error_codes.' . $this->getErrorCode()),
    //     'error_code' => $this->getErrorCode(),
    //   ],
    // ]);
  }

  /**
   * Sends a response that the object has been deleted, and also indicates
   * the id of the object that has been deleted.
   *
   * @param  int $id
   * @return JsonResponse
   */
  public function respondObjectDeleted($id)
  {
    return $this->respond([
      'deleted' => true,
      'id' => $id,
    ]);
  }
}
