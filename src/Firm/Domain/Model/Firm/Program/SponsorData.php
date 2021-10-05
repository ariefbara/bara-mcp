<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\FirmFileInfo;

class SponsorData
{

    /**
     * 
     * @var string|null
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

    public function getName(): ?string
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

    public function __construct(?string $name, ?FirmFileInfo $logo, ?string $website)
    {
        $this->name = $name;
        $this->logo = $logo;
        $this->website = $website;
    }

}
