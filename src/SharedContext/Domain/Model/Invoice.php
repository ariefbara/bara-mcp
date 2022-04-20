<?php

namespace SharedContext\Domain\Model;

use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;

class Invoice
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $issuedTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $expiredTime;

    /**
     * 
     * @var string
     */
    protected $paymentLink;

    /**
     * 
     * @var bool
     */
    protected $settled;

    public function __construct(string $id, DateTimeImmutable $expiredTime, string $paymentLink)
    {
        $this->id = $id;
        $this->issuedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->expiredTime = $expiredTime;
        $this->paymentLink = $paymentLink;
        $this->settled = false;
    }
    
    public function getId(): string
    {
        return $this->id;
    }

    public function getIssuedTimeString(): string
    {
        return $this->issuedTime->format('Y-m-d H:i:s');
    }

    public function getExpiredTimeString(): string
    {
        return $this->expiredTime->format('Y-m-d H:i:s');
    }

    public function getPaymentLink(): string
    {
        return $this->paymentLink;
    }

    public function isSettled(): bool
    {
        return $this->settled;
    }
    
    public function settle(): void
    {
        if ($this->settled) {
            throw RegularException::forbidden('invoice already settled');
        }
        $this->settled = true;
    }

}
