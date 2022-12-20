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

namespace CoreShop\Payum\PostFinanceBundle\EventListener;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Payum\PostFinanceBundle\Invalidator\TokenInvalidatorInterface;
use Pimcore\Maintenance\TaskInterface;

final class TokenInvalidationListener implements TaskInterface
{
    public function __construct(
        private readonly ConfigurationServiceInterface $configurationService,
        private readonly TokenInvalidatorInterface $tokenInvalidator,
        private $days = 0
    ) {
    }

    public function execute(): void
    {
        $lastMaintenance = $this->configurationService->get('payum_postfinance.token_invalidation.last_run');

        if (is_null($lastMaintenance)) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $this->tokenInvalidator->invalidate($this->days);
            $this->configurationService->set('payum_postfinance.token_invalidation.last_run', time());
        }
    }
}
