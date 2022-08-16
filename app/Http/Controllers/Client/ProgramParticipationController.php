<?php

namespace App\Http\Controllers\Client;

use Client\Application\Service\ExecuteTask;
use Client\Domain\Model\Client as Client2;
use Client\Domain\Task\ApplyProgram;
use Config\EventList;
use Firm\Application\Listener\ListeningToProgramRegistrationFromClient;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant3;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromClient;
use Participant\Application\Service\ClientQuitParticipation;
use Participant\Domain\Model\ClientParticipant as ClientParticipant2;
use Payment\Application\Listener\GenerateClientParticipantInvoice;
use Payment\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant4;
use Query\Application\Service\Firm\Client\ViewProgramParticipation;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Listener\CommonEntityCreatedListener;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;

class ProgramParticipationController extends ClientBaseController
{

    protected function buildReceiveApplicationListener(AdvanceDispatcher $dispatcher)
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant3::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $programRepository = $this->em->getRepository(Program::class);
        $task = new AcceptProgramApplicationFromClient(
                $clientParticipantRepository, $clientRepository, $programRepository, $dispatcher);
        return new ListeningToProgramRegistrationFromClient($firmRepository, $task);
    }
    protected function buildGenerateInvoiceListener()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant4::class);
        $paymentGateway = new XenditPaymentGateway();
        return new GenerateClientParticipantInvoice($clientParticipantRepository, $paymentGateway);
    }
    public function applyProgram()
    {
        $dispatcher = new AdvanceDispatcher();

        $receiveApplicationListener = $this->buildReceiveApplicationListener($dispatcher);
        $dispatcher->addImmediateListener(EventList::CLIENT_HAS_APPLIED_TO_PROGRAM, $receiveApplicationListener);
        
        $generateInvoiceListener = $this->buildGenerateInvoiceListener();
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $generateInvoiceListener);
        
        $applicationReceivedListener = new CommonEntityCreatedListener();
        $dispatcher->addImmediateListener(EventList::PROGRAM_APPLICATION_RECEIVED, $applicationReceivedListener);
        
        $task = new ApplyProgram($dispatcher);
        $clientRepository = $this->em->getRepository(Client2::class);
        $service = new ExecuteTask($clientRepository);
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($service, $task, $dispatcher) {
            $programId = $this->stripTagsInputRequest('programId');
            $service->execute($this->clientId(), $task, $programId);
            $dispatcher->finalize();
        });
        
        $programParticipation = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $applicationReceivedListener->getEntityId());
        return $this->commandCreatedResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

//    public function quit($programParticipationId)
//    {
//        $service = $this->buildQuitService();
//        $service->execute($this->firmId(), $this->clientId(), $programParticipationId);
//        return $this->commandOkResponse();
//    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $programParticipations = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $activeStatus);

        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = [
                "id" => $programParticipation->getId(),
                "program" => [
                    "id" => $programParticipation->getProgram()->getId(),
                    "name" => $programParticipation->getProgram()->getName(),
                ],
                "status" => $programParticipation->getStatus(),
                "programPrice" => $programParticipation->getProgramPrice(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramParticipation(ClientParticipant $programParticipation): array
    {
        $sponsors = [];
        foreach ($programParticipation->getProgram()->iterateActiveSponsort() as $sponsor) {
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
            "id" => $programParticipation->getId(),
            "program" => [
                "id" => $programParticipation->getProgram()->getId(),
                "name" => $programParticipation->getProgram()->getName(),
                "sponsors" => $sponsors,
            ],
            "status" => $programParticipation->getStatus(),
            "programPrice" => $programParticipation->getProgramPrice(),
            "metricAssignment" => $this->arrayDataOfMetricAssignment($programParticipation->getMetricAssignment()),
            'invoice' => $this->arrayDataOfInvoice($programParticipation->getParticipantInvoice()),
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

    protected function buildQuitService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        return new ClientQuitParticipation($clientParticipantRepository);
    }

    protected function buildViewService()
    {
        $programParticipationRepository = $this->em->getRepository(ClientParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }

}
