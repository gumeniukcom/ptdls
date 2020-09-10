<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\AbstractService\LoggerTrait;

use Gumeniukcom\Handler\REST\Board;
use Gumeniukcom\Handler\REST\Status;
use Gumeniukcom\Handler\REST\Task;
use Gumeniukcom\ToDo\Board\BoardInMemoryStorage;
use Gumeniukcom\ToDo\Board\BoardRedisStorage;
use Gumeniukcom\ToDo\Status\StatusInMemoryStorage;
use Gumeniukcom\ToDo\Task\TaskInMemoryStorage;
use Psr\Log\LoggerInterface;
use Redis;
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

    private BoardCRUDInterface $boardCRUD;

    private StatusCRUDInterface $statusCRUD;

    private TaskCRUDInterface $taskCRUD;


    /**
     * Application constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {

        $this->logger = $logger;

        $redis = new Redis();

        $redisUrl = $_ENV['REDIS_URL'];
        $redisUrlParsed = parse_url($redisUrl);
        if (!is_array($redisUrlParsed)) {
            $this->logger->emergency("redis url not parsed", ['redis_url' => $redisUrl]);
            throw new \Exception("redis url not parsed");
        }

        $this->logger->debug('try to pconnect redis', ['redis_url_parsed' => $redisUrlParsed]);

        $redisConnected = $redis->pconnect($redisUrlParsed['host'], $redisUrlParsed['port']);
        if (!$redisConnected) {
            $this->logger->emergency("redis not connected", ['redis_url' => $redisUrl]);
            throw new \Exception("redis not connected");
        }

        $boardStorage = new BoardRedisStorage($this->logger, $redis);
        $statusStorage = new StatusInMemoryStorage($this->logger);
        $taskStorage = new TaskInMemoryStorage($this->logger);
        $tasker = new Service($this->logger, $statusStorage, $boardStorage, $taskStorage);

        $this->statusCRUD = $tasker;
        $this->boardCRUD = $tasker;
        $this->taskCRUD = $tasker;


        $this->initRouter();
    }

    public function initRouter()
    {
        $this->logger->info("start init router");
        $openApi = new OpenApi(new Info('0.0.1', 'API'));

        $validator = new RequestBodyValidationMiddleware();
        $middlewares = [$validator];
        $collector = new RouteCollector();
        $collector->get(
            'board.all',
            '/api/board/',
            new Board\All($this->logger, $this->boardCRUD),
            $middlewares
        );
        $collector->get(
            'board.get_by_id',
            '/api/board/{id<\d+>}',
            new Board\Get($this->logger, $this->boardCRUD),
            $middlewares
        );
        $collector->post(
            'board.create',
            '/api/board/',
            new Board\Create($this->logger, $this->boardCRUD),
            $middlewares,
        );
        $collector->put(
            'board.update',
            '/api/board/{id<\d+>}',
            new Board\Update($this->logger, $this->boardCRUD),
            $middlewares,
        );
        $collector->delete(
            'board.delete',
            '/api/board/{id<\d+>}',
            new Board\Delete($this->logger, $this->boardCRUD),
            $middlewares,
        );

        $collector->get(
            'status.get_by_id',
            '/api/status/{id<\d+>}',
            new Status\Get($this->logger, $this->statusCRUD),
            $middlewares
        );
        $collector->post(
            'status.create',
            '/api/status/',
            new Status\Create($this->logger, $this->statusCRUD, $this->boardCRUD),
            $middlewares,
        );
        $collector->put(
            'status.update',
            '/api/status/{id<\d+>}',
            new Status\Update($this->logger, $this->statusCRUD),
            $middlewares,
        );
        $collector->delete(
            'status.delete',
            '/api/status/{id<\d+>}',
            new Status\Delete($this->logger, $this->statusCRUD),
            $middlewares,
        );

        $collector->get(
            'task.get_by_id',
            '/api/task/{id<\d+>}',
            new Task\Get($this->logger, $this->taskCRUD),
            $middlewares
        );
        $collector->post(
            'task.create',
            '/api/task/',
            new Task\Create($this->logger, $this->taskCRUD, $this->boardCRUD, $this->statusCRUD),
            $middlewares,
        );
//        $collector->put(
//            'status.update',
//            '/api/status/{id<\d+>}',
//            new Status\Update($this->logger, $this->statusCRUD),
//            $middlewares,
//        );
        $collector->delete(
            'task.delete',
            '/api/task/{id<\d+>}',
            new Task\Delete($this->logger, $this->taskCRUD),
            $middlewares,
        );

        $this->router = new Router();
        $this->router->addRoute(...$collector->getCollection()->all());

        $openApi->addRoute(...$this->router->getRoutes());

        $collectorOpenAPI = new RouteCollector();
        $collectorOpenAPI->get('apidocs', '/apidocs', new \Gumeniukcom\Handler\OpenAPI($this->logger, $openApi));
        $this->router->addRoute(...$collectorOpenAPI->getCollection()->all());

        $this->logger->info("end init router");
    }

    public function run()
    {
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

    }
}