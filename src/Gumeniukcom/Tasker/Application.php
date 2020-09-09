<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\AbstractService\LoggerTrait;

use Gumeniukcom\Handler\REST\Board;
use Gumeniukcom\ToDo\Board\BoardInMemoryStorage;
use Gumeniukcom\ToDo\Status\StatusInMemoryStorage;
use Gumeniukcom\ToDo\Task\TaskInMemoryStorage;
use Psr\Log\LoggerInterface;
use Sunrise\Http\Router\OpenApi\Middleware\RequestBodyValidationMiddleware;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\Router;
use Sunrise\Http\ServerRequest\ServerRequestFactory;
use Sunrise\Http\Router\RouteCollector;

class Application
{
    use LoggerTrait;

    private Router $router;

    /**
     * Application constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {

        $this->logger = $logger;

        $boardStorage = new BoardInMemoryStorage($this->logger);
        $statusStorage = new StatusInMemoryStorage($this->logger);
        $taskStorage = new TaskInMemoryStorage($this->logger);
        $tasker = new Tasker($this->logger, $statusStorage, $boardStorage, $taskStorage);
        ////
        $tasker->CreateBoard("foobar");
        $tasker->CreateStatus("New");
        $tasker->CreateStatus("WIP");
        ///

        $openApi = new OpenApi(new Info('0.0.1', 'API'));

        $validator = new RequestBodyValidationMiddleware();
        $middlewares = [$validator];
        $collector = new RouteCollector();
        $collector->get('board.get_by_id', '/api/board/{id<\d+>}', new Board\Get($this->logger, $tasker), $middlewares);
        $collector->post(
            'board.create',
            '/api/board/',
            new Board\Create($this->logger, $tasker),
            $middlewares,
        );
        $collector->put(
            'board.update',
            '/api/board/{id<\d+>}',
            new Board\Update($this->logger, $tasker),
            $middlewares,
        );
        $collector->delete(
            'board.delete',
            '/api/board/{id<\d+>}',
            new Board\Delete($this->logger, $tasker),
            $middlewares,
        );

        $this->router = new Router();
        $this->router->addRoute(...$collector->getCollection()->all());

        $openApi->addRoute(...$this->router->getRoutes());

        $collectorOpenAPI = new RouteCollector();
        $collectorOpenAPI->get('docs', '/docs', new \Gumeniukcom\Tasker\OpenAPI($this->logger, $openApi));
        $this->router->addRoute(...$collectorOpenAPI->getCollection()->all());
    }


    public function run()
    {
        try {
            $request = ServerRequestFactory::fromGlobals();
            $response = $this->router->handle($request);
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf(
                        '%s: %s',
                        $name,
                        $value
                    ), false);
                }
            }

            header(sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ), true);

            echo $response->getBody();
        } catch (\Exception $e) {
            $this->logger->emergency("some uncatch error", [
                'e' => $e,
            ]);
        }
    }
}