<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Action;

use Http\Client\Exception\RequestException;
use Kubaceg\SyliusPaynowPlugin\Model\PaynowApi;
use Paynow\Client;
use Paynow\Exception\PaynowException;
use Paynow\Service\Payment;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\Capture;
use Sylius\Component\Payment\Model\PaymentInterface;

final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    /** @var PaynowApi */
    private $api;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $client = new Client($this->api->getApiKey(), $this->api->getSignatureKey(), $this->api->getEnvironment());

        $idempotencyKey = uniqid($payment->getId() . '_');
        $paymentData = [
            "amount" => $payment->getAmount(),
            "currency" => $payment->getCurrencyCode(),
            "externalId" => $payment->getId(),
            "description" => "Mamelki",
            "buyer" => [
                "email" => "aaa@aaa.pl"
            ]
        ];

        try {
            $payment = new Payment($client);
            $response = $payment->authorize($paymentData, $idempotencyKey)->getRedirectUrl();
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } catch (PaynowException $e) {
            $response = $e->getResponse();
        } finally {
            $payment->setDetails(['status' => $response->getStatusCode()]);
        }
    }

    /**
     * @param mixed $request
     * @return bool
     */
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof PaymentInterface;
    }

    /**
     * @param mixed $api
     */
    public function setApi($api)
    {
        if (!$api instanceof PaynowApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . PaynowApi::class);
        }

        $this->api = $api;
    }
}