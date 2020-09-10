<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Board;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class All implements RequestHandlerInterface
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

        $board = $this->boardCRUD->getBoardList();
        if ($board === null) {
            return $this->error("Not found", null, null, 404);
        }
        return $this->json($board, 200);
    }
}