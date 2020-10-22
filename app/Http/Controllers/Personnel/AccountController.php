<?php

namespace App\Http\Controllers\Personnel;

use Personnel\ {
    Application\Service\Firm\PersonnelChangePassword,
    Application\Service\Firm\PersonnelUpdateProfile,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\PersonnelProfileData
};
use Query\ {
    Application\Service\Firm\PersonnelView,
    Domain\Model\Firm\Personnel as Personnel2
};

class AccountController extends PersonnelBaseController
{
    public function updateProfile()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $service = new PersonnelUpdateProfile($personnelRepository);
        
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $phone = $this->stripTagsInputRequest('phone');
        $bio = $this->stripTagsInputRequest('bio');
        
        $personnelProfileData = new PersonnelProfileData($firstName, $lastName, $phone, $bio);
        $service->execute($this->firmId(), $this->personnelId(), $personnelProfileData);
        
        $viewService = $this->buildViewService();
        $personnel = $viewService->showById($this->firmId(), $this->personnelId());
        return $this->singleQueryResponse($this->arrayDataOfPersonnel($personnel));
    }
    public function changePassword()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $service = new PersonnelChangePassword($personnelRepository);
        $previousPassword = $this->stripTagsInputRequest('previousPassword');
        $newPassword = $this->stripTagsInputRequest('newPassword');
        
        $service->execute($this->firmId(), $this->personnelId(), $previousPassword, $newPassword);
    }
    
    public function show()
    {
        $service = $this->buildViewService();
        $personnel = $service->showById($this->firmId(), $this->personnelId());
        return $this->singleQueryResponse($this->arrayDataOfPersonnel($personnel));
    }
    
    protected function arrayDataOfPersonnel(Personnel2 $personnel)
    {
        return [
            "id" => $personnel->getId(),
            "firstName" => $personnel->getFirstName(),
            "lastName" => $personnel->getLastName(),
            "phone" => $personnel->getPhone(),
            "bio" => $personnel->getBio(),
        ];
    }
    
    protected function buildViewService()
    {
        $personnelRepository = $this->em->getRepository(Personnel2::class);
        return new PersonnelView($personnelRepository);
    }
}
