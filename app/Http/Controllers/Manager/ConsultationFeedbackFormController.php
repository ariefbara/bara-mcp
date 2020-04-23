<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\ {
    FormDataBuilder,
    FormToArrayDataConverter
};
use Firm\ {
    Application\Service\Firm\ConsultationFeedbackFormAdd,
    Application\Service\Firm\ConsultationFeedbackFormRemove,
    Application\Service\Firm\ConsultationFeedbackFormUpdate,
    Application\Service\Firm\ConsultationFeedbackFormView,
    Domain\Model\Firm,
    Domain\Model\Firm\ConsultationFeedbackForm
};

class ConsultationFeedbackFormController extends ManagerBaseController
{
    public function add()
    {
        $service = $this->buildAddService();
        $formData = (new FormDataBuilder($this->request))->build();
        $consultationFeedbackForm = $service->execute($this->firmId(), $formData);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationFeedbackForm($consultationFeedbackForm));
    }

    public function update($consultationFeedbackFormId)
    {
        $service = $this->buildUpdateService();
        $formData = (new FormDataBuilder($this->request))->build();
        $consultationFeedbackForm = $service->execute($this->firmId(), $consultationFeedbackFormId, $formData);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationFeedbackForm($consultationFeedbackForm));
    }

    public function remove($consultationFeedbackFormId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $consultationFeedbackFormId);
        return $this->commandOkResponse();
    }

    public function show($consultationFeedbackFormId)
    {
        $service = $this->buildViewService();
        $consultationFeedbackForm = $service->showById($this->firmId(), $consultationFeedbackFormId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationFeedbackForm($consultationFeedbackForm));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $consultationFeedbackForms = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($consultationFeedbackForms);
        foreach ($consultationFeedbackForms as $consultationFeedbackForm) {
            $result['list'][] = [
                "id" => $consultationFeedbackForm->getId(),
                "name" => $consultationFeedbackForm->getName()
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationFeedbackForm(ConsultationFeedbackForm $consultationFeedbackForm)
    {
        $consultationFeedbackFormData = (new FormToArrayDataConverter())->convert($consultationFeedbackForm);
        $consultationFeedbackFormData['id'] = $consultationFeedbackForm->getId();
        return $consultationFeedbackFormData;
    }

    protected function buildAddService()
    {
        $consultationFeedbackFormRepository = $this->em->getRepository(ConsultationFeedbackForm::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        return new ConsultationFeedbackFormAdd($consultationFeedbackFormRepository, $firmRepository);
    }
    protected function buildUpdateService()
    {
        $consultationFeedbackFormRepository = $this->em->getRepository(ConsultationFeedbackForm::class);
        return new ConsultationFeedbackFormUpdate($consultationFeedbackFormRepository);
    }
    protected function buildRemoveService()
    {
        $consultationFeedbackFormRepository = $this->em->getRepository(ConsultationFeedbackForm::class);
        return new ConsultationFeedbackFormRemove($consultationFeedbackFormRepository);
    }
    protected function buildViewService()
    {
        $consultationFeedbackFormRepository = $this->em->getRepository(ConsultationFeedbackForm::class);
        return new ConsultationFeedbackFormView($consultationFeedbackFormRepository);
    }

}
