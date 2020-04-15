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

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function isCreated(): bool
    {
        return Response::HTTP_CREATED === $this->response->getStatusCode();
    }

    public function isUnprocessable(): bool
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY === $this->response->getStatusCode();
    }

    public function isOk(): bool
    {
        return $this->response->isOk();
    }

    public function isEmpty(): bool
    {
        return $this->response->isEmpty();
    }

    public function isUnauthorized(): bool
    {
        return Response::HTTP_UNAUTHORIZED === $this->response->getStatusCode();
    }

    public function isForbidden(): bool
    {
        return Response::HTTP_FORBIDDEN === $this->response->getStatusCode();
    }

    public function isBadRequest(): bool
    {
        return Response::HTTP_BAD_REQUEST === $this->response->getStatusCode();
    }

    public function isNotFound(): bool
    {
        return Response::HTTP_NOT_FOUND === $this->response->getStatusCode();
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

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(): ResponseHeaderBag
    {
        return $this->response->headers;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
