<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Payum\PostFinanceBundle\Security;

use CoreShop\Component\PayumPayment\Model\GatewayConfig;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier as InnerHttpRequestVerifier;
use CoreShop\Component\Core\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\Payment;

class HttpRequestVerifier implements HttpRequestVerifierInterface
{
    public function __construct(
        protected RepositoryInterface $paymentRepository,
        protected StorageInterface $tokenStorage,
        protected InnerHttpRequestVerifier $inner
    )
    {
    }

    public function verify($httpRequest)
    {
        return $this->inner->verify($httpRequest);
    }

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
                $gatewayConfig = $paymentProvider->getGatewayConfig();
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
