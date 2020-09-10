<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Status;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;


final class Create implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    /**
     * @var StatusCRUDInterface
     */
    private StatusCRUDInterface $statusCRUD;

    /**
     * @var BoardCRUDInterface
     */
    private BoardCRUDInterface $boardCRUD;

    /**
     * Get constructor.
     * @param LoggerInterface $logger
     * @param StatusCRUDInterface $statusCRUD
     */
    public function __construct(LoggerInterface $logger, StatusCRUDInterface $statusCRUD, BoardCRUDInterface $boardCRUD)
    {
        $this->logger = $logger;
        $this->statusCRUD = $statusCRUD;
        $this->boardCRUD = $boardCRUD;
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

        $title = (string)$body['title'];
        $boardId = (int)$body['board_id'];

        $board = $this->boardCRUD->getBoardById($boardId);
        if ($board === null) {
            $this->logger->debug("board not found", ['board_id' => $boardId]);
            return $this->error("Board not found", null, null, 400);
        }

        if (strlen($title) < 2) {
            $this->logger->debug("title to short", ['title' => $title]);
            return $this->error("Too short title", null, null, 400);
        }
        $status = $this->statusCRUD->createStatus($title, $board);
        if ($status === null) {
            return $this->error("Error on create", null, null, 500);
        }
        $this->logger->debug("status created",
            [
                'title' => $title,
                'status' => $status,
                'board_id' => $boardId,
            ]);
        return $this->json($status, 201);
    }
}