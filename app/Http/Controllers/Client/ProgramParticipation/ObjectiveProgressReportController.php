<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\Application\Service\Client\CancelObjectiveProgressReportSubmission;
use Participant\Application\Service\Client\SubmitObjectiveProgressReport;
use Participant\Application\Service\Client\UpdateObjectiveProgressReport;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport as ObjectiveProgressReport2;
use Participant\Domain\Model\TeamProgramParticipation as TeamProgramParticipation2;
use Participant\Domain\Model\ClientParticipant as ClientParticipant2;
use Query\Application\Service\Client\AsProgramParticipant\ViewObjectiveProgressReport;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Query\Domain\Service\ObjectiveProgressReportFinder;

class ObjectiveProgressReportController extends ClientBaseController
{

    public function submit($programParticipationId, $objectiveId)
    {
        $objectiveProgressReportId = $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveId, $this->getObjectiveProgressReportData());

        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveProgressReportId);
        return $this->commandCreatedResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function update($programParticipationId, $objectiveProgressReportId)
    {
        $this->buildUpdateService()->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveProgressReportId,
                $this->getObjectiveProgressReportData());
        return $this->show($programParticipationId, $objectiveProgressReportId);
    }

    protected function getObjectiveProgressReportData()
    {
        $reportDate = $this->dateTimeImmutableOfInputRequest('reportDate');
        $data = new Objective\ObjectiveProgressReportData($reportDate);
        foreach ($this->request->input('keyResultProgressReports') as $keyResultProgressReport) {
            $value = $keyResultProgressReport['value'];
            $keyResultProgressReportData = new ObjectiveProgressReport2\KeyResultProgressReportData($value);
            $keyResultId = $keyResultProgressReport['keyResultId'];
            $data->addKeyResultProgressReportData($keyResultProgressReportData, $keyResultId);
        }
        return $data;
    }

    public function cancel($programParticipationId, $objectiveProgressReportId)
    {
        $this->buildCancelService()->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveProgressReportId);
        return $this->show($programParticipationId, $objectiveProgressReportId);
    }

    public function show($programParticipationId, $objectiveProgressReportId)
    {
        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveProgressReportId);
        return $this->singleQueryResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function showAll($programParticipationId, $objectiveId)
    {
        $objectiveProgressReports = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $objectiveId, $this->getPage(),
                $this->getPageSize());
        $result = [];
        $result['total'] = count($objectiveProgressReports);
        foreach ($objectiveProgressReports as $objectiveProgressReport) {
            $result['list'][] = $this->arrayDataOfObjectiveProgressReport($objectiveProgressReport);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfObjectiveProgressReport(ObjectiveProgressReport $objectiveProgressReport): array
    {
        $keyResultProgressReports = [];
        foreach ($objectiveProgressReport->iterateKeyResultProgressReports() as $keyResultProgressReport) {
            $keyResultProgressReports[] = $this->arrayDataOfKeyResultProgressReport($keyResultProgressReport);
        }
        return [
            'id' => $objectiveProgressReport->getId(),
            'reportDate' => $objectiveProgressReport->getReportDateString(),
            'submitTime' => $objectiveProgressReport->getSubmitTimeString(),
            'approvalStatus' => $objectiveProgressReport->getApprovalStatusValue(),
            'cancelled' => $objectiveProgressReport->isCancelled(),
            'keyResultProgressReports' => $keyResultProgressReports,
        ];
    }
    protected function arrayDataOfKeyResultProgressReport(KeyResultProgressReport $keyResultProgressReport): array
    {
        return [
            'id' => $keyResultProgressReport->getId(),
            'value' => $keyResultProgressReport->getValue(),
            'disabled' => $keyResultProgressReport->isDisabled(),
            'keyResult' => [
                'id' => $keyResultProgressReport->getKeyResult()->getId(),
                'name' => $keyResultProgressReport->getKeyResult()->getName(),
                'target' => $keyResultProgressReport->getKeyResult()->getTarget(),
                'weight' => $keyResultProgressReport->getKeyResult()->getWeight(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport::class);
        $objectiveProgressReportFinder = new ObjectiveProgressReportFinder($objectiveProgressReportRepository);
        return new ViewObjectiveProgressReport($clientParticipantRepository, $objectiveProgressReportFinder);
    }

    protected function buildSubmitService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $objectiveRepository = $this->em->getRepository(Objective::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new SubmitObjectiveProgressReport($clientParticipantRepository, $objectiveRepository, $objectiveProgressReportRepository);
    }

    protected function buildUpdateService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new UpdateObjectiveProgressReport($clientParticipantRepository, $objectiveProgressReportRepository);
    }

    protected function buildCancelService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new CancelObjectiveProgressReportSubmission($clientParticipantRepository, $objectiveProgressReportRepository);
    }

}
