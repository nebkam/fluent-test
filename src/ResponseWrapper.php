<?php

namespace Nebkam\FluentTest;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ResponseWrapper
	{
	/**
	 * @var Response
	 */
	private $response;

	/**
	 * ResponseWrapper constructor.
	 * @param Response $response
	 */
	public function __construct(Response $response)
		{
		$this->response = $response;
		}

	/**
	 * @return bool
	 */
	public function isCreated(): bool
		{
		return $this->response->getStatusCode() === Response::HTTP_CREATED;
		}

	/**
	 * @return bool
	 */
	public function isUnprocessable(): bool
		{
		return $this->response->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY;
		}

	/**
	 * @return bool
	 */
	public function isOk(): bool
		{
		return $this->response->isOk();
		}

	/**
	 * @return bool
	 */
	public function isEmpty(): bool
		{
		return $this->response->isEmpty();
		}

    /**
     * @return bool
     */
    public function isUnauthorized(): bool
        {
        return $this->response->getStatusCode() === Response::HTTP_UNAUTHORIZED;
        }

    /**
     * @return bool
     */
    public function isForbidden(): bool
        {
        return $this->response->getStatusCode() === Response::HTTP_FORBIDDEN;
        }

    /**
     * @return bool
     */
    public function isBadRequest(): bool
        {
        return $this->response->getStatusCode() === Response::HTTP_BAD_REQUEST;
        }

    /**
     * @return bool
     */
    public function isNotFound(): bool
        {
        return $this->response->getStatusCode() === Response::HTTP_NOT_FOUND;
        }

	/**
	 * @return mixed
	 */
	public function getJsonContent()
		{
		return json_decode($this->response->getContent(), true);
		}

    /**
     * @return string
     */
    public function getRawContent()
        {
        return $this->response->getContent();
        }

    /**
     * @return int
     */
    public function getStatusCode(): int
        {
        return $this->response->getStatusCode();
        }

	/**
	 * @return ResponseHeaderBag
	 */
	public function getHeaders(): ResponseHeaderBag
		{
		return $this->response->headers;
		}
	}