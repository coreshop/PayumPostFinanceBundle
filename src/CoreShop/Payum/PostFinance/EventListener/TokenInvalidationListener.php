<?php

namespace CoreShop\Payum\PostFinanceBundle\EventListener;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Payum\PostFinanceBundle\Invalidator\TokenInvalidatorInterface;
use Pimcore\Event\System\MaintenanceEvent;
use Pimcore\Model\Schedule\Maintenance\Job;

final class TokenInvalidationListener
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var TokenInvalidatorInterface
     */
    private $tokenInvalidator;

    /**
     * @var int
     */
    private $days;

    /**
     * TokenInvalidationListener constructor.
     *
     * @param ConfigurationServiceInterface $configurationService
     * @param TokenInvalidatorInterface     $tokenInvalidator
     * @param int                           $days
     */
    public function __construct(
        ConfigurationServiceInterface $configurationService,
        TokenInvalidatorInterface $tokenInvalidator,
        $days = 0
    ) {
        $this->configurationService = $configurationService;
        $this->tokenInvalidator = $tokenInvalidator;
        $this->days = $days;
    }

    /**
     * @param MaintenanceEvent $maintenanceEvent
     */
    public function registerInvalidator(MaintenanceEvent $maintenanceEvent)
    {
        $lastMaintenance = $this->configurationService->get('payum_postfinance.token_invalidation.last_run');

        if (is_null($lastMaintenance)) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $manager = $maintenanceEvent->getManager();
            $manager->registerJob(new Job('payum_postfinance.token_invalidation', [
                $this->tokenInvalidator,
                'invalidate'
            ], [$this->days]));
            $this->configurationService->set('payum_postfinance.token_invalidation.last_run', time());
        }
    }
}