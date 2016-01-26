<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentMethodFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $defaultFactory)
    {
        $this->beConstructedWith($defaultFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodFactory');
    }

    public function it_implements_payment_method_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodFactoryInterface');
    }

    public function it_is_factory()
    {
        $this->shouldImplement('Sylius\Component\Resource\Factory\FactoryInterface');
    }

    public function it_creates_payment_method_from_array($defaultFactory, PaymentMethodInterface $paymentMethod)
    {
        $parameters = [
            'code' => 'PM1',
            'gateway' => 'offline',
            'name' => 'Offline',
            'description' => 'Payment method',
        ];

        $defaultFactory->createNew()->willReturn($paymentMethod);

        $paymentMethod->setCode('PM1')->shouldBeCalled();
        $paymentMethod->setGateway('offline')->shouldBeCalled();
        $paymentMethod->setName('Offline')->shouldBeCalled();
        $paymentMethod->setDescription('Payment method')->shouldBeCalled();

        $this->createFromArray($parameters)->shouldReturn($paymentMethod);
    }

    public function it_prevents_creation_with_bad_gateway()
    {
        $parameters = [
            'name' => 'Offline',
            'gateway' => 'silly',
        ];

        $this->shouldThrow(new \InvalidArgumentException('There is no silly gateway registered, or update this check'))->during('createFromArray', [$parameters]);
    }

    public function it_throws_exception_when_can_not_find_proper_setter(PropertyPathInterface $propertyPath)
    {
        $parameters = [
            'productName' => 'Star wars mug',
            'name' => 'Offline',
            'gateway' => 'offline',
        ];

        $propertyPath->__toString()->willReturn('productName');
        $propertyPath->getElement(0)->willReturn('productName');

        $this->shouldThrow(new UnexpectedTypeException(null, $propertyPath->getWrappedObject(), 0))->during('createFromArray', [$parameters]);
    }

    public function it_tries_resolve_gateway_name_from_payment_method_name_if_gateway_is_not_set($defaultFactory, PaymentMethodInterface $paymentMethod)
    {
        $parameters = [
            'name' => 'Offline',
            'code' => 'PM1',
        ];
        $defaultFactory->createNew()->willReturn($paymentMethod);
        $paymentMethod->setGateway('offline')->shouldBeCalled();
        $paymentMethod->setCode('PM1')->shouldBeCalled();
        $paymentMethod->setName('Offline')->shouldBeCalled();

        $this->createFromArray($parameters);
    }

    public function it_throws_exception_if_payment_method_name_is_not_set()
    {
        $parameters = [
            'code' => 'PM1',
        ];

        $this->shouldThrow(new \InvalidArgumentException(sprintf('Name cannot be empty')))->during('createFromArray', [$parameters]);
    }
}
