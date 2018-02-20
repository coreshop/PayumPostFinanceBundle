<?php

namespace CoreShop\Payum\PostFinanceBundle\Builder;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Payum\PostFinanceBundle\Security\HttpRequestVerifier;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier as InnerHttpRequestVerifier;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Storage\StorageInterface;

class HttpRequestVerifierBuilder
{
    /**
     * @var RepositoryInterface
     */
    protected $paymentRepository;

    public function __construct(RepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param StorageInterface $tokenStorage
     *
     * @return HttpRequestVerifierInterface
     */
    public function build(StorageInterface $tokenStorage)
    {
        $inner = new InnerHttpRequestVerifier($tokenStorage);
        return new HttpRequestVerifier($this->paymentRepository, $tokenStorage, $inner);
    }

    public function __invoke()
    {
        return call_user_func_array([$this, 'build'], func_get_args());
    }
}