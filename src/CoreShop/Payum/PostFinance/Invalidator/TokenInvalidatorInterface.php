<?php

namespace CoreShop\Payum\PostFinanceBundle\Invalidator;

interface TokenInvalidatorInterface
{
    public function invalidate($days);
}