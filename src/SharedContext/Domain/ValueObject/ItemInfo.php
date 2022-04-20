<?php

namespace SharedContext\Domain\ValueObject;

class ItemInfo
{

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var int
     */
    protected $quantity;

    /**
     * 
     * @var int|null
     */
    protected $price;

    /**
     * 
     * @var string|null
     */
    protected $category;

    /**
     * 
     * @var string|null
     */
    protected $url;

    public function __construct(string $name, int $quantity, ?int $price, ?string $category, ?string $url)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->category = $category;
        $this->url = $url;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }

}
