<?php

declare(strict_types=1);

namespace Nebkam\FluentTest\Test;

use Nebkam\FluentTest\ResponseWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ResponseWrapperTest extends TestCase
{
    public function testIsCreated()
    {
        $wrapper = new ResponseWrapper(new Response('', 201));
        $this->assertTrue($wrapper->isCreated());
    }
}
