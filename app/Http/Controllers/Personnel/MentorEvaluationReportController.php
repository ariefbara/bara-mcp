<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\FormRecordDataBuilder;
use Config\EventList;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor\ExecuteTask;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Personnel\Domain\Service\PersonnelFileInfoFinder;
use Personnel\Domain\Task\DedicatedMentor\CancelEvaluationReport;
use Personnel\Domain\Task\DedicatedMentor\EvaluationReportPayload;
use Personnel\Domain\Task\DedicatedMentor\SubmitEvaluationReportTask;
use Query\Application\Service\Personnel\ExecuteTask as ExecuteTask2;
use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportFilter;
use Query\Domain\Task\Personnel\ViewAllEvaluationReportsPayload;
use Query\Domain\Task\Personnel\ViewAllEvaluationReportsTask;
use Query\Domain\Task\Personnel\ViewEvaluationReportTask;
use Resources\Application\Event\Dispatcher;
use Resources\Application\Listener\CommonEntityCreatedListener;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class MentorEvaluationReportController extends PersonnelBaseController
{
    public function submit($dedicatedMentorId, $evaluationPlanId)
    {
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);
        
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new PersonnelFileInfoFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $payload = new EvaluationReportPayload($evaluationPlanId, $formRecordData);
        
        $dispatcher = new Dispatcher();
        $listener = new CommonEntityCreatedListener();
        $dispatcher->addListener(EventList::COMMON_ENTITY_CREATED, $listener);
        
        $task = new SubmitEvaluationReportTask($evaluationPlanRepository, $payload, $dispatcher);
        
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        $service = new ExecuteTask($dedicatedMentorRepository);
        $service->execute($this->firmId(), $this->personnelId(), $dedicatedMentorId, $task);
        return $this->commandCreatedResponse($this->getSingleEvaluationReportDetail($listener->getEntityId()));
    }
    
    public function cancel($dedicatedMentorId, $id)
    {
        $evaluationReportRepository = $this->em->getRepository(DedicatedMentor\EvaluationReport::class);
        $task = new CancelEvaluationReport($evaluationReportRepository, $id);
        
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        $service = new ExecuteTask($dedicatedMentorRepository);
        $service->execute($this->firmId(), $this->personnelId(), $dedicatedMentorId, $task);
        
        return $this->singleQueryResponse($this->getSingleEvaluationReportDetail($id));
    }
    
    public function show($id)
    {
        return $this->singleQueryResponse($this->getSingleEvaluationReportDetail($id));
    }
    
    public function showAll($programId)
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        
        $evaluationReportFilter = (new EvaluationReportFilter())
                ->setSubmittedStatus($this->filterBooleanOfQueryRequest('submittedStatus'))
                ->setEvaluationPlanId($this->stripTagQueryRequest('evaluationPlanId'))
                ->setParticipantName($this->stripTagQueryRequest('participantName'));
        
        $payload = new ViewAllEvaluationReportsPayload($programId, $this->getPage(), $this->getPageSize(), $evaluationReportFilter);
        $task = new ViewAllEvaluationReportsTask($evaluationReportRepository, $payload);
        
        $evaluationReports = (new ExecuteTask2($personnelRepository, $task))
                ->execute($this->firmId(), $this->personnelId());
        return $this->listQueryResponse($evaluationReports);
    }
    
    protected function getSingleEvaluationReportDetail($id): array
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new ViewEvaluationReportTask($evaluationReportRepository, $id);
        
        return (new ExecuteTask2($personnelRepository, $task))
                ->execute($this->firmId(), $this->personnelId());
    }
    
    
}
