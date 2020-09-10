<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Task;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Gumeniukcom\Tasker\TaskCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class All implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    /**
     * @var TaskCRUDInterface
     */
    private TaskCRUDInterface $taskCRUD;

    /**
     * All constructor.
     * @param LoggerInterface $logger
     * @param TaskCRUDInterface $taskCRUD
     */
    public function __construct(LoggerInterface $logger, TaskCRUDInterface $taskCRUD)
    {
        $this->logger = $logger;
        $this->taskCRUD = $taskCRUD;
    }


    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $statuses = $this->taskCRUD->getTaskList();
        if ($statuses === null) {
            return $this->error("Not found", null, null, 404);
        }
        return $this->json($statuses, 200);
    }
}