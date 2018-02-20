<?php

namespace CoreShop\Payum\PostFinanceBundle\Security;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier as InnerHttpRequestVerifier;
use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Core\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\Payment;

class HttpRequestVerifier implements HttpRequestVerifierInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var \Payum\Core\Storage\StorageInterface
     */
    protected $tokenStorage;

    /**
     * @var InnerHttpRequestVerifier
     */
    protected $inner;

    /**
     * @param RepositoryInterface      $paymentRepository
     * @param StorageInterface         $tokenStorage
     * @param InnerHttpRequestVerifier $inner
     */
    public function __construct(RepositoryInterface $paymentRepository, StorageInterface $tokenStorage, InnerHttpRequestVerifier $inner)
    {
        $this->paymentRepository = $paymentRepository;
        $this->tokenStorage = $tokenStorage;
        $this->inner = $inner;
    }

    /**
     * {@inheritDoc}
     */
    public function verify($httpRequest)
    {
        return $this->inner->verify($httpRequest);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidate(TokenInterface $token)
    {
        /** @var \Payum\Core\Model\Identity $identity */
        $identity = $token->getDetails();
        $payment = $this->paymentRepository->find($identity->getId());

        if ($payment instanceof Payment) {
            /** @var PaymentProvider $paymentProvider */
            $paymentProvider = $payment->getPaymentProvider();
            if ($paymentProvider instanceof PaymentProvider) {
                /** @var GatewayConfig $gatewayConfig */
                $gatewayConfig = $paymentProvider = $paymentProvider->getGatewayConfig();
                if ($gatewayConfig instanceof GatewayConfig) {
                    if ($gatewayConfig->getFactoryName() === 'postfinance') {
                        return;
                    }
                }
            }
        }

        $this->tokenStorage->delete($token);
    }
}