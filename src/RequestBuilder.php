<?php

namespace Nebkam\FluentTest;

use Symfony\Bundle\FrameworkBundle\Client;

class RequestBuilder
	{
	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var array
	 */
	private $server = [];

	/**
	 * @param Client $client
	 * @return RequestBuilder
	 */
	public function setClient(Client $client): RequestBuilder
		{
		$this->client = $client;

		return $this;
		}

	/**
	 * @param string $method
	 * @return RequestBuilder
	 */
	public function setMethod(string $method): RequestBuilder
		{
		$this->method = $method;

		return $this;
		}

	/**
	 * @param string $uri
	 * @return RequestBuilder
	 */
	public function setUri(string $uri): RequestBuilder
		{
		$this->uri = $uri;

		return $this;
		}

	/**
	 * @param string $content
	 * @return RequestBuilder
	 */
	public function setContent(string $content): RequestBuilder
		{
		$this->content = $content;

		return $this;
		}

	/**
	 * @param mixed $content
	 * @return RequestBuilder
	 */
	public function setJsonContent($content): RequestBuilder
		{
		$this->content = json_encode($content);

		return $this;
		}

	/**
	 * @return RequestBuilder
	 */
	public function sendAsAdmin(): RequestBuilder
		{
		$this->server = array_merge($this->server, [
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => $this->client->getContainer()->getParameter('test_admin_pass')
		]);

		return $this;
		}

	/**
	 * @return ResponseWrapper
	 */
	public function getResponse(): ResponseWrapper
		{
		$this->client->request($this->method, $this->uri, [], [], $this->server, $this->content);

		return new ResponseWrapper($this->client->getResponse());
		}

	/**
	 * @return RequestBuilder
	 */
	public static function create()
		{
		return new self();
		}
	}