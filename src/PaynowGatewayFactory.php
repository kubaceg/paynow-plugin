<?php

declare(strict_types=1);

namespace Kubaceg\SyliusPaynowPlugin;

use Kubaceg\SyliusPaynowPlugin\Model\PaynowApi;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class PaynowGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'paynow',
            'payum.factory_title' => 'paynow',
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new PaynowApi($config['environment'], $config['api_key'], $config['signature_key']);
        };
    }
}
