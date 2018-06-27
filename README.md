# Fluent Test Helper
Few classes to make your Symfony tests more readable

## `RequestBuilder`
Since `Symfony\Bundle\FrameworkBundle\Client::request` has 7 optional parameters, arbitrary ordered, this class follows a [builder pattern](https://en.wikipedia.org/wiki/Builder_pattern) to construct the request using semantic methods:

### Usage
#### Before
```
$response = $client->request($method, $uri, $parameters, $files, $server, $content);
```

#### After
```php
$response = RequestBuilder::create($client)
  ->setMethod($method)
  ->setUri($uri)
  ->setParameters($parameters)
  ->setFiles($files)
  ->setContent($content)
  ->getResponse();
```
### What about $server parameter?
There's no `RequestBuilder::setServer` method, since it seemed to general to be semantic.
Instead, you can use more specific methods:
 - `::setHttpHeader`
 - `::setHeader`
 - `::setCredentials`

(if you think of some other uses of server variables, feel free to write a semantic method for it in a PR)

#### Before
```php
$response = $client->request($method, $uri, $parameters, $files, [
  'HTTP_X-Custom-Header' => $value
  ], $content);
```
#### After
```php
$response = RequestBuilder::create($client)
  ->setHttpHeader('X-Custom-Header', $value)
  ...
```

#### Before
```php
$response = $client->request($method, $uri, $parameters, $files, [
  'CONTENT_TYPE' => $value
  ], $content);
```
#### After
```php
$response = RequestBuilder::create($client)
  ->setHeader('CONTENT_TYPE', $value)
  ...
```

#### Before
```php
$response = $client->request($method, $uri, $parameters, $files, [
  'PHP_AUTH_USER' => $username,
  'PHP_AUTH_PW' => $password
  ], $content);
```
#### After
```php
$response = RequestBuilder::create($client)
  ->setCredentials($username, $password)
  ...
```

### JSON payload in the request
#### Before
```php
$response = $client->request($method, $uri, $parameters, $files, $server, json_encode($content));
```
#### After
```php
$response = RequestBuilder::create($client)
  ->setJsonContent($content)
  ...
```

## `ResponseWrapper`
A [decorator](https://en.wikipedia.org/wiki/Decorator_pattern) for `Symfony\Component\HttpFoundation\Response` that adds wraps the response and has semantic asserts

### Usage
#### Before
```php
$client->request($method, $uri, $parameters, $files, $server, $content);
$response = $client->getResponse();
$this->assertEquals(200,$response->getStatusCode())
```
#### After
```php
$response = RequestBuilder::create($client)
  ...
  ->getResponse();
$this->assertTrue($response->isOk());
```

### Other semantic asserts
```php
$this->assertTrue($response->isCreated());
$this->assertTrue($response->isEmpty());
$this->assertTrue($response->isBadRequest());
$this->assertTrue($response->isUnauthorized());
$this->assertTrue($response->isForbidden());
$this->assertTrue($response->isNotFound());
$this->assertTrue($response->isUnprocessable());
```

### JSON payload in the response
#### Before
```php
$response = $client->request($method, $uri, $parameters, $files, $server, $content);
$data = json_decode($client->getResponse());
```

#### After
```php
$data = RequestBuilder::create($client)
  ...
  ->getResponse()
  ->getJsonContent();
```
