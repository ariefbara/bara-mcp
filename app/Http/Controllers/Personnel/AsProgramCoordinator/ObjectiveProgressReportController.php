<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Firm\Application\Service\Coordinator\ApproveObjectiveProgressReport;
use Firm\Application\Service\Coordinator\RejectObjectiveProgressReport;
use Firm\Domain\Model\Firm\Program\Coordinator as Coordinator2;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport as ObjectiveProgressReport2;
use Query\Application\Service\Coordinator\ViewObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

class ObjectiveProgressReportController extends AsProgramCoordinatorBaseController
{
    public function approve($programId, $objectiveProgressReportId)
    {
        $this->buildApproveService()->execute($this->firmId(), $this->personnelId(), $programId, $objectiveProgressReportId);
        return $this->show($programId, $objectiveProgressReportId);
    }
    public function reject($programId, $objectiveProgressReportId)
    {
        $this->buildRejectService()->execute($this->firmId(), $this->personnelId(), $programId, $objectiveProgressReportId);
        return $this->show($programId, $objectiveProgressReportId);
    }
    public function show($programId, $objectiveProgressReportId)
    {
        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->firmId(), $this->personnelId(), $programId, $objectiveProgressReportId);
        return $this->singleQueryResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }
    public function showAllInObjective($programId, $objectiveId)
    {
        $objectiveProgressReports = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $programId, $objectiveId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($objectiveProgressReports);
        foreach ($objectiveProgressReports as $objectiveProgressReport) {
            $result['list'][] = [
                'id' => $objectiveProgressReport->getId(),
                'reportDate' => $objectiveProgressReport->getReportDateString(),
                'submitTime' => $objectiveProgressReport->getSubmitTimeString(),
                'approvalStatus' => $objectiveProgressReport->getApprovalStatusValue(),
                'cancelled' => $objectiveProgressReport->isCancelled(),
            ];
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
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport::class);
        return new ViewObjectiveProgressReport($coordinatorRepository, $objectiveProgressReportRepository);
    }
    protected function buildApproveService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new ApproveObjectiveProgressReport($coordinatorRepository, $objectiveProgressReportRepository);
    }
    protected function buildRejectService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new RejectObjectiveProgressReport($coordinatorRepository, $objectiveProgressReportRepository);
    }
}
