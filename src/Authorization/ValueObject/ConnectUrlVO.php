<?php

namespace BMM\DotyposSdk\Authorization\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ConnectUrlVO extends ValueObject
{
    public function __construct(
        #[Assert\NotBlank]
        private string $clientId,
        #[Assert\NotBlank]
        private string $clientSecret,
        #[Assert\Url]
        private string $redirectUri,
        #[Assert\NotBlank(allowNull: true)]
        private ?string $state = null,
        //TODO scope from Enum
        private ?string $scope = '*',
    ) {
        $this->validate();
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }
}