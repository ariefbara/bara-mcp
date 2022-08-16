<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Config\EventList;
use Firm\Application\Listener\ReceiveProgramApplicationFromTeamListener;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\Firm\Team\TeamParticipant as TeamParticipant2;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromTeam;
use Participant\Application\Service\Firm\Client\TeamMembership\QuitProgramParticipation;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\TeamProgramParticipation as TeamProgramParticipation2;
use Payment\Application\Listener\GenerateTeamParticipantInvoice;
use Payment\Domain\Model\Firm\Team\TeamParticipant;
use Query\Application\Service\Firm\Team\ViewTeamProgramParticipation;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Listener\CommonEntityCreatedListener;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;
use Team\Application\Service\TeamMember\ExecuteTeamTask;
use Team\Domain\Model\Team\Member;
use Team\Domain\Task\ApplyProgram;

class ProgramParticipationController extends AsTeamMemberBaseController
{
    
    protected function buildReceiveApplicationListener(AdvanceDispatcher $dispatcher)
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant2::class);
        $teamRepository = $this->em->getRepository(Team::class);
        $programRepository = $this->em->getRepository(Program::class);
        $task = new AcceptProgramApplicationFromTeam(
                $teamParticipantRepository, $teamRepository, $programRepository, $dispatcher);
        return new ReceiveProgramApplicationFromTeamListener($firmRepository, $task);
    }
    protected function buildGenerateInvoiceListener()
    {
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant::class);
        $paymentGateway = new XenditPaymentGateway();
        return new GenerateTeamParticipantInvoice($teamParticipantRepository, $paymentGateway);
    }
    public function applyProgram($teamId)
    {
        $dispatcher = new AdvanceDispatcher();
        
        $receiveApplicationListener = $this->buildReceiveApplicationListener($dispatcher);
        $dispatcher->addImmediateListener(EventList::TEAM_HAS_APPLIED_TO_PROGRAM, $receiveApplicationListener);
        
        $generateInvoiceListener = $this->buildGenerateInvoiceListener();
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $generateInvoiceListener);
        
        $applicationReceivedListener = new CommonEntityCreatedListener();
        $dispatcher->addImmediateListener(EventList::PROGRAM_APPLICATION_RECEIVED, $applicationReceivedListener);
        
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($dispatcher, $teamId) {
            $task = new ApplyProgram($dispatcher);
            $payload = $this->stripTagsInputRequest('programId');
            
            $teamMemberRepository = $this->em->getRepository(Member::class);
            $service = new ExecuteTeamTask($teamMemberRepository);
            $service->execute($this->firmId(), $this->clientId(), $teamId, $task, $payload);
            $dispatcher->finalize();
        });
        
        $teamProgramParticipation = $this->buildViewService()
                ->showById($teamId, $applicationReceivedListener->getEntityId());
        return $this->commandCreatedResponse($this->arrayDataOfTeamProgramParticipation($teamProgramParticipation));
    }
    
//    public function quit($teamId, $teamProgramParticipationId)
//    {
//        $service = $this->buildQuitService();
//        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId);
//        
//        return $this->commandOkResponse();
//    }

    public function show($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $teamProgramParticipation = $service->showById($teamId, $teamProgramParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramParticipation($teamProgramParticipation));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $teamProgramParticipations = $service->showAll($teamId, $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result["total"] = count($teamProgramParticipations);
        foreach ($teamProgramParticipations as $teamProgramParticipation) {
            $result['list'][] = [
                "id" => $teamProgramParticipation->getId(),
                "program" => [
                    "id" => $teamProgramParticipation->getProgram()->getId(),
                    "name" => $teamProgramParticipation->getProgram()->getName(),
                ],
                "status" => $teamProgramParticipation->getStatus(),
                "programPrice" => $teamProgramParticipation->getProgramPrice(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfTeamProgramParticipation(TeamProgramParticipation $teamProgramParticipation): array
    {
        $sponsors = [];
        foreach ($teamProgramParticipation->getProgram()->iterateActiveSponsort() as $sponsor) {
            $logo = empty($sponsor->getLogo()) ? null : [
                "id" => $sponsor->getLogo()->getId(),
                "url" => $sponsor->getLogo()->getFullyQualifiedFileName(),
            ];
            $sponsors[] = [
                "id" => $sponsor->getId(),
                "name" => $sponsor->getName(),
                "website" => $sponsor->getWebsite(),
                "logo" => $logo,
            ];
        }
        return [
            "id" => $teamProgramParticipation->getId(),
            "program" => [
                "id" => $teamProgramParticipation->getProgram()->getId(),
                "name" => $teamProgramParticipation->getProgram()->getName(),
                "sponsors" => $sponsors,
            ],
            "status" => $teamProgramParticipation->getStatus(),
            "programPrice" => $teamProgramParticipation->getProgramPrice(),
            "metricAssignment" => $this->arrayDataOfMetricAssignment($teamProgramParticipation->getMetricAssignment()),
            'invoice' => $this->arrayDataOfInvoice($teamProgramParticipation->getParticipantInvoice()),
        ];
    }
    protected function arrayDataOfInvoice(?ParticipantInvoice $participantInvoice): ?array
    {
        return empty($participantInvoice) ? null : [
            'issuedTime' => $participantInvoice->getInvoice()->getIssuedTimeString(),
            'expiredTime' => $participantInvoice->getInvoice()->getExpiredTimeString(),
            'paymentLink' => $participantInvoice->getInvoice()->getPaymentLink(),
            'settled' => $participantInvoice->getInvoice()->isSettled(),
        ];
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
        return [
            "id" => $metricAssignment->getId(),
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
            'lastMetricAssignmentReport' => $this->arrayDataOfMetricAssignmentReport(
                    $metricAssignment->getLastApprovedMetricAssignmentReports()),
        ];
    }
    protected function arrayDataOfAssignmentField(AssignmentField $assignmentField): array
    {
        return [
            "id" => $assignmentField->getId(),
            "target" => $assignmentField->getTarget(),
            "metric" => [
                "id" => $assignmentField->getMetric()->getId(),
                "name" => $assignmentField->getMetric()->getName(),
                "minValue" => $assignmentField->getMetric()->getMinValue(),
                "maxValue" => $assignmentField->getMetric()->getMaxValue(),
                "higherIsBetter" => $assignmentField->getMetric()->getHigherIsBetter(),
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
    
    protected function buildViewService()
    {
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new ViewTeamProgramParticipation($teamProgramParticipationRepository);
    }
    protected function buildQuitService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        return new QuitProgramParticipation($teamMembershipRepository, $teamProgramParticipationRepository);
    }

}
