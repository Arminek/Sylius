<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context;

use Doctrine\ORM\EntityManagerInterface;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Sylius\Component\Core\Test\Services\SharedStorage;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\WebAssert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class FeatureContext extends PageObjectContext implements MinkAwareContext, KernelAwareContext
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Mink
     */
    protected $mink;

    /**
     * @var array
     */
    protected $minkParameters;

    /**
     * @var SharedStorage
     */
    protected $clipboard;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager = null;

    /**
     * {@inheritdoc}
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getService('doctrine.orm.entity_manager');
        }
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $this->entityManager->clear();
    }

    /**
     * @BeforeScenario
     */
    public function setClipboard()
    {
        $this->clipboard = $this->getService('sylius.behat.shared_storage');
    }

    /**
     * @BeforeScenario
     */
    public function setEntityManager(BeforeScenarioScope $scope)
    {
        $this->entityManager = $this->getService('doctrine.orm.entity_manager');
    }

    /**
     *
     * @param string|null $name name of the session OR active session will be used
     *
     * @return Session
     */
    public function getSession($name = null)
    {
        return $this->mink->getSession($name);
    }

    /**
     * @param string|null $name name of the session OR active session will be used
     *
     * @return WebAssert
     */
    public function assertSession($name = null)
    {
        return $this->mink->assertSession($name);
    }

    /**
     * @param string $repositoryName
     * @param array  $values
     *
     * @return object
     *
     * @throws EntityNotFoundException
     */
    protected function getObjectWithSpecificValue($repositoryName, array $values)
    {
        $objectRepository = $this->getService($repositoryName);
        $object = $objectRepository->findOneBy($values);

        $this->assertEntityIsNotNull($object);

        return $object;
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        if (null === $this->container) {
            $this->container = $this->kernel->getContainer();
        }

        return $this->container->get($id);
    }

    /**
     * @param $object
     */
    protected function persistObject($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf('This %s is not an object', $object));
        }

        $this->entityManager->persist($object);
    }

    protected function flushEntityManager()
    {
        $this->entityManager->flush();
    }

    /**
     * @param mixed|null $object
     *
     * @throws EntityNotFoundException
     */
    private function assertEntityIsNotNull($object)
    {
        if (null === $object) {
            throw new EntityNotFoundException();
        }
    }
}
