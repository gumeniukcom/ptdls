<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Task;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Gumeniukcom\Tasker\TaskCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Delete implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    /**
     * @var TaskCRUDInterface
     */
    private TaskCRUDInterface $taskCRUD;

    /**
     * Get constructor.
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

        $id = (int)$request->getAttribute("id");
        $task = $this->taskCRUD->getTaskById($id);
        if ($task === null) {
            $this->logger->error("task not found", ['board_id' => $id]);
            return $this->error("Not found", null, null, 404);
        }

        $result = $this->taskCRUD->deleteTask($task);
        if (!$result) {
            $this->logger->error("task delete failed", ['board_id' => $id]);
            return $this->error("Delete failed", null, null, 500);
        }
        return $this->json("ok", 200);
    }
}