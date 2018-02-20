<?php

namespace CoreShop\Payum\PostFinanceBundle\Security;

use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier as InnerHttpRequestVerifier;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;

class HttpRequestVerifier implements HttpRequestVerifierInterface
{
    /**
     * @var \Payum\Core\Storage\StorageInterface
     */
    protected $tokenStorage;

    /**
     * @var InnerHttpRequestVerifier
     */
    protected $inner;

    /**
     * @param StorageInterface         $tokenStorage
     * @param InnerHttpRequestVerifier $inner
     */
    public function __construct(StorageInterface $tokenStorage, InnerHttpRequestVerifier $inner)
    {
        $this->inner = $inner;
        $this->tokenStorage = $tokenStorage;
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
        if ($token->getGatewayName() === 'Postfinance') {
            return;
        }

        $this->tokenStorage->delete($token);
    }
}