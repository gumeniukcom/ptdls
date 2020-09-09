<?php declare(strict_types=1);


namespace Gumeniukcom\Handler\REST\Status;


use Arus\Http\Response\ResponseFactoryAwareTrait;
use Exception;
use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\Tasker\BoardCRUDInterface;
use Gumeniukcom\Tasker\StatusCRUDInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Update implements RequestHandlerInterface
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

        $id = (int)$request->getAttribute("id");
        $status = $this->statusCRUD->getStatusById($id);
        if ($status === null) {
            return $this->error("Not found", null, null, 404);
        }


        $result = $this->statusCRUD->changeStatus($status, $title);
        if ($result === null || $result === false) {
            return $this->error("Error on update", null, null, 500);
        }
        $this->logger->debug("status updated",
            [
                'title' => $title,
                'status' => $status,
            ]);
        return $this->json($status, 200);
    }
}