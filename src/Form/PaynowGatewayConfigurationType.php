<?php
/**
 * @author Jakub Cegiełka <kuba.ceg@gmail.com>
 */

namespace Kubaceg\SyliusPaynowPlugin\Form;

use Paynow\Environment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaynowGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'environment',
                ChoiceType::class,
                [
                    'choices' => [
                        'kubaceg.paynow.production' => Environment::PRODUCTION,
                        'kubaceg.paynow.sandbox' => Environment::SANDBOX,
                    ],
                    'label' => 'kubaceg.paynow.environment_type',
                ]
            )
            ->add(
                'api_key',
                TextType::class,
                [
                    'label' => 'kubaceg.paynow.api_key',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'kubaceg.paynow.gateway_configuration.api_key.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'signature_key',
                TextType::class,
                [
                    'label' => 'kubaceg.paynow.signature_key',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'kubaceg.paynow.gateway_configuration.signature_key.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            );
    }
}
