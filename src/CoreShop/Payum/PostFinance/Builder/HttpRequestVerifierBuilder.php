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

namespace CoreShop\Payum\PostFinanceBundle\Builder;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Payum\PostFinanceBundle\Security\HttpRequestVerifier;
use Payum\Core\Bridge\Symfony\Security\HttpRequestVerifier as InnerHttpRequestVerifier;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Storage\StorageInterface;

class HttpRequestVerifierBuilder
{
    public function __construct(protected RepositoryInterface $paymentRepository)
    {
    }

    public function build(StorageInterface $tokenStorage): HttpRequestVerifierInterface
    {
        $inner = new InnerHttpRequestVerifier($tokenStorage);

        return new HttpRequestVerifier($this->paymentRepository, $tokenStorage, $inner);
    }

    public function __invoke()
    {
        return call_user_func_array([$this, 'build'], func_get_args());
    }
}
