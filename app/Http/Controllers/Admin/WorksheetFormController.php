<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\FormDataBuilder;
use App\Http\Controllers\FormToArrayDataConverter;
use Bara\Application\Service\CreateWorksheetForm;
use Bara\Application\Service\RemoveWorksheetForm;
use Bara\Application\Service\UpdateWorksheetForm;
use Bara\Domain\Model\Admin;
use Bara\Domain\Model\WorksheetForm as WorksheetForm2;
use Query\Application\Service\ViewWorksheetForm;
use Query\Domain\Model\Firm\WorksheetForm;

class WorksheetFormController extends AdminBaseController
{

    public function create()
    {
        $this->authorizeUserIsAdmin();
        $formData = (new FormDataBuilder($this->request))->build();
        $worksheetFormId = $this->buildCreateService()->execute($this->adminId(), $formData);
        
        $worksheetForm = $this->buildViewService()->showById($worksheetFormId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheetForm($worksheetForm));
    }

    public function update($worksheetFormId)
    {
        $this->authorizeUserIsAdmin();
        $formData = (new FormDataBuilder($this->request))->build();
        $this->buildUpdateService()->execute($this->adminId(), $worksheetFormId, $formData);
        return $this->show($worksheetFormId);
    }

    public function remove($worksheetFormId)
    {
        $this->authorizeUserIsAdmin();
        $this->buildRemoveService()->execute($this->adminId(), $worksheetFormId);
        return $this->commandOkResponse();
    }

    public function showAll()
    {
        $this->authorizeUserIsAdmin();
        $worksheetForms = $this->buildViewService()->showAll($this->getPage(), $this->getPageSize());
        $result = [];
        $result['total'] = count($worksheetForms);
        
        foreach ($worksheetForms as $worksheetForm) {
            $result['list'][] = [
                "id" => $worksheetForm->getId(),
                "name" => $worksheetForm->getName(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($worksheetFormId)
    {
        $this->authorizeUserIsAdmin();
        $worksheetForm = $this->buildViewService()->showById($worksheetFormId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheetForm($worksheetForm));
    }
    
    protected function arrayDataOfWorksheetForm(WorksheetForm $worksheetForm): array
    {
        $result = (new FormToArrayDataConverter())->convert($worksheetForm);
        $result['id'] = $worksheetForm->getId();
        return $result;
    }
    protected function buildViewService()
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new ViewWorksheetForm($worksheetFormRepository);
    }
    
    protected function buildCreateService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm2::class);
        return new CreateWorksheetForm($adminRepository, $worksheetFormRepository);
    }
    protected function buildUpdateService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm2::class);
        return new UpdateWorksheetForm($adminRepository, $worksheetFormRepository);
    }
    protected function buildRemoveService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm2::class);
        return new RemoveWorksheetForm($adminRepository, $worksheetFormRepository);
    } 

}
