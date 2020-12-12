<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FormDataBuilder;
use App\Http\Controllers\FormToArrayDataConverter;
use Firm\Application\Service\Manager\CreateProfileForm;
use Firm\Application\Service\Manager\UpdateProfileForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\ProfileForm as ProfileForm2;
use Query\Application\Service\Firm\ViewProfileForm;
use Query\Domain\Model\Firm\ProfileForm;

class ProfileFormController extends ManagerBaseController
{

    public function create()
    {
        $formData = (new FormDataBuilder($this->request))->build();
        $profileFormId = $this->buildCreateService()->execute($this->firmId(), $this->managerId(), $formData);
        
        $profileForm = $this->buildViewService()->showById($this->firmId(), $profileFormId);
        return $this->commandCreatedResponse($this->arrayDataOfProfileForm($profileForm));
    }

    public function update($profileFormId)
    {
        $formData = (new FormDataBuilder($this->request))->build();
        $this->buildUpdateService()->execute($this->firmId(), $this->managerId(), $profileFormId, $formData);
        return $this->show($profileFormId);
    }

    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        $profileForms = $this->buildViewService()->showAlll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        return $this->commonIdNameListQueryResponse($profileForms);
    }

    public function show($profileFormId)
    {
        $this->authorizedUserIsFirmManager();
        $profileForm = $this->buildViewService()->showById($this->firmId(), $profileFormId);
        return $this->singleQueryResponse($this->arrayDataOfProfileForm($profileForm));
    }
    
    protected function arrayDataOfProfileForm(ProfileForm $profileForm): array
    {
        $result = (new FormToArrayDataConverter())->convert($profileForm);
        $result["id"] = $profileForm->getId();
        return $result;
    }
    protected function buildViewService()
    {
        $profileFormRepository = $this->em->getRepository(ProfileForm::class);
        return new ViewProfileForm($profileFormRepository);
    }
    protected function buildCreateService()
    {
        $profileFormRepository = $this->em->getRepository(ProfileForm2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new CreateProfileForm($profileFormRepository, $managerRepository);
    }
    protected function buildUpdateService()
    {
        $profileFormRepository = $this->em->getRepository(ProfileForm2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new UpdateProfileForm($profileFormRepository, $managerRepository);
    }

}
