<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Controller;

use Kubaceg\SyliusPaynowPlugin\PaynowGatewayFactory;
use Kubaceg\SyliusPaynowPlugin\Resolver\PaymentStateResolver;
use Paynow\Notification;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NotifyController extends AbstractController
{
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
            /** @var Order $order */
            $order = $this->orderRepository->findOneByNumber($notificationData['externalId']);
            /** @var Payment $payment */
            $payment = $order->getLastPayment();

            if ($payment->getMethod()->getCode() !== PaynowGatewayFactory::PAYNOW_METHOD_CODE) {
                throw new BadRequestHttpException('Invalid payment method');
            }

            $signatureKey = $this->getSignatureKey($payment);
            new Notification($signatureKey, $payload, $headers);

            $this->stateResolver->resolve($payment, $notificationData['status']);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        return new Response('', Response::HTTP_OK);
    }

    private function getSignatureKey(Payment $payment): string
    {
        $gatewayConfig = $payment->getMethod()->getGatewayConfig()->getConfig();
        if (!isset($gatewayConfig['signature_key'])) {
            throw new BadRequestHttpException('Invalid data');
        }

        return $gatewayConfig['signature_key'];
    }
}
