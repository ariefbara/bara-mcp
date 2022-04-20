<?php

namespace SharedContext\Domain\Task\Dependency;

use SharedContext\Domain\ValueObject\CustomerInfo;
use SharedContext\Domain\ValueObject\ItemInfo;

class InvoiceParameter
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int
     */
    protected $amount;

    /**
     * 
     * @var string|null
     */
    protected $description;

    /**
     * 
     * @var int|null
     */
    protected $duration;

    /**
     * 
     * @var CustomerInfo
     */
    protected $customerInfo;

    /**
     * 
     * @var ItemInfo
     */
    protected $itemInfo;

    public function __construct(
            string $id, int $amount, ?string $description, ?int $duration, CustomerInfo $customerInfo,
            ItemInfo $itemInfo)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->description = $description;
        $this->duration = $duration;
        $this->customerInfo = $customerInfo;
        $this->itemInfo = $itemInfo;
    }
    
    public function toArray()
    {
        return [
            'external_id' => $this->id,
            'amount' => $this->amount,
            'description' => $this->description,
            'invoice_duration' => $this->duration,
            'customer' => $this->customerInfo->toArray(),
            'items' => [$this->itemInfo->toArray()],
        ];
    }

}
