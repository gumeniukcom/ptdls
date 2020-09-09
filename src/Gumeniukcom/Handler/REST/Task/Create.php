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


final class Create implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    /**
     * @var TaskCRUDInterface
     */
    private TaskCRUDInterface $taskCRUD;

    /**
     * @var BoardCRUDInterface
     */
    private BoardCRUDInterface $boardCRUD;

    /**
     * @var StatusCRUDInterface
     */
    private StatusCRUDInterface $statusCRUD;

    /**
     * Get constructor.
     * @param LoggerInterface $logger
     * @param TaskCRUDInterface $taskCRUD
     * @param BoardCRUDInterface $boardCRUD
     * @param StatusCRUDInterface $statusCRUD
     */
    public function __construct(LoggerInterface $logger, TaskCRUDInterface $taskCRUD, BoardCRUDInterface $boardCRUD, StatusCRUDInterface $statusCRUD)
    {
        $this->logger = $logger;
        $this->taskCRUD = $taskCRUD;
        $this->boardCRUD = $boardCRUD;
        $this->statusCRUD = $statusCRUD;
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
        } catch (\Exception $e) {
            $this->logger->error("error on unmarshall json", ['e' => $e->getMessage()]);
            return $this->error("Error on update", null, null, 500);
        }

        $this->logger->debug("request body parsed", ['body' => $body]);

        if (!isset($body['title'])) {
            $this->logger->debug("title empty");
            return $this->error("Empty title", null, null, 400);
        }

        if (!isset($body['board_id'])) {
            $this->logger->debug("board_id empty");
            return $this->error("Empty board_id", null, null, 400);
        }

        if (!isset($body['status_id'])) {
            $this->logger->debug("status_id empty");
            return $this->error("Empty status_id", null, null, 400);
        }

        $title = (string)$body['title'];
        $board_id = (int)$body['board_id'];
        $status_id = (int)$body['status_id'];

        if (strlen($title) < 2) {
            $this->logger->debug("title to short", ['title' => $title]);
            return $this->error("Too short title", null, null, 400);
        }

        $board = $this->boardCRUD->getBoardById($board_id);
        if ($board === null) {
            $this->logger->debug("board not found", ['board_id' => $board_id]);
            return $this->error("Board not found", null, null, 400);
        }
        $status = $this->statusCRUD->getStatusById($status_id);
        if ($board === null) {
            $this->logger->debug("status not found", ['status_id' => $status_id]);
            return $this->error("Status not found", null, null, 400);
        }


        $task = $this->taskCRUD->createTask($title, $board, $status);
        if ($task === null) {
            return $this->error("Error on create task", null, null, 500);
        }
        $this->logger->debug("task created",
            [
                'title' => $title,
                'status' => $status,
                'board' => $board,
                'task' => $task,
            ]);
        return $this->json($task, 201);
    }
}