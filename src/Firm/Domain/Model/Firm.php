<?php

namespace Firm\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\ClientCVForm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\ProfileForm;
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
     * @var ArrayCollection
     */
    protected $clientCVForms;

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

    public function assignClientCVForm(ProfileForm $profileForm): string
    {
        $p = function (ClientCVForm $clientCVForm) use ($profileForm) {
            return $clientCVForm->correspondWithProfileForm($profileForm);
        };
        if (!empty($clientCVForm = $this->clientCVForms->filter($p)->first())) {
            $clientCVForm->enable();
            $id = $clientCVForm->getId();
        } else {
            $id = Uuid::generateUuid4();
            $clientCVForm = new ClientCVForm($this, $id, $profileForm);
            $this->clientCVForms->add($clientCVForm);
        }
        return $id;
    }

}
