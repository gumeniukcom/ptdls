<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Status;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Get implements RequestHandlerInterface
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
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $id = (int)$request->getAttribute("id");
        $status = $this->statusCRUD->getStatusById($id);
        if ($status === null) {
            $this->logger->error("status not found", ['status_id' => $id]);
            return $this->error("Not found", null, null, 404);
        }
        return $this->json($status, 404);
    }
}