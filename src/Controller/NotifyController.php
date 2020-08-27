<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Controller;

use Kubaceg\SyliusPaynowPlugin\Model\PaynowApi;
use Kubaceg\SyliusPaynowPlugin\PaynowGatewayFactory;
use Kubaceg\SyliusPaynowPlugin\Resolver\PaymentStateResolver;
use Paynow\Notification;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NotifyController extends AbstractController implements ApiAwareInterface
{
    /** @var PaynowApi */
    private $api;

    /** @var OrderRepository */
    private $orderRepository;

    /** @var PaymentStateResolver */
    private $stateResolver;

    public function __construct(OrderRepository $orderRepository, PaymentStateResolver $stateResolver)
    {
        $this->orderRepository = $orderRepository;
        $this->stateResolver = $stateResolver;
    }

    public function notifyAction(Request $request): Response
    {
        $payload = trim(file_get_contents('php://input'));
        $headers = getallheaders();
        $notificationData = json_decode($payload, true);

        try {
            new Notification($this->api->getSignatureKey(), $payload, $headers);

            /** @var Order $order */
            $order = $this->orderRepository->findOneByNumber($notificationData['externalId']);

            $payment = $order->getLastPayment();

            if ($payment->getMethod()->getCode() !== PaynowGatewayFactory::PAYNOW_METHOD_CODE) {
                throw new BadRequestHttpException('Invalid payment method');
            }

            $this->stateResolver->resolve($payment, $notificationData['status']);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Bad request');
        }

        return new Response('', Response::HTTP_OK);
    }

    public function setApi($api)
    {
        if (!$api instanceof PaynowApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . PaynowApi::class);
        }

        $this->api = $api;
    }
}
