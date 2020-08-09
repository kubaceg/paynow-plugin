<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace SyliusPaynowPlugin;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use SyliusPaynowPlugin\Model\PaynowApi;

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
