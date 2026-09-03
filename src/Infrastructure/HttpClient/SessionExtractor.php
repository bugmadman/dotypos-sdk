<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient;

trait SessionExtractor
{
    private function getCloudId(): int
    {
        return $_SESSION['cloudId'];
    }

    private function getAccessToken(): string
    {
        return $_SESSION['accessToken'];
    }
}