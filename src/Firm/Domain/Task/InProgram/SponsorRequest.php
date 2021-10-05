<?php

namespace Firm\Domain\Task\InProgram;

class SponsorRequest
{

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $website;

    /**
     * 
     * @var string|null
     */
    protected $firmFileInfoId;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getFirmFileInfoId(): ?string
    {
        return $this->firmFileInfoId;
    }

    public function __construct(?string $name, ?string $website, ?string $firmFileInfoId)
    {
        $this->name = $name;
        $this->website = $website;
        $this->firmFileInfoId = $firmFileInfoId;
    }

}
