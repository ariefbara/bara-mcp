<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Task\InProgram\ViewAllConsultationSetupPayload;
use Query\Domain\Task\InProgram\ViewAllConsultationSetupTask;
use Query\Domain\Task\InProgram\ViewConsultationSetupTask;

class ConsultationSetupController extends ProgramConsultationBaseController
{

    public function show($programConsultationId, $id)
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $task = new ViewConsultationSetupTask($consultationSetupRepository, $id);
        $this->executeQueryTaskInProgram($programConsultationId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationSetup($task->result));
    }

    public function showAll($programConsultationId)
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $payload = new ViewAllConsultationSetupPayload($this->getPage(), $this->getPageSize());

        $task = new ViewAllConsultationSetupTask($consultationSetupRepository, $payload);
        $this->executeQueryTaskInProgram($programConsultationId, $task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $consultationSetup) {
            $result['list'][] = [
                'id' => $consultationSetup->getId(),
                'name' => $consultationSetup->getName(),
                'duration' => $consultationSetup->getSessionDuration(),
            ];
        }
        
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationSetup(ConsultationSetup $consultationSetup): array
    {
        return [
            'id' => $consultationSetup->getId(),
            'name' => $consultationSetup->getName(),
            'duration' => $consultationSetup->getSessionDuration(),
            'feedbackForm' => $this->arrayDataOfFeedbackForm($consultationSetup->getConsultantFeedbackForm()),
        ];
    }

    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        if (empty($feedbackForm)) {
            return null;
        }
        $result = (new FormToArrayDataConverter())->convert($feedbackForm);
        $result['id'] = $feedbackForm->getId();
        return $result;
    }

}
