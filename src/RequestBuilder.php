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
	 * @var array
	 */
	private $files = [];

	/**
	 * @var array
	 */
	private $parameters = [];

	/**
	 * @var string
	 */
	private $defaultUsername;

	/**
	 * @var string
	 */
	private $defaultPassword;

	/**
	 * @var bool
	 */
	private static $CLEAR_CREDENTIALS_AFTER_REQUEST = false;

	/**
	 * @param Client|null $client
	 * @return RequestBuilder
	 */
	public static function create($client = null)
		{
		$instance = new self();
		if ($client)
			{
			$instance->setClient($client);
			}

		return $instance;
		}

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
	 * @return Client
	 */
	public function getClient()
		{
		return $this->client;
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
	 * @param string $key
	 * @param string $value
	 * @return RequestBuilder
	 */
	public function setHeader(string $key, string $value): RequestBuilder
		{
		$this->server = array_merge($this->server, [
			'HTTP_'.$key => $value
		]);

		return $this;
		}

    /**
     * @return $this
     */
    public function unsetHeaders()
        {
        $this->server = [];

        return $this;
        }

	/**
	 * @param array $files
	 * @return RequestBuilder
	 */
	public function setFiles(array $files): RequestBuilder
		{
		$this->files = $files;

		return $this;
		}

	/**
	 * @param array $parameters
	 * @return RequestBuilder
	 */
	public function setParameters(array $parameters): RequestBuilder
		{
		$this->parameters = $parameters;

		return $this;
		}

	/**
	 * @return string|null
	 */
	public function getDefaultUsername()
		{
		return $this->defaultUsername;
		}

	/**
	 * @param string|null $defaultUsername
	 * @return RequestBuilder
	 */
	public function setDefaultUsername($defaultUsername): RequestBuilder
		{
		$this->defaultUsername = $defaultUsername;

		return $this;
		}

	/**
	 * @return string|null
	 */
	public function getDefaultPassword()
		{
		return $this->defaultPassword;
		}

	/**
	 * @param string|null $defaultPassword
	 * @return RequestBuilder
	 */
	public function setDefaultPassword($defaultPassword): RequestBuilder
		{
		$this->defaultPassword = $defaultPassword;

		return $this;
		}

	/**
	 * Sets Basic Auth headers based on passed or default credentials *for one request only*
	 *
	 * @param null|string $username
	 * @param null|string $password
	 * @return RequestBuilder
	 */
	public function useCredentialsOnce($username = null, $password = null): RequestBuilder
		{
		self::$CLEAR_CREDENTIALS_AFTER_REQUEST = true;

		return $this->setCredentials($username,$password);
		}

	/**
	 * Sets Basic Auth headers based on passed or default credentials
	 *
	 * @param null|string $username
	 * @param null|string $password
	 * @return RequestBuilder
	 */
	public function setCredentials($username = null, $password = null): RequestBuilder
		{
		// Since PHP can't handle expressions as default function arguments..
		$username = $username ? $username : $this->getDefaultUsername();
		$password = $password ? $password : $this->getDefaultPassword();

		if ($username === null)
			{
			throw new \LogicException('Either provide username or set the default username');
			}
		if ($password === null)
			{
			throw new \LogicException('Either provide password or set the default password');
			}

		$this->server = array_merge($this->server, [
			'PHP_AUTH_USER' => $username,
			'PHP_AUTH_PW'   => $password
		]);

		return $this;
		}

	/**
	 * @return RequestBuilder
	 */
	public function unsetCredentials(): RequestBuilder
		{
		if (isset($this->server['PHP_AUTH_USER']))
			{
			unset($this->server['PHP_AUTH_USER']);
			}
		if (isset($this->server['PHP_AUTH_PW']))
			{
			unset($this->server['PHP_AUTH_PW']);
			}

		return $this;
		}


	/**
	 * @deprecated Ambiguous. Use more semantic setCredentials or useCredentialsOnce
	 *
	 * Sets Basic Auth headers based on passed or default credentials
	 *
	 * @param null|string $username
	 * @param null|string $password
	 * @return RequestBuilder
	 */
	public function sendWithCredentials($username = null, $password = null): RequestBuilder
		{
		return $this->setCredentials($username,$password);
		}

	/**
	 * @deprecated Use sendWithCredentials
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
		$this->client->request($this->method, $this->uri, $this->parameters, $this->files, $this->server, $this->content);

		$responseWrapper = new ResponseWrapper($this->client->getResponse());

		if (self::$CLEAR_CREDENTIALS_AFTER_REQUEST)
			{
			$this->unsetCredentials();
			self::$CLEAR_CREDENTIALS_AFTER_REQUEST = false; //back to default
			}

		return $responseWrapper;
		}
	}