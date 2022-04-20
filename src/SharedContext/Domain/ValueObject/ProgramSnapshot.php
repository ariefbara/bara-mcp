<?php

namespace SharedContext\Domain\ValueObject;

class ProgramSnapshot
{
    
    /**
     * 
     * @var string
     */
    protected $name;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function isAutoAccept(): ?bool
    {
        return $this->autoAccept;
    }

    public function __construct(string $name, ?int $price, ?bool $autoAccept)
    {
        $this->name = $name;
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
    
    public function generateItemInfo(): ItemInfo
    {
        return new ItemInfo($this->name, 1, $this->price, null, null);
    }

}
