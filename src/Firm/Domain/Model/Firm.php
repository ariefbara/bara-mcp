<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\ClientRegistrationData;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\Firm\TeamData;
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\Uuid;
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

    /**
     * 
     * @var BioSearchFilter|null
     */
    protected $bioSearchFilter;

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

    public function setBioSearchFilter(BioSearchFilterData $bioSearchFilterData): void
    {
        if (empty($this->bioSearchFilter)) {
            $id = Uuid::generateUuid4();
            $this->bioSearchFilter = new BioSearchFilter($this, $id, $bioSearchFilterData);
        } else {
            $this->bioSearchFilter->update($bioSearchFilterData);
        }
    }

    public function createClient(string $clientId, ClientRegistrationData $clientRegistrationData): Client
    {
        return new Client($this, $clientId, $clientRegistrationData);
    }
    
    public function createTeam(string $teamId, TeamData $teamData): Team
    {
        return new Team($this, $teamId, $teamData);
    }

}
