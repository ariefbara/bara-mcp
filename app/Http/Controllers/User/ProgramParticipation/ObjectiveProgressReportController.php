<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Participant\Application\Service\User\CancelObjectiveProgressReportSubmission;
use Participant\Application\Service\User\SubmitObjectiveProgressReport;
use Participant\Application\Service\User\UpdateObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport as ObjectiveProgressReport2;
use Participant\Domain\Model\UserParticipant as UserParticipant2;
use Query\Application\Service\User\AsProgramParticipant\ViewObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Service\ObjectiveProgressReportFinder;

class ObjectiveProgressReportController extends UserBaseController
{

    public function submit($programParticipationId, $objectiveId)
    {
        $objectiveProgressReportId = $this->buildSubmitService()->execute(
                $this->userId(), $programParticipationId, $objectiveId, $this->getObjectiveProgressReportData());

        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->userId(), $programParticipationId, $objectiveProgressReportId);
        return $this->commandCreatedResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function update($programParticipationId, $objectiveProgressReportId)
    {
        $this->buildUpdateService()->execute(
                $this->userId(), $programParticipationId, $objectiveProgressReportId,
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
                $this->userId(), $programParticipationId, $objectiveProgressReportId);
        return $this->show($programParticipationId, $objectiveProgressReportId);
    }

    public function show($programParticipationId, $objectiveProgressReportId)
    {
        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->userId(), $programParticipationId, $objectiveProgressReportId);
        return $this->singleQueryResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function showAll($programParticipationId, $objectiveId)
    {
        $objectiveProgressReports = $this->buildViewService()->showAll(
                $this->userId(), $programParticipationId, $objectiveId, $this->getPage(),
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
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport::class);
        $objectiveProgressReportFinder = new ObjectiveProgressReportFinder($objectiveProgressReportRepository);
        return new ViewObjectiveProgressReport($userParticipantRepository, $objectiveProgressReportFinder);
    }

    protected function buildSubmitService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        $objectiveRepository = $this->em->getRepository(Objective::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new SubmitObjectiveProgressReport($userParticipantRepository, $objectiveRepository, $objectiveProgressReportRepository);
    }

    protected function buildUpdateService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new UpdateObjectiveProgressReport($userParticipantRepository, $objectiveProgressReportRepository);
    }

    protected function buildCancelService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new CancelObjectiveProgressReportSubmission($userParticipantRepository, $objectiveProgressReportRepository);
    }

}
