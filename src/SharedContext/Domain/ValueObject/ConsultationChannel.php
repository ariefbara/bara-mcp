<?php

namespace SharedContext\Domain\ValueObject;

class ConsultationChannel
{

    /**
     * 
     * @var string|null
     */
    protected $media;

    /**
     * 
     * @var string|null
     */
    protected $address;

    function __construct(?string $media, ?string $address)
    {
        $this->media = $media;
        $this->address = $address;
    }

    function getMedia(): ?string
    {
        return $this->media;
    }

    function getAddress(): ?string
    {
        return $this->address;
    }

}
