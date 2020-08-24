<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Action;

use Paynow\Model\Payment\Status;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['status'] ?? null;

        switch ($status) {
            case Status::STATUS_NEW:
                $request->markNew();
                break;
            case Status::STATUS_PENDING:
                $request->markPending();
                break;
            case Status::STATUS_REJECTED:
                $request->setCanceled();
                break;
            case Status::STATUS_CONFIRMED:
                $request->setCaptured();
                break;
            case Status::STATUS_ERROR:
                $request->markSuspended();
                break;
            default:
                $request->markUnknown();
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface;
    }
}
