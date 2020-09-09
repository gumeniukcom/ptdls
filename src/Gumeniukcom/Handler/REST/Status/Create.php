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
     * Get constructor.
     * @param LoggerInterface $logger
     * @param StatusCRUDInterface $statusCRUD
     */
    public function __construct(LoggerInterface $logger, StatusCRUDInterface $statusCRUD)
    {
        $this->logger = $logger;
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
        $params = $request->getQueryParams();

        $title = (string)$params["title"];
        if (strlen($title) < 2) {
            $this->logger->debug("title to short", ['title' => $title]);
            return $this->error("Too short title", null, null, 400);
        }
        $status = $this->statusCRUD->CreateStatus($title);
        if ($status === null) {
            return $this->error("Error on create", null, null, 500);
        }
        $this->logger->debug("status created",
            [
                'title' => $title,
                'status' => $status,
            ]);
        return $this->json($status, 201);
    }
}