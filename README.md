<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
    <a href="https://www.paynow.pl/" target="_blank">
        <img width="300px" src="https://www.paynow.pl/src/images/logo.svg" />
    </a>
</p>

<h1 align="center">Sylius Paynow Plugin</h1>

<p align="center">This plugin integrates Sylius with paynow payments. Currently it's in work in beta state, use on production environment on your own risk.</p>

## Installation

* Add repository to Your composer.json
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/kubaceg/paynow-plugin"
        }
    ],
```
* install plugin ``composer require kubaceg/paynow-plugin``
* add plugin to bundles.php `` Kubaceg\SyliusPaynowPlugin\KubacegSyliusPaynowPlugin::class => ['all' => true],``
* clear cache and create new payment in Sylius admin panel
* configure paynow notify URL `{your-store-address}/payment/poynow/notify`
