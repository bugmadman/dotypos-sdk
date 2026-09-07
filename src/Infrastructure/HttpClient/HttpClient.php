<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient;


use BMM\DotyposSdk\Infrastructure\DataTransformer\DenormalizeTrait;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DeserializerTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ConnectExceptionDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\HeaderDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ResponseDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ViolationsExceptionDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\AuthorizationRequestVO;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

        if (201 !== $response->getStatusCode()) {
            $response = $response->getContent(false);
            $connectException = $this->deserialize($response, ConnectExceptionDTO::class);
            throw new \Exception($connectException->message, $connectException->status);
        }

        return $response->getContent();
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
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode <= 299) {
            $headers = $this->denormalize($response->getHeaders(), HeaderDTO::class);
            $responseDTO = new ResponseDTO();
            $responseDTO->data = $response->getContent();
            $responseDTO->etag = $headers->etag[0] ?? null;

            return $responseDTO;
        }

        if ($statusCode === 400) {
            $response = $response->getContent(false);
            $violationsExceptionDTO = $this->deserialize($response, ViolationsExceptionDTO::class)->violations;

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

            throw new \Exception($violations);
        } elseif ($statusCode === 403 || $statusCode === 405) {
            $response = $response->getContent(false);
            $connectException = $this->deserialize($response, ConnectExceptionDTO::class);
            throw new \Exception($connectException->message, $connectException->status);
        } elseif ($statusCode === 404) {
//            TODO return null ?
            throw new \Exception('Not found.');
        } elseif ($statusCode === 412) {
            throw new \Exception('Failed validate ETag.');
        } else {
            throw new \Exception('Something wrong at Dotypos request.');
        }
    }
}
