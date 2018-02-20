<?php

namespace CoreShop\Payum\PostFinanceBundle\Invalidator;

use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Bundle\PayumBundle\Model\PaymentSecurityToken;
use CoreShop\Component\Core\Model\PaymentProvider;
use CoreShop\Component\Payment\Model\Payment;
use Payum\Core\Payum;
use Payum\Core\Storage\StorageInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class TokenInvalidator implements TokenInvalidatorInterface
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var StorageInterface
     */
    private $objectManager;

    /**
     * TokenInvalidator constructor.
     *
     * @param Payum         $payum
     * @param ObjectManager $objectManager
     */
    public function __construct(Payum $payum, ObjectManager $objectManager = null)
    {
        $this->payum = $payum;
        $this->objectManager = $objectManager;
    }

    /**
     * @param $days
     */
    public function invalidate($days)
    {
        $now = new \DateTime();
        $repository = $this->objectManager->getRepository(PaymentSecurityToken::class);
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
            if (strpos($targetUrl, 'payment/capture') === false && strpos($targetUrl, 'cs/after-pay') === false) {
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
            $this->objectManager->remove($outdatedToken);
        }

        $this->objectManager->flush();

    }
}