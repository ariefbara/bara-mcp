<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\FirmWhitelableInfo;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class Firm
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;

    /**
     *
     * @var FirmFileInfo|null
     */
    protected $logo;

    /**
     *
     * @var string|null
     */
    protected $displaySetting;

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function createFileInfo(string $firmFileInfoId, FileInfoData $fileInfoData): FirmFileInfo
    {
       return new FirmFileInfo($this, $firmFileInfoId, $fileInfoData); 
    }

    public function updateProfile(?FirmFileInfo $logo, ?string $displaySetting): void
    {
        $this->logo = $logo;
        $this->displaySetting = $displaySetting;
    }

}
