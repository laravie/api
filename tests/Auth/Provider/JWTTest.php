<?php

namespace Dingo\Api\Tests\Auth\Provider;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Dingo\Api\Auth\Provider\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTTest extends TestCase
{
    protected $auth;
    protected $provider;

    protected function setUp(): void
    {
        if (! class_exists('Tymon\JWTAuth\JWTAuth')) {
            $this->markTestIncomplete('Missing tymon/jwt-auth dependency.');
        }

        $this->auth = m::mock('Tymon\JWTAuth\JWTAuth');
        $this->provider = new JWT($this->auth);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testValidatingAuthorizationHeaderFailsAndThrowsException()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\BadRequestHttpException');

        $request = Request::create('foo', 'GET');

        $this->auth->shouldReceive('parseToken')->andReturnSelf();
        $this->auth->shouldReceive('authenticate')->andReturnNull();

        $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class));
    }

    public function testAuthenticatingFailsAndThrowsException()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException');

        $request = Request::create('foo', 'GET');
        $request->headers->set('authorization', 'Bearer foo');

        $this->auth->shouldReceive('parseToken')->andReturnSelf();
        $this->auth->shouldReceive('authenticate')->once()->andThrow(new JWTException('foo'));

        $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class));
    }

    public function testAuthenticatingSucceedsAndReturnsUserObject()
    {
        $request = Request::create('foo', 'GET');
        $request->headers->set('authorization', 'Bearer foo');

        $this->auth->shouldReceive('parseToken')->andReturnSelf();
        $this->auth->shouldReceive('authenticate')->once()->andReturn((object) ['id' => 1]);

        $this->assertSame(1, $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class))->id);
    }

    public function testAuthenticatingWithQueryStringSucceedsAndReturnsUserObject()
    {
        $request = Request::create('foo', 'GET', ['token' => 'foo']);

        $this->auth->shouldReceive('parseToken')->andReturnSelf();
        $this->auth->shouldReceive('authenticate')->once()->andReturn((object) ['id' => 1]);

        $this->assertSame(1, $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class))->id);
    }
}
