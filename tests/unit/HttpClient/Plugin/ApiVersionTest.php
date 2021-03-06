<?php

declare(strict_types=1);

namespace Gitlab\Tests\HttpClient\Plugin;

use Gitlab\HttpClient\Plugin\ApiVersion;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Promise\HttpFulfilledPromise;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use stdClass;

final class ApiVersionTest extends TestCase
{
    public function testCallNextCallback(): void
    {
        $request = new Request('GET', '');
        $plugin  = new ApiVersion();
        $promise = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(stdClass::class)
            ->setMethods(['next'])
            ->getMock();
        $callback->expects($this->once())
            ->method('next')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willReturn($promise);

        $this->assertEquals($promise, $plugin->handleRequest($request, [$callback, 'next'], static function (): void {
        }));
    }

    public function testPrefixRequestPath(): void
    {
        $request  = new Request('GET', 'projects');
        $expected = new Request('GET', '/api/v4/projects');
        $plugin   = new ApiVersion();
        $promise  = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(stdClass::class)
            ->setMethods(['next'])
            ->getMock();
        $callback->expects($this->once())
            ->method('next')
            ->with($expected)
            ->willReturn($promise);

        $plugin->handleRequest($request, [$callback, 'next'], static function (): void {
        });
    }

    public function testNoPrefixingRequired(): void
    {
        $request = new Request('GET', '/api/v4/projects');
        $plugin  = new ApiVersion();
        $promise = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(stdClass::class)
            ->setMethods(['next'])
            ->getMock();
        $callback->expects($this->once())
            ->method('next')
            ->with($request)
            ->willReturn($promise);

        $plugin->handleRequest($request, [$callback, 'next'], static function (): void {
        });
    }
}
