<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace Kubaceg\SyliusPaynowPlugin\Controller;

use Kubaceg\SyliusPaynowPlugin\Model\PaynowApi;
use Paynow\Notification;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\UnsupportedApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NotifyController extends AbstractController implements ApiAwareInterface
{
    /** @var PaynowApi */
    private $api;

    /**
     * @param Request $request
     * @return Response
     */
    public function notifyAction(Request $request): Response
    {
        $payload = trim(file_get_contents('php://input'));
        $headers = getallheaders();
        $notificationData = json_decode($payload, true);

        try {
            new Notification($this->api->getSignatureKey(), $payload, $headers);


        } catch (\Exception $exception) {
            throw new BadRequestHttpException("Bad request");
        }

        return new Response('', Response::HTTP_OK);
    }

    public function setApi($api)
    {
        if (!$api instanceof PaynowApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of '.PaynowApi::class);
        }

        $this->api = $api;
    }
}
