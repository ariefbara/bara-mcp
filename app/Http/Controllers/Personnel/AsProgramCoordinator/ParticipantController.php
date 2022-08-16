<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Config\EventList;
use Firm\Application\Service\Coordinator\EvaluateParticipant;
use Firm\Application\Service\Coordinator\ExecuteProgramTask;
use Firm\Application\Service\Coordinator\QualifyParticipant;
use Firm\Application\Service\Firm\Program\Participant\AssignMetrics;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\Metric;
use Firm\Domain\Model\Firm\Program\Participant as Participant2;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use Firm\Domain\Task\InProgram\AcceptRegisteredParticipant;
use Firm\Domain\Task\InProgram\RejectRegisteredParticipant;
use Payment\Application\Listener\GenerateClientParticipantInvoice;
use Payment\Application\Listener\GenerateTeamParticipantInvoice;
use Payment\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant2;
use Payment\Domain\Model\Firm\Team\TeamParticipant;
use Query\Application\Service\Firm\Program\ViewParticipant;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\Evaluation;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;

class ParticipantController extends AsProgramCoordinatorBaseController
{
    
    protected function buildGenerateClientParticipantInvoiceListener()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $paymentGateway = new XenditPaymentGateway();
        return new GenerateClientParticipantInvoice($clientParticipantRepository, $paymentGateway);
    }
    protected function buildGenerateTeamParticipantInvoiceListener()
    {
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant::class);
        $paymentGateway = new XenditPaymentGateway();
        return new GenerateTeamParticipantInvoice($teamParticipantRepository, $paymentGateway);
    }
    
    public function acceptRegisteredParticipant($programId, $participantId)
    {
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($programId, $participantId) {
            $dispatcher = new AdvanceDispatcher();

            $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $this->buildGenerateClientParticipantInvoiceListener());
            $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $this->buildGenerateTeamParticipantInvoiceListener());
            
            $coordinatorRepository = $this->em->getRepository(Coordinator::class);
            $service = new ExecuteProgramTask($coordinatorRepository);

            $participantRepository = $this->em->getRepository(Participant2::class);
            $task = new AcceptRegisteredParticipant($participantRepository, $dispatcher);
            $service->execute($this->firmId(), $this->personnelId(), $programId, $task, $participantId);
            
            $dispatcher->finalize();
        });
        
        return $this->show($programId, $participantId);
    }
    
    public function rejectRegisteredParticipant($programId, $participantId)
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $service = new ExecuteProgramTask($coordinatorRepository);

        $participantRepository = $this->em->getRepository(Participant2::class);
        $task = new RejectRegisteredParticipant($participantRepository);
        $service->execute($this->firmId(), $this->personnelId(), $programId, $task, $participantId);
        
        return $this->show($programId, $participantId);
    }

    public function evaluate($programId, $participantId)
    {
        $service = $this->buildEvaluateService();
        $evaluationPlanId = $this->stripTagsInputRequest("evaluationPlanId");
        $status = $this->stripTagsInputRequest("status");
        $extendDays = $this->integerOfInputRequest("extendDays");
        $evaluationData = new EvaluationData($status, $extendDays);

        $service->execute(
                $this->firmId(), $this->personnelId(), $programId, $participantId, $evaluationPlanId, $evaluationData);
        return $this->show($programId, $participantId);
    }

    public function qualify($programId, $participantId)
    {
        $this->buildQualifyService()->execute($this->firmId(), $this->personnelId(), $programId, $participantId);
        return $this->show($programId, $participantId);
    }

    public function assignMetric($programId, $participantId)
    {
        $service = $this->buildAssignMetricService();
        $service->execute($programId, $this->personnelId(), $participantId, $this->getMetricAssignmentDataProvider());

        return $this->show($programId, $participantId);
    }

    protected function getMetricAssignmentDataProvider()
    {
        $metricRepositoy = $this->em->getRepository(Metric::class);
        $startDate = $this->dateTimeImmutableOfInputRequest("startDate");
        $endDate = $this->dateTimeImmutableOfInputRequest("endDate");
        $dataProvider = new MetricAssignmentDataProvider($metricRepositoy, $startDate, $endDate);
        foreach ($this->request->input("assignmentFields") as $assignmentField) {
            $metricId = $this->stripTagsVariable($assignmentField["metricId"]);
            $target = $this->stripTagsVariable($assignmentField["target"]);
            $dataProvider->add($metricId, $target);
        }
        return $dataProvider;
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $status = $this->request->query('status') ?? null;
        $searchByName = $this->stripTagQueryRequest('searchByName');

        $participants = $service->showAll(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $status, $searchByName);

        $result = [];
        $result["total"] = count($participants);
        foreach ($participants as $participant) {
            $result["list"][] = $this->arrayDataOfParticipant($participant);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $participant = $service->showById($this->firmId(), $programId, $participantId);
        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "status" => $participant->getStatus(),
            "programPriceSnapshot" => $participant->getProgramPrice(),
            "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
            "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
            "team" => $this->arrayDataOfTeam($participant->getTeamParticipant()),
            "metricAssignment" => $this->arrayDataOfMetricAssignment($participant->getMetricAssignment()),
            "lastEvaluation" => $this->arrayDataOfEvaluation($participant->getLastEvaluation()),
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
            'members' => $this->arrayDataOfTeamMembers($teamParticipant->getTeam()),
        ];
    }
    protected function arrayDataOfTeamMembers(?Team $team): array
    {
        $members = [];
        foreach ($team->iterateActiveMember() as $member) {
            $members[] = [
                'id' => $member->getId(),
                'client' => [
                    'id' => $member->getClient()->getId(),
                    'name' => $member->getClient()->getFullName(),
                ],
            ];
        }
        return $members;
    }

    protected function arrayDataOfMetricAssignment(?MetricAssignment $metricAssignment): ?array
    {
        if (empty($metricAssignment)) {
            return null;
        }
        $assignmentFields = [];
        foreach ($metricAssignment->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentFields[] = $this->arrayDataOfAssignmentField($assignmentField);
        }
        return empty($metricAssignment) ? null : [
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
            "lastMetricAssignmentReport" => $this->arrayDataOfMetricAssignmentReport(
                    $metricAssignment->getLastApprovedMetricAssignmentReports()),
        ];
    }

    protected function arrayDataOfAssignmentField(?AssignmentField $assignmentField): array
    {
        return [
            "id" => $assignmentField->getId(),
            "target" => $assignmentField->getTarget(),
            "metric" => [
                "id" => $assignmentField->getMetric()->getId(),
                "name" => $assignmentField->getMetric()->getName(),
            ],
        ];
    }

    protected function arrayDataOfMetricAssignmentReport(?MetricAssignmentReport $metricAssignmentReport): ?array
    {
        if (empty($metricAssignmentReport)) {
            return null;
        }
        $assignmentFieldValues = [];
        foreach ($metricAssignmentReport->iterateNonremovedAssignmentFieldValues() as $assignmentFieldValue) {
            $assignmentFieldValues[] = $this->arrayDataOfAssignmentFieldValue($assignmentFieldValue);
        }
        return [
            "id" => $metricAssignmentReport->getId(),
            "observationTime" => $metricAssignmentReport->getObservationTimeString(),
            "submitTime" => $metricAssignmentReport->getSubmitTimeString(),
            "removed" => $metricAssignmentReport->isRemoved(),
            "assignmentFieldValues" => $assignmentFieldValues,
        ];
    }

    protected function arrayDataOfAssignmentFieldValue(AssignmentFieldValue $assignmentFieldValue): array
    {
        return [
            "id" => $assignmentFieldValue->getId(),
            "value" => $assignmentFieldValue->getValue(),
            "assignmentFieldId" => $assignmentFieldValue->getAssignmentField()->getId(),
        ];
    }

    protected function arrayDataOfEvaluation(?Evaluation $evaluation): ?array
    {
        return empty($evaluation) ? null : [
            "id" => $evaluation->getId(),
            "status" => $evaluation->getStatus(),
            "extendDays" => $evaluation->getExtendDays(),
            "submitTime" => $evaluation->getSubmitTimeString(),
            "coordinator" => [
                "id" => $evaluation->getCoordinator()->getId(),
                "name" => $evaluation->getCoordinator()->getPersonnel()->getName(),
            ],
            "evaluationPlan" => [
                "id" => $evaluation->getEvaluationPlan()->getId(),
                "name" => $evaluation->getEvaluationPlan()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        return new ViewParticipant($participantRepository);
    }

    protected function buildAssignMetricService()
    {
        $participantRepository = $this->em->getRepository(Participant2::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new AssignMetrics($participantRepository, $coordinatorRepository);
    }

    protected function buildEvaluateService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $participantRepository = $this->em->getRepository(Participant2::class);
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);
        return new EvaluateParticipant($coordinatorRepository, $participantRepository, $evaluationPlanRepository);
    }

    protected function buildQualifyService()
    {
        $participantRepository = $this->em->getRepository(Participant2::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);

        return new QualifyParticipant($participantRepository, $coordinatorRepository);
    }

}
