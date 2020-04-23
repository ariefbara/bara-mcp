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
    Application\Service\Firm\WorksheetFormView,
    Domain\Model\Firm,
    Domain\Model\Firm\WorksheetForm
};

class WorksheetFormController extends ManagerBaseController
{
    public function add()
    {
        $service = $this->buildAddService();
        $formData = (new FormDataBuilder($this->request))->build();
        $worksheetForm = $service->execute($this->firmId(), $formData);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheetForm($worksheetForm));
    }

    public function update($worksheetFormId)
    {
        $service = $this->buildUpdateService();
        $formData = (new FormDataBuilder($this->request))->build();
        $worksheetForm = $service->execute($this->firmId(), $worksheetFormId, $formData);
        
        return $this->singleQueryResponse($this->arrayDataOfWorksheetForm($worksheetForm));
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
                "name" => $worksheetForm->getName()
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfWorksheetForm(WorksheetForm $worksheetForm)
    {
        $worksheetFormData = (new FormToArrayDataConverter())->convert($worksheetForm);
        $worksheetFormData['id'] = $worksheetForm->getId();
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
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new WorksheetFormView($worksheetFormRepository);
    }

}
