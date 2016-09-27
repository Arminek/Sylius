<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Payum\Core\Payum;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PayumController
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var ObjectManager
     */
    private $paymentManager;

    /**
     * @var MetadataInterface
     */
    private $paymentMetadata;

    /**
     * @var RequestConfigurationFactoryInterface
     */
    private $requestConfigurationFactory;

    /**
     * @var RedirectHandlerInterface
     */
    private $redirectHandler;

    /**
     * @param Payum $payum
     * @param PaymentRepositoryInterface $paymentRepository
     * @param ObjectManager $paymentManager
     * @param MetadataInterface $paymentMetadata
     * @param RequestConfigurationFactoryInterface $requestConfigurationFactory
     * @param RedirectHandlerInterface $redirectHandler
     */
    public function __construct(
        Payum $payum,
        PaymentRepositoryInterface $paymentRepository,
        ObjectManager $paymentManager,
        MetadataInterface $paymentMetadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RedirectHandlerInterface $redirectHandler
    ) {
        $this->payum = $payum;
        $this->paymentRepository = $paymentRepository;
        $this->paymentManager = $paymentManager;
        $this->paymentMetadata = $paymentMetadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->redirectHandler = $redirectHandler;
    }

    /**
     * @param Request $request
     * @param $lastNewPaymentId
     *
     * @return Response
     */
    public function prepareCaptureAction(Request $request, $lastNewPaymentId)
    {
        $configuration = $this->requestConfigurationFactory->create($this->paymentMetadata, $request);

        $payment = $this->paymentRepository->find($lastNewPaymentId);
        Assert::notNull($payment);

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            $configuration->getParameters()->get('after_pay[route]', null, true),
            $configuration->getParameters()->get('after_pay[parameters]', [], true)
        );

        return $this->redirectHandler->redirect($configuration, $captureToken->getTargetUrl());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function afterCaptureAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->paymentMetadata, $request);

        $token = $this->getHttpRequestVerifier()->verify($request);
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new GetStatus($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();

        $orderStateResolver = $this->getOrderStateResolver();
        $orderStateResolver->resolvePaymentState($order);
        $orderStateResolver->resolveShippingState($order);

        $this->paymentManager->flush();

        return $this->redirectHandler->redirectToRoute(
            $configuration,
            $configuration->getParameters()->get('redirect[route]', null, true),
            $configuration->getParameters()->get('redirect[parameters]', [], true)
        );
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    private function getTokenFactory()
    {
        return $this->payum->getTokenFactory();
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    private function getHttpRequestVerifier()
    {
        return $this->payum->getHttpRequestVerifier();
    }
}
