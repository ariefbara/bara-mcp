<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\ {
    FormDataBuilder,
    FormToArrayDataConverter
};
use Firm\ {
    Application\Service\Firm\WorksheetFormAdd,
    Application\Service\Firm\WorksheetFormRemove,
    Application\Service\Firm\WorksheetFormUpdate,
    Domain\Model\Firm,
    Domain\Model\Firm\WorksheetForm
};
use Query\ {
    Application\Service\Firm\WorksheetFormView,
    Domain\Model\Firm\WorksheetForm as WorksheetForm2
};

class WorksheetFormController extends ManagerBaseController
{
    public function add()
    {
        $service = $this->buildAddService();
        $formData = (new FormDataBuilder($this->request))->build();
        $worksheetFormId = $service->execute($this->firmId(), $formData);
        
        $viewService = $this->buildViewService();
        $worksheetForm = $viewService->showById($this->firmId(), $worksheetFormId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheetForm($worksheetForm));
    }

    public function update($worksheetFormId)
    {
        $service = $this->buildUpdateService();
        $formData = (new FormDataBuilder($this->request))->build();
        $service->execute($this->firmId(), $worksheetFormId, $formData);
        
        return $this->show($worksheetFormId);
    }

    public function remove($worksheetFormId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $worksheetFormId);
        return $this->commandOkResponse();
    }

    public function show($worksheetFormId)
    {
        $service = $this->buildViewService();
        $worksheetForm = $service->showById($this->firmId(), $worksheetFormId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheetForm($worksheetForm));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $worksheetForms = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($worksheetForms);
        foreach ($worksheetForms as $worksheetForm) {
            $result['list'][] = [
                "id" => $worksheetForm->getId(),
                "name" => $worksheetForm->getName(),
                "globalForm" => $worksheetForm->isGlobalForm(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfWorksheetForm(WorksheetForm2 $worksheetForm)
    {
        $worksheetFormData = (new FormToArrayDataConverter())->convert($worksheetForm);
        $worksheetFormData['id'] = $worksheetForm->getId();
        $worksheetFormData['globalForm'] = $worksheetForm->isGlobalForm();
        return $worksheetFormData;
    }

    protected function buildAddService()
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        return new WorksheetFormAdd($worksheetFormRepository, $firmRepository);
    }
    protected function buildUpdateService()
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new WorksheetFormUpdate($worksheetFormRepository);
    }
    protected function buildRemoveService()
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new WorksheetFormRemove($worksheetFormRepository);
    }
    protected function buildViewService()
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm2::class);
        return new WorksheetFormView($worksheetFormRepository);
    }

}
