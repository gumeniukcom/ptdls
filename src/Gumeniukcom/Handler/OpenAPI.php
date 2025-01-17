<?php declare(strict_types=1);


namespace Gumeniukcom\Handler;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class OpenAPI implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    protected \Sunrise\Http\Router\OpenApi\OpenApi $openapi;

    /**
     * OpenAPI constructor.
     * @param LoggerInterface $logger
     * @param \Sunrise\Http\Router\OpenApi\OpenApi $openapi
     */
    public function __construct(LoggerInterface $logger, \Sunrise\Http\Router\OpenApi\OpenApi $openapi)
    {
        $this->openapi = $openapi;
        $this->logger = $logger;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->json($this->openapi->toArray());
    }
}