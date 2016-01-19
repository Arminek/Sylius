<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Behat\Context\Setup;
 
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $productRepository, SharedStorageInterface $clipboard, FactoryInterface $productFactory)
    {
        $this->beConstructedWith($productRepository, $clipboard, $productFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ProductContext');
    }

    function it_is_setup_context()
    {
        $this->shouldHaveType('Sylius\Behat\Context\SetupContext');
    }

    function it_adds_product_to_database(Product $product, $productRepository, $productFactory, $clipboard, ChannelInterface $channel)
    {
        $productFactory->createNew()->willReturn($product);
        $product->setName('Star wars mug')->shouldBeCalled();
        $product->setPrice((int) '10')->shouldBeCalled();
        $product->setDescription('Awesome star wars mug')->shouldBeCalled();

        $clipboard->getCurrentResource('channel')->willReturn($channel);
        $product->addChannel($channel)->shouldBeCalled();

        $productRepository->add($product)->shouldBeCalled();

        $this->catalogHasAProductPricedAt('Star wars mug', '10');
    }
}
