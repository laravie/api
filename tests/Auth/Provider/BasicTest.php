<?php

namespace Dingo\Api\Tests\Auth\Provider;

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Dingo\Api\Auth\Provider\Basic;

class BasicTest extends TestCase
{
    protected $auth;
    protected $provider;

    protected function setUp(): void
    {
        $this->auth = m::mock('Illuminate\Auth\AuthManager');
        $this->provider = new Basic($this->auth);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testInvalidBasicCredentialsThrowsException()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException');

        $request = Request::create('GET', '/', [], [], [], ['HTTP_AUTHORIZATION' => 'Basic 12345']);

        $this->auth->shouldReceive('onceBasic')->once()->with('email')->andReturn(new Response('', 401));

        $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class));
    }

    public function testValidCredentialsReturnsUser()
    {
        $request = Request::create('GET', '/', [], [], [], ['HTTP_AUTHORIZATION' => 'Basic 12345']);

        $this->auth->shouldReceive('onceBasic')->once()->with('email')->andReturn(null);
        $this->auth->shouldReceive('user')->once()->andReturn('foo');

        $this->assertSame('foo', $this->provider->authenticate($request, m::mock(\Dingo\Api\Routing\Route::class)));
    }
}
