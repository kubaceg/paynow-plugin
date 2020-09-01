<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace Kubaceg\SyliusPaynowPlugin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class SuccessController
{
    /** @var Environment */
    private $environment;

    /**
     * SuccessController constructor.
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function successAction(Request $request): Response
    {
        $params = [
            'paymentId' => $request->get('paymentId'),
            'paymentStatus' => $request->get('paymentStatus'),
        ];

        return new Response($this->environment->render('@KubacegSyliusPaynowPlugin/success.html.twig', $params));
    }
}
