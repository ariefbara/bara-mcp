<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\Firm\Program;

class Sponsor
{

    /**
     * 
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var FirmFileInfo|null
     */
    protected $logo;

    /**
     * 
     * @var string|null
     */
    protected $website;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogo(): ?FirmFileInfo
    {
        return $this->logo;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    protected function __construct()
    {
        
    }

}
