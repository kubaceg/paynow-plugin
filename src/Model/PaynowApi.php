<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace Kubaceg\SyliusPaynowPlugin\Model;

final class PaynowApi
{
    /** @var string */
    private $environment;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $signatureKey;

    /**
     * @param string $environment
     * @param string $apiKey
     * @param string $signatureKey
     */
    public function __construct(string $environment, string $apiKey, string $signatureKey)
    {
        $this->environment = $environment;
        $this->apiKey = $apiKey;
        $this->signatureKey = $signatureKey;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }
}
