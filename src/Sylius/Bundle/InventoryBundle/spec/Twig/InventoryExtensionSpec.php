<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;
use Sylius\Bundle\InventoryBundle\Twig\InventoryExtension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class InventoryExtensionSpec extends ObjectBehavior
{
    function let(InventoryHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InventoryExtension::class);
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }
}
