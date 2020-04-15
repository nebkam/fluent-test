<?php

namespace Nebkam\FluentTest;

use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class RequestBuilder
{
    /**
     * @var KernelBrowser
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

    public static function create(?KernelBrowser $client = null): self
    {
        $instance = new self();
        if ($client) {
            $instance->setClient($client);
        }

        return $instance;
    }

    public function setClient(KernelBrowser $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getClient(): ?KernelBrowser
    {
        return $this->client;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param mixed ...$args
     *
     * @return $this
     */
    public function setUri(string $uri, ...$args): self
    {
        if ($args) {
            $this->uri = sprintf($uri, ...$args);
        } else {
            $this->uri = $uri;
        }

        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param mixed $content
     *
     * @return $this
     */
    public function setJsonContent($content): self
    {
        $this->content = json_encode($content);

        return $this;
    }

    public function setHttpHeader(string $key, string $value): self
    {
        $this->server = array_merge($this->server, [
            'HTTP_'.$key => $value,
        ]);

        return $this;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->server = array_merge($this->server, [
            $key => $value,
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

    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

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
     *
     * @return $this
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
     *
     * @return $this
     */
    public function setDefaultPassword($defaultPassword): self
    {
        $this->defaultPassword = $defaultPassword;

        return $this;
    }

    /**
     * Sets Basic Auth headers based on passed or default credentials *for one request only*.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return $this
     */
    public function useCredentialsOnce($username = null, $password = null): self
    {
        self::$CLEAR_CREDENTIALS_AFTER_REQUEST = true;

        return $this->setCredentials($username, $password);
    }

    /**
     * Sets Basic Auth headers based on passed or default credentials.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return $this
     */
    public function setCredentials($username = null, $password = null): self
    {
        // Since PHP can't handle expressions as default function arguments..
        $username = $username ?: $this->getDefaultUsername();
        $password = $password ?: $this->getDefaultPassword();

        if (null === $username) {
            throw new LogicException('Either provide username or set the default username');
        }
        if (null === $password) {
            throw new LogicException('Either provide password or set the default password');
        }

        $this->setHeader('PHP_AUTH_USER', $username);
        $this->setHeader('PHP_AUTH_PW', $password);

        return $this;
    }

    public function unsetCredentials(): self
    {
        if (isset($this->server['PHP_AUTH_USER'])) {
            unset($this->server['PHP_AUTH_USER']);
        }
        if (isset($this->server['PHP_AUTH_PW'])) {
            unset($this->server['PHP_AUTH_PW']);
        }

        return $this;
    }

    public function getResponse(): ResponseWrapper
    {
        if (empty($this->method)) {
            throw new \LogicException(sprintf('Cannot call %s::getResponse() without adding a method. Please use %s::setMethod("GET")', __CLASS__, __CLASS__));
        }
        if (empty($this->uri)) {
            throw new \LogicException(sprintf('Cannot call %s::getResponse() without adding a URI. Please use %s::setUri("/homepage")', __CLASS__, __CLASS__));
        }

        $this->client->request($this->method, $this->uri, $this->parameters, $this->files, $this->server, $this->content);

        $responseWrapper = new ResponseWrapper($this->client->getResponse());

        if (self::$CLEAR_CREDENTIALS_AFTER_REQUEST) {
            $this->unsetCredentials();
            self::$CLEAR_CREDENTIALS_AFTER_REQUEST = false; //back to default
        }

        return $responseWrapper;
    }
}
