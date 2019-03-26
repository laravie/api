<?php

namespace Dingo\Api\Tests\Auth\Provider;

use Mockery as m;
use Dingo\Api\Routing\Route;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Dingo\Api\Tests\Stubs\AuthorizationProviderStub;

class AuthorizationTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testExceptionThrownWhenAuthorizationHeaderIsInvalid()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\BadRequestHttpException');

        $request = Request::create('GET', '/');

        (new AuthorizationProviderStub)->authenticate($request, m::mock(Route::class));
    }
}
