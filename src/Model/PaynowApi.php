<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Model;

final class PaynowApi
{
    /** @var string */
    private $environment;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $signatureKey;

    /** @var string */
    private $transferTitle;

    public function __construct(string $environment, string $apiKey, string $signatureKey, string $transferTitle)
    {
        $this->environment = $environment;
        $this->apiKey = $apiKey;
        $this->signatureKey = $signatureKey;
        $this->transferTitle = $transferTitle;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }

    public function getTransferTitle(): string
    {
        return $this->transferTitle;
    }
}
