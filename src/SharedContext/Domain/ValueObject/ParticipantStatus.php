<?php

namespace SharedContext\Domain\ValueObject;

use Resources\Exception\RegularException;

class ParticipantStatus
{

    const REGISTERED = 1;
    const SETTLEMENT_REQUIRED = 2;
    const ACTIVE = 3;
    const REJECTED = 4;
    const CANCELLED = 5;
    const FAILED = 6;
    const COMPLETED = 7;

    /**
     * 
     * @var int
     */
    protected $status;
    
    public function getValueName(): string
    {
        $c = new \ReflectionClass($this);
        return array_search($this->status, $c->getConstants());
    }

    public function __construct(bool $autoAccept, ?int $programPrice)
    {
        $this->status = !$autoAccept ? self::REGISTERED :
                ( $programPrice ? self::SETTLEMENT_REQUIRED : self::ACTIVE );
    }

    public function acceptRegistrant(?int $programPrice): self
    {
        $this->assertRegistered();
        $status = clone $this;
        $status->status = $programPrice ? self::SETTLEMENT_REQUIRED : self::ACTIVE;
        return $status;
    }

    public function rejectRegistrant(): self
    {
        $this->assertRegistered();
        $status = clone $this;
        $status->status = self::REJECTED;
        return $status;
    }

    public function cancelApplication(): self
    {
        $this->assertInApplicantState();
        $status = clone $this;
        $status->status = self::CANCELLED;
        return $status;
    }
    
    public function qualify(): self
    {
        if ($this->status !== self::ACTIVE) {
            throw RegularException::forbidden('can only qualify active participant');
        }
        $status = clone $this;
        $status->status = self::COMPLETED;
        return $status;
    }
    
    public function fail(): self
    {
        if ($this->status !== self::ACTIVE) {
            throw RegularException::forbidden('can only fail active participant');
        }
        $status = clone $this;
        $status->status = self::FAILED;
        return $status;
    }

    public function settlePayment(): self
    {

//THIS REQUEST COME FROM XENDIT, SO IT SHOULD NOT THROW ERROR
//        if ($this->status !== self::SETTLEMENT_REQUIRED) {
//        throw RegularException::forbidden("unable to process, no settlement required");
//        }
        $status = clone $this;
        $status->status = self::ACTIVE;
        return $status;
    }

    protected function assertRegistered(): void
    {
        if ($this->status !== self::REGISTERED) {
            throw RegularException::forbidden("can only process registered applicant");
        }
    }

    protected function assertInApplicantState(): void
    {
        if (!in_array($this->status, [self::REGISTERED, self::SETTLEMENT_REQUIRED])) {
            throw RegularException::forbidden("can only process active applicant");
        }
    }

    public function statusEquals(int $status): bool
    {
        return $this->status === $status;
    }

    public function isActiveRegistrantOrParticipant(): bool
    {
        return in_array($this->status, [self::REGISTERED, self::SETTLEMENT_REQUIRED, self::ACTIVE]);
    }

}
