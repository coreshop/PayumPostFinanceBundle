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

namespace CoreShop\Payum\PostFinanceBundle\Command;

use CoreShop\Payum\PostFinanceBundle\Invalidator\TokenInvalidatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TokenInvalidationCommand extends Command
{
    public function __construct(
        protected TokenInvalidatorInterface $tokenInvalidator,
        protected $days = 0
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('postfinance:invalidate-expired-tokens')
            ->setDescription('Invalid Payment Tokens which are older than ' . $this->days . ' days')
            ->addOption(
                'days', 'days',
                InputOption::VALUE_OPTIONAL,
                'Older than given days'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = $this->days;
        if ($input->getOption('days')) {
            $days = (int)$input->getOption('days');
        }

        $output->writeln('Invalidate Tokens older than ' . $days . ' days.');
        $this->tokenInvalidator->invalidate($days);

        return 0;
    }
}
