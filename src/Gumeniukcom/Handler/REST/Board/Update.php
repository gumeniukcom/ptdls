<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Board;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Exception;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * @Route(
 *   name="apiBoardUpdate",
 *   path="/api/board/",
 *   methods={"POST"},
 *   middlewares={
 *   },
 *   attributes={
 *   },
 *   summary="Create board",
 *   description="Some description",
 *   tags={"api", "board"},
 *   priority=0,
 * )
 */
final class Update implements RequestHandlerInterface
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

        if (!isset($body['title'])) {
            $this->logger->debug("title empty");
            return $this->error("Empty title", null, null, 400);
        }

        $title = (string)$body['title'];

        if (strlen($title) < 2) {
            $this->logger->debug("title to short", ['title' => $title]);
            return $this->error("Too short title", null, null, 400);
        }

        $id = $request->getAttribute("id");
        $board = $this->boardCRUD->getBoardById((int)$id);
        if ($board === null) {
            return $this->error("Not found", null, null, 404);
        }


        $result = $this->boardCRUD->changeBoard($board, $title);
        if ($result === null || $result === false) {
            return $this->error("Error on update", null, null, 500);
        }
        $this->logger->debug("board updated",
            [
                'title' => $title,
                'board' => $board,
            ]);
        return $this->json($board, 200);
    }
}