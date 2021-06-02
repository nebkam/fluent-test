[![Latest Stable Version](https://poser.pugx.org/nebkam/fluent-test/v)](//packagist.org/packages/nebkam/fluent-test)

# Fluent Test Helper
Few classes to make your Symfony tests more readable

### Symfony 5
`composer require nebkam/fluent-test`
### Symfony 3 & 4
`composer require nebkam/fluent-test:"^2.0"`

## `RequestBuilder`
Since `Symfony\Bundle\FrameworkBundle\KernelBrowser::request` has 7 optional parameters, arbitrary ordered, this class follows a [builder pattern](https://en.wikipedia.org/wiki/Builder_pattern) to construct the request using semantic methods.

### Usage
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, $server, $content);

// After
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
Instead, you can use more specific methods (Thanks, @KristijanKanalas):
 - `setHeader`
 - `setHttpHeader`
 - `setCredentials`

(if you think of some other uses of server variables, feel free to write a semantic method for it in a PR)

#### setHeader
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, [
  'CONTENT_TYPE' => $value
  ], $content);

// After
$response = RequestBuilder::create($client)
  ->setHeader('CONTENT_TYPE', $value)
  ...
```

#### setHttpHeader
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, [
  'HTTP_X-Custom-Header' => $value
  ], $content);

// After
$response = RequestBuilder::create($client)
  ->setHttpHeader('X-Custom-Header', $value)
  ...
```

#### setCredentials
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, [
  'PHP_AUTH_USER' => $username,
  'PHP_AUTH_PW' => $password
  ], $content);

//After
$response = RequestBuilder::create($client)
  ->setCredentials($username, $password)
  ...
```

### `setJsonContent`
Send a JSON encoded payload with the request
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, $server, json_encode($content));

// After
$response = RequestBuilder::create($client)
  ->setJsonContent($content)
  ...
```

### Dynamic URIs
`setUri` accepts either a plain `string` or [sprintf](https://www.php.net/manual/en/function.sprintf.php) -compatible parameters (format and values)
```php
// This works
$response = RequestBuilder::create($client)
  ->setUri('/users/'. $email .'/details')
  ...

// This is more readable
$response = RequestBuilder::create($client)
  ->setUri('/users/%s/details', $email)
  ...
```

## `ResponseWrapper`
A [decorator](https://en.wikipedia.org/wiki/Decorator_pattern) for `Symfony\Component\HttpFoundation\Response` that wraps the response and provides few semantic _issers_ to make asserts more readable

### Usage
```php
// Before
$client->request($method, $uri, $parameters, $files, $server, $content);
$response = $client->getResponse();
$this->assertEquals(200,$response->getStatusCode())

// After
$response = RequestBuilder::create($client)
  ...
  ->getResponse();
$this->assertTrue($response->isOk());
```

### List of _issers_
- `isOk`
- `isCreated`
- `isEmpty`
- `isBadRequest`
- `isUnauthorized`
- `isForbidden`
- `isNotFound`
- `isUnprocessable`

### `getJsonContent`
Get a JSON decoded body from the response
```php
// Before
$response = $client->request($method, $uri, $parameters, $files, $server, $content);
$data = json_decode($client->getResponse());

// After
$data = RequestBuilder::create($client)
  ...
  ->getResponse()
  ->getJsonContent();
```
