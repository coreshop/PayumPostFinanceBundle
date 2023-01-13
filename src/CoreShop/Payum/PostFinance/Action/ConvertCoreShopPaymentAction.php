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

namespace CoreShop\Payum\PostFinanceBundle\Action;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;

final class ConvertCoreShopPaymentAction implements ActionInterface
{
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $order = null;
        if ($payment instanceof \CoreShop\Component\Core\Model\PaymentInterface) {
            $order = $payment->getOrder();
        }

        if (!$order instanceof OrderInterface) {
            return;
        }

        $gatewayLanguage = 'en_EN';

        if (!empty($order->getLocaleCode())) {
            $orderLanguage = $order->getLocaleCode();
            // post finance always requires a full language ISO Code
            if (!str_contains($orderLanguage, '_')) {
                $gatewayLanguage = $orderLanguage . '_' . strtoupper($orderLanguage);
            } else {
                $gatewayLanguage = $orderLanguage;
            }
        }

        $details['LANGUAGE'] = $gatewayLanguage;

        // We need to copy all logic from DachcomDigital\Payum\PostFinance\Action\ConvertPaymentAction
        // @see https://github.com/coreshop/CoreShop/pull/2164

        $details['ORDERID'] = $payment->getNumber();
        $details['CURRENCY'] = $payment->getCurrencyCode();
        $details['AMOUNT'] = $payment->getTotalAmount();
        $details['COM'] = $payment->getDescription();

        $request->setResult((array) $details);
    }

    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array';
    }
}