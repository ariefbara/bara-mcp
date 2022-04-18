<?php

namespace SharedContext\Domain\ValueObject;

class ProgramSnapshot
{

    /**
     * 
     * @var int|null
     */
    protected $price;

    /**
     * 
     * @var bool|null
     */
    protected $autoAccept;

    public function __construct(?int $price, ?bool $autoAccept)
    {
        $this->price = $price;
        $this->autoAccept = $autoAccept;
    }
    
    public function generateInitialRegistrationStatus(): RegistrationStatus
    {
        if (!$this->autoAccept) {
            return new RegistrationStatus(RegistrationStatus::REGISTERED);
        } elseif ($this->price) {
            return new RegistrationStatus(RegistrationStatus::SETTLEMENT_REQUIRED);
        } else {
            return new RegistrationStatus(RegistrationStatus::ACCEPTED);
        }
    }

}
