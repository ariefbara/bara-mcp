<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Firm\Application\Service\Coordinator\ApproveOKRPeriod;
use Firm\Application\Service\Coordinator\RejectOKRPeriod;
use Firm\Domain\Model\Firm\Program\Coordinator as Coordinator2;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod as OKRPeriod2;
use Query\Application\Service\Coordinator\ViewOKRPeriod;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

class OKRPeriodController extends AsProgramCoordinatorBaseController
{
    public function approve($programId, $okrPeriodId)
    {
        $this->buildApproveService()->execute($this->firmId(), $this->personnelId(), $programId, $okrPeriodId);
        return $this->show($programId, $okrPeriodId);
    }
    public function reject($programId, $okrPeriodId)
    {
        $this->buildRejectService()->execute($this->firmId(), $this->personnelId(), $programId, $okrPeriodId);
        return $this->show($programId, $okrPeriodId);
    }
    public function show($programId, $okrPeriodId)
    {
        $okrPeriod = $this->buildViewService()->showById($this->firmId(), $this->personnelId(), $programId, $okrPeriodId);
        return $this->singleQueryResponse($this->arrayDataOfOKRPeriod($okrPeriod));
    }
    public function showAllBelongsToParticipant($programId, $participantId)
    {
        $okrPeriods = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $programId, $participantId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($okrPeriods);
        foreach ($okrPeriods as $okrPeriod) {
            $result['list'][] = [
                'id' => $okrPeriod->getId(),
                'name' => $okrPeriod->getName(),
                'startDate' => $okrPeriod->getStartDateString(),
                'endDate' => $okrPeriod->getEndDateString(),
                'approvalStatus' => $okrPeriod->getApprovalStatusValue(),
                'cancelled' => $okrPeriod->isCancelled(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfOKRPeriod(OKRPeriod $okrPeriod): array
    {
        $objectives = [];
        foreach ($okrPeriod->iterateObjectives() as $objective) {
            $objectives[] = $this->arrayDataOfObjective($objective);
        }
        
        return [
            'id' => $okrPeriod->getId(),
            'name' => $okrPeriod->getName(),
            'description' => $okrPeriod->getDescription(),
            'startDate' => $okrPeriod->getStartDateString(),
            'endDate' => $okrPeriod->getEndDateString(),
            'approvalStatus' => $okrPeriod->getApprovalStatusValue(),
            'cancelled' => $okrPeriod->isCancelled(),
            'objectives' => $objectives,
        ];
    }
    protected function arrayDataOfObjective(Objective $objective): array
    {
        $keyResults = [];
        foreach ($objective->iterateKeyResults() as $keyResult) {
            $keyResults[] = $this->arrayDataOfKeyResult($keyResult);
        }
        return [
            'id' => $objective->getId(),
            'name' => $objective->getName(),
            'description' => $objective->getDescription(),
            'weight' => $objective->getWeight(),
            'disabled' => $objective->isDisabled(),
            'lastApprovedProgressReport' => $this->arrayDataOfLastApprovedObjectiveProgressReport($objective->getLastApprovedProgressReport()),
            'keyResults' => $keyResults,
        ];
    }
    protected function arrayDataOfKeyResult(KeyResult $keyResult): array
    {
        return [
            'id' => $keyResult->getId(),
            'name' => $keyResult->getName(),
            'description' => $keyResult->getDescription(),
            'target' => $keyResult->getTarget(),
            'weight' => $keyResult->getWeight(),
            'disabled' => $keyResult->isDisabled(),
        ];
    }
    protected function arrayDataOfLastApprovedObjectiveProgressReport(?Objective\ObjectiveProgressReport $objectiveProgressReport): ?array
    {
        if (empty($objectiveProgressReport)) {
            return null;
        }
        
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
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod::class);
        return new ViewOKRPeriod($coordinatorRepository, $okrPeriodRepository);
    }
    protected function buildApproveService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new ApproveOKRPeriod($coordinatorRepository, $okrPeriodRepository);
    }
    protected function buildRejectService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new RejectOKRPeriod($coordinatorRepository, $okrPeriodRepository);
    }
}
