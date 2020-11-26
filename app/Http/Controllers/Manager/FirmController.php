<?php

namespace App\Http\Controllers\Manager;

use Firm\ {
    Application\Service\UpdateFirmProfile,
    Domain\Model\Firm as Firm2,
    Domain\Model\Firm\FirmFileInfo
};
use Query\ {
    Application\Service\FirmView,
    Domain\Model\Firm,
    Domain\Model\Firm\FirmFileInfo as FirmFileInfo2
};

class FirmController extends ManagerBaseController
{
    public function update()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $firmFileInfoId = $this->stripTagsInputRequest("firmFileInfoIdOfLogo");
        $displaySetting = $this->stripTagsInputRequest("displaySetting");
        
        $service->execute($this->firmId(), $firmFileInfoId, $displaySetting);
        
        $firm = $this->buildViewService()->showById($this->firmId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }
    
    public function show()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $firm = $service->showById($this->firmId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }
    
    protected function arrayDataOfFirm(Firm $firm): array
    {
        return [
            "name" => $firm->getName(),
            "domain" => $firm->getWhitelableUrl(),
            "mailSenderAddress" => $firm->getWhitelableMailSenderAddress(),
            "mailSenderName" => $firm->getWhitelableMailSenderName(),
            "logo" => $this->arrayDataOfLogo($firm->getLogo()),
            "displaySetting" => $firm->getDisplaySetting(),
        ];
    }
    protected function arrayDataOfLogo(?FirmFileInfo2 $logo): ?array
    {
        return empty($logo)? null: [
            "id" => $logo->getId(),
            "path" => $logo->getFullyQualifiedFileName(),
        ];
    }
    protected function buildViewService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmView($firmRepository);
    }
    protected function buildUpdateService()
    {
        $firmRepository = $this->em->getRepository(Firm2::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        return new UpdateFirmProfile($firmRepository, $firmFileInfoRepository);
    }
}
