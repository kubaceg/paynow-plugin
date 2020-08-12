<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace Kubaceg\SyliusPaynowPlugin;

use Kubaceg\SyliusPaynowPlugin\Model\PaynowApi;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class PaynowPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'paynow',
            'payum.factory_title' => 'Paynow',
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new PaynowApi($config['environment'], $config['api_key'], $config['signature_key']);
        };
    }
}
