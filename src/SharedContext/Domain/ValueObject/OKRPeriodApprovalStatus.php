<?php

namespace SharedContext\Domain\ValueObject;

use Resources\BaseEnum;
use Resources\Exception\RegularException;

class OKRPeriodApprovalStatus extends BaseEnum
{

    const UNCONCLUDED = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    protected function assertUnconcluded(): void
    {
        if ($this->value !== self::UNCONCLUDED) {
            throw RegularException::forbidden('forbidden: approval status already concluded');
        }
    }
    
    public function isConcluded(): bool
    {
        return $this->value !== self::UNCONCLUDED;
    }
    
    public function isRejected(): bool
    {
        return $this->value === self::REJECTED;
    }
    
    public function isApproved(): bool
    {
        return $this->value === self::APPROVED;
    }
    
    public function approve(): self
    {
        $this->assertUnconcluded();
        return new static(self::APPROVED);
    }
    public function reject(): self
    {
        $this->assertUnconcluded();
        return new static(self::REJECTED);
    }
    
}
