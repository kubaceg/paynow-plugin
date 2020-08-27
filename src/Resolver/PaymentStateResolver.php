<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Kubaceg\SyliusPaynowPlugin\PaynowGatewayFactory;
use Paynow\Model\Payment\Status;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateResolver
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EntityManagerInterface */
    private $paymentEntityManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $paymentEntityManager
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentEntityManager = $paymentEntityManager;
    }

    public function resolve(PaymentInterface $payment, string $newState): void
    {
        if (PaynowGatewayFactory::PAYNOW_METHOD_CODE !== $payment->getMethod()->getCode()) {
            return;
        }

        $paymentStateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);

        switch ($newState) {
            case Status::STATUS_NEW:
            case Status::STATUS_PENDING:
            case Status::STATUS_ERROR:
                $this->applyTransition($paymentStateMachine, PaymentTransitions::TRANSITION_PROCESS);

                break;
            case Status::STATUS_REJECTED:
                $this->applyTransition($paymentStateMachine, PaymentTransitions::TRANSITION_CANCEL);

                break;
            case Status::STATUS_CONFIRMED:
                $this->applyTransition($paymentStateMachine, PaymentTransitions::TRANSITION_COMPLETE);

                break;
            default:
                $this->applyTransition($paymentStateMachine, PaymentTransitions::TRANSITION_FAIL);
        }

        $this->paymentEntityManager->flush();
    }

    private function applyTransition(StateMachineInterface $paymentStateMachine, string $transition): void
    {
        if ($paymentStateMachine->can($transition)) {
            $paymentStateMachine->apply($transition);
        }
    }
}
