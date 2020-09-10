<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Task;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Exception;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Gumeniukcom\Tasker\TaskCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Update implements RequestHandlerInterface
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
     *
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            $this->logger->error("error on unmarshall json", ['e' => $e->getMessage()]);
            return $this->error("Error on update", null, null, 500);
        }

        $this->logger->debug("request body parsed", ['body' => $body]);

        if (!isset($body['title']) && !isset($body['status'])) {
            $this->logger->debug("empty");
            return $this->error("Empty ", null, null, 400);
        }

        $title = null;
        if (isset($body['title'])) {
            $title = (string)$body['title'];


            if (strlen($title) < 2) {
                $this->logger->debug("title to short", ['title' => $title]);
                return $this->error("Too short title", null, null, 400);
            }
        }


        $status = null;
        if (isset($body['status_id'])) {
            $status = (int)$body['status_id'];
        }

        $id = (int)$request->getAttribute("id");
        $task = $this->taskCRUD->getTaskById($id);
        if ($task === null) {
            return $this->error("Not found task", null, null, 404);
        }


        $result = $this->taskCRUD->changeTask($task, $title, $status);
        if ($result === null || $result === false) {
            return $this->error("Error on update task", null, null, 500);
        }
        $this->logger->debug("task updated",
            [
                'title' => $title,
                'task' => $task,
                'status' => $status,
            ]);
        return $this->json($task, 200);
    }
}