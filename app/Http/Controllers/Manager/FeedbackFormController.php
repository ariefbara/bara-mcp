<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\ {
    FormDataBuilder,
    FormToArrayDataConverter
};
use Firm\ {
    Application\Service\Firm\FeedbackFormAdd,
    Application\Service\Firm\FeedbackFormRemove,
    Application\Service\Firm\FeedbackFormUpdate,
    Domain\Model\Firm,
    Domain\Model\Firm\FeedbackForm
};
use Query\ {
    Application\Service\Firm\FeedbackFormView,
    Domain\Model\Firm\FeedbackForm as FeedbackForm2
};

class FeedbackFormController extends ManagerBaseController
{
    public function add()
    {
        $service = $this->buildAddService();
        $formData = (new FormDataBuilder($this->request))->build();
        $feedbackFormId = $service->execute($this->firmId(), $formData);
        
        $viewService = $this->buildViewService();
        $feedbackForm = $viewService->showById($this->firmId(), $feedbackFormId);
        return $this->commandCreatedResponse($this->arrayDataOfFeedbackForm($feedbackForm));
    }

    public function update($feedbackFormId)
    {
        $service = $this->buildUpdateService();
        $formData = (new FormDataBuilder($this->request))->build();
        $service->execute($this->firmId(), $feedbackFormId, $formData);
        
        return $this->show($feedbackFormId);
    }

    public function remove($feedbackFormId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $feedbackFormId);
        return $this->commandOkResponse();
    }

    public function show($feedbackFormId)
    {
        $service = $this->buildViewService();
        $feedbackForm = $service->showById($this->firmId(), $feedbackFormId);
        return $this->singleQueryResponse($this->arrayDataOfFeedbackForm($feedbackForm));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $feedbackForms = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($feedbackForms);
        foreach ($feedbackForms as $feedbackForm) {
            $result['list'][] = [
                "id" => $feedbackForm->getId(),
                "name" => $feedbackForm->getName()
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfFeedbackForm(FeedbackForm2 $feedbackForm)
    {
        $feedbackFormData = (new FormToArrayDataConverter())->convert($feedbackForm);
        $feedbackFormData['id'] = $feedbackForm->getId();
        return $feedbackFormData;
    }

    protected function buildAddService()
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FeedbackFormAdd($feedbackFormRepository, $firmRepository);
    }
    protected function buildUpdateService()
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);
        return new FeedbackFormUpdate($feedbackFormRepository);
    }
    protected function buildRemoveService()
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);
        return new FeedbackFormRemove($feedbackFormRepository);
    }
    protected function buildViewService()
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm2::class);
        return new FeedbackFormView($feedbackFormRepository);
    }

}
