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

namespace CoreShop\Payum\PostFinanceBundle\Invalidator;

use CoreShop\Component\PayumPayment\Model\GatewayConfig;
use CoreShop\Component\PayumPayment\Model\PaymentSecurityToken;
use CoreShop\Component\Core\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;

final class TokenInvalidator implements TokenInvalidatorInterface
{
    public function __construct(
        private Payum $payum,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function invalidate(int $days): void
    {
        $now = new \DateTime();
        $repository = $this->entityManager->getRepository(PaymentSecurityToken::class);
        $tokens = $repository->findAll();

        $outdatedTokens = [];
        if (empty($tokens)) {
            return;
        }

        /** @var PaymentSecurityToken $token */
        foreach ($tokens as $token) {

            $targetUrl = $token->getTargetUrl();

            if (empty($targetUrl)) {
                continue;
            }

            // hacky: we only want to delete capture and after-pay tokens.
            if (!str_contains($targetUrl, 'payment/capture') && !str_contains($targetUrl, 'cs/after-pay')) {
                continue;
            }

            /** @var \Payum\Core\Model\Identity $identity */
            $identity = $token->getDetails();

            $payment = $this->payum->getStorage($identity->getClass())->find($identity);
            if (!$payment instanceof Payment) {
                continue;
            }

            /** @var PaymentProvider $paymentProvider */
            $paymentProvider = $payment->getPaymentProvider();
            if (!$paymentProvider instanceof PaymentProvider) {
                continue;
            }

            /** @var GatewayConfig $gatewayConfig */
            $gatewayConfig = $paymentProvider = $paymentProvider->getGatewayConfig();
            if (!$gatewayConfig instanceof GatewayConfig) {
                continue;
            }

            //now only tokens from postfinance factory should get deleted!
            if ($gatewayConfig->getFactoryName() !== 'postfinance') {
                continue;
            }

            $creationDate = $payment->getCreationDate();
            if (!$creationDate instanceof \DateTime) {
                continue;
            }

            if ($creationDate->diff($now)->days >= $days) {
                $outdatedTokens[] = $token;
            }
        }

        //cycle outdated and remove them.
        if (count($outdatedTokens) === 0) {
            return;
        }

        foreach ($outdatedTokens as $outdatedToken) {
            $this->entityManager->remove($outdatedToken);
        }

        $this->entityManager->flush();
    }
}
