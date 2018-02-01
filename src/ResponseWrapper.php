<?php

namespace Nebkam\FluentTest;

use Symfony\Component\HttpFoundation\Response;

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

	public function isOk(): bool
		{
		return $this->response->isOk();
		}

	public function isEmpty(): bool
		{
		return $this->response->isEmpty();
		}

	/**
	 * @return mixed
	 */
	public function getJsonContent()
		{
		return json_decode($this->response->getContent(), true);
		}
	}