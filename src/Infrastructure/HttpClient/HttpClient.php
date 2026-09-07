<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient;


use BMM\DotyposSdk\Exception\AuthorizationException;
use BMM\DotyposSdk\Exception\ConnectionException;
use BMM\DotyposSdk\Exception\DotyposException;
use BMM\DotyposSdk\Exception\NotFoundException;
use BMM\DotyposSdk\Exception\PreconditionFailedException;
use BMM\DotyposSdk\Exception\ValidationFailedException;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DenormalizeTrait;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DeserializerTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ConnectExceptionDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\HeaderDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ResponseDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ViolationsExceptionDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\AuthorizationRequestVO;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class HttpClient
{
    use DeserializerTrait;
    use DenormalizeTrait;

    private HttpClientInterface $client;

    public function __construct(
        private ?int $cloudId = null,
        private ?string $accessToken = null,
        ?HttpClientInterface $client = null,
    ) {
        $this->client = $client ?? SymfonyHttpClient::create();
    }

    public function sendAuthorizationRequest(AuthorizationRequestVO $payload): string
    {
        $response = $this->client->request(
            $payload->getRequestsMethod(),
            $payload->getUri(),
            [
                'headers' => [
                    'Accept' => 'application/json; charset=UTF-8',
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'authorization' => 'User ' . $payload->getUser(),
                ],
                'json' => ['_cloudId' => $payload->getCloudId()],
            ]
        );

        [$statusCode, $content] = $this->readResponse($response);

        if (201 !== $statusCode) {
            $connectException = $this->deserialize($content, ConnectExceptionDTO::class);

            throw match ($statusCode) {
                403, 405 => new AuthorizationException($connectException->message, (int) $connectException->status),
                404 => new NotFoundException($connectException->message),
                412 => new PreconditionFailedException($connectException->message),
                default => new DotyposException($connectException->message, (int) $connectException->status),
            };
        }

        return $content;
    }

    public function sendRequest(ValueObject\RequestVO $payload): ResponseDTO
    {
        $headers = [
            'Accept' => 'application/json; charset=UTF-8',
            'Content-Type' => 'application/json; charset=UTF-8',
            'authorization' => 'Bearer ' . $this->accessToken,
        ];
        if ($payload->getETag() !== null) {
            $headers['If-Match'] = $payload->getETag();
        }

        $options = [
            'headers' => $headers,
//            'query' => [
////                    'filter' => 'id|eq|900327549975047;id|eq|900269440754183',
////                    'filter' => 'id|eq|900327549975047',
////                    'filter' => 'lastName|like|111',
////                    'filter' => 'id|eq|903516130902723',
////                    'filter' => 'id|eq|907224679486955',
//            ],
        ];

        if ($payload->getData() !== null) {
            $options['json'] = \json_decode($payload->getData());
        }
        if ($payload->getPagination() !== null) {
            $options['query']['page'] = $payload->getPagination()->getPage();
            $options['query']['limit'] = $payload->getPagination()->getLimit();
        }

        $response = $this->client->request(
            $payload->getRequestsMethod(),
            $payload->getUri() . $this->cloudId . '/' . $payload->getPath(),
            $options
        );
        [$statusCode, $content, $rawHeaders] = $this->readResponse($response);

        if ($statusCode >= 200 && $statusCode <= 299) {
            $headers = $this->denormalize($rawHeaders, HeaderDTO::class);
            $responseDTO = new ResponseDTO();
            $responseDTO->data = $content;
            $responseDTO->etag = $headers->etag[0] ?? null;

            return $responseDTO;
        }

        if ($statusCode === 400) {
            $violationsExceptionDTO = $this->deserialize($content, ViolationsExceptionDTO::class)->violations;

            $violations = '';
            foreach ($violationsExceptionDTO as $key => $violation) {
                $fieldName = $violation->fieldName;
                $violations .= \sprintf(
                    '%s %s.%s',
                    $fieldName? $fieldName . ': ' : '',
                    $violation->message,
                    $key !== count($violationsExceptionDTO)? ' ' : ''
                );
            }

            throw new ValidationFailedException($violations, $violationsExceptionDTO);
        } elseif ($statusCode === 403 || $statusCode === 405) {
            $connectException = $this->deserialize($content, ConnectExceptionDTO::class);
            throw new AuthorizationException($connectException->message, (int) $connectException->status);
        } elseif ($statusCode === 404) {
            throw new NotFoundException('Not found.');
        } elseif ($statusCode === 412) {
            throw new PreconditionFailedException('Failed validate ETag.');
        } else {
            throw new DotyposException('Something wrong at Dotypos request.');
        }
    }

    /**
     * @return array{0: int, 1: string, 2: array<string, string[]>}
     */
    private function readResponse(ResponseInterface $response): array
    {
        try {
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
            $headers = $statusCode >= 200 && $statusCode <= 299 ? $response->getHeaders() : [];

            return [$statusCode, $content, $headers];
        } catch (TransportExceptionInterface $e) {
            throw new ConnectionException($e->getMessage(), previous: $e);
        }
    }
}
