<?php

namespace CoreShop\Payum\PostFinanceBundle\Command;

use CoreShop\Payum\PostFinanceBundle\Invalidator\TokenInvalidatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TokenInvalidationCommand extends Command
{
    /**
     * @var TokenInvalidatorInterface
     */
    protected $tokenInvalidator;

    /**
     * @var int
     */
    protected $days;

    /**
     * TokenInvalidationCommand constructor.
     *
     * @param TokenInvalidatorInterface $tokenInvalidator
     * @param int                       $days
     */
    public function __construct(TokenInvalidatorInterface $tokenInvalidator, $days = 0)
    {
        $this->tokenInvalidator = $tokenInvalidator;
        $this->days = $days;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $this->days;
        if ($input->getOption('days')) {
            $days = (int)$input->getOption('days');
        }

        $output->writeln('Invalidate Tokens older than ' . $days . ' days.');
        $this->tokenInvalidator->invalidate($days);

    }
}
