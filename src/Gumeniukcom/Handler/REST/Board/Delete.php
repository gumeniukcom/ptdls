<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Board;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Delete implements RequestHandlerInterface
{
    use ResponseFactoryAwareTrait;
    use LoggerTrait;

    /**
     * @var BoardCRUDInterface
     */
    private BoardCRUDInterface $boardCRUD;

    /**
     * Get constructor.
     * @param LoggerInterface $logger
     * @param BoardCRUDInterface $boardCRUD
     */
    public function __construct(LoggerInterface $logger, BoardCRUDInterface $boardCRUD)
    {
        $this->logger = $logger;
        $this->boardCRUD = $boardCRUD;
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
        $board = $this->boardCRUD->getBoardById($id);
        if ($board === null) {
            $this->logger->error("board not found", ['board_id' => $id]);
            return $this->error("Not found", null, null, 404);
        }

        $result = $this->boardCRUD->deleteBoard($board);
        if (!$result) {
            $this->logger->error("board delete faild", ['board_id' => $id]);
            return $this->error("Delete failed", null, null, 500);
        }
        return $this->json("ok", 200);
    }
}