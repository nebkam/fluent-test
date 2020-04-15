<?php

namespace Nebkam\FluentTest;

use LogicException;
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
	 * @return self
	 */
	public static function create($client = null): self
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
	 * @return self
	 */
	public function setClient(Client $client): self
		{
		$this->client = $client;

		return $this;
		}

	/**
	 * @return Client|null
	 */
	public function getClient()
		{
		return $this->client;
		}

	/**
	 * @param string $method
	 * @return self
	 */
	public function setMethod(string $method): self
		{
		$this->method = $method;

		return $this;
		}

	/**
	 * @param string $uri
	 * @param array $args
	 * @return self
	 */
	public function setUri(string $uri, ...$args): self
		{
		if ($args)
			{
			$this->uri = sprintf($uri, ...$args);
			}
		else
			{
			$this->uri = $uri;
			}

		return $this;
		}

	/**
	 * @param string $content
	 * @return self
	 */
	public function setContent(string $content): self
		{
		$this->content = $content;

		return $this;
		}

	/**
	 * @param mixed $content
	 * @return self
	 */
	public function setJsonContent($content): self
		{
		$this->content = json_encode($content);

		return $this;
		}

	/**
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function setHttpHeader(string $key, string $value): self
		{
		$this->server = array_merge($this->server, [
			'HTTP_'.$key => $value
		]);

		return $this;
		}

	/**
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function setHeader(string $key, string $value): self
		{
		$this->server = array_merge($this->server, [
			$key => $value
		]);

		return $this;
		}

    /**
     * @return $this
     */
    public function unsetHeaders(): self
	    {
        $this->server = [];

        return $this;
        }

	/**
	 * @param array $files
	 * @return self
	 */
	public function setFiles(array $files): self
		{
		$this->files = $files;

		return $this;
		}

	/**
	 * @param array $parameters
	 * @return self
	 */
	public function setParameters(array $parameters): self
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
	 * @return self
	 */
	public function setDefaultUsername($defaultUsername): self
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
	 * @return self
	 */
	public function setDefaultPassword($defaultPassword): self
		{
		$this->defaultPassword = $defaultPassword;

		return $this;
		}

	/**
	 * Sets Basic Auth headers based on passed or default credentials *for one request only*
	 *
	 * @param null|string $username
	 * @param null|string $password
	 * @return self
	 */
	public function useCredentialsOnce($username = null, $password = null): self
		{
		self::$CLEAR_CREDENTIALS_AFTER_REQUEST = true;

		return $this->setCredentials($username,$password);
		}

	/**
	 * Sets Basic Auth headers based on passed or default credentials
	 *
	 * @param null|string $username
	 * @param null|string $password
	 * @return self
	 */
	public function setCredentials($username = null, $password = null): self
		{
		// Since PHP can't handle expressions as default function arguments..
		$username = $username ? $username : $this->getDefaultUsername();
		$password = $password ? $password : $this->getDefaultPassword();

		if ($username === null)
			{
			throw new LogicException('Either provide username or set the default username');
			}
		if ($password === null)
			{
			throw new LogicException('Either provide password or set the default password');
			}

		$this->setHeader('PHP_AUTH_USER', $username);
		$this->setHeader('PHP_AUTH_PW', $password);

		return $this;
		}

	/**
	 * @return self
	 */
	public function unsetCredentials(): self
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
