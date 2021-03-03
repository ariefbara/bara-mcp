<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\Application\Service\Client\CancelOKRPeriod;
use Participant\Application\Service\Client\CreateOKRPeriod;
use Participant\Application\Service\Client\UpdateOKRPeriod;
use Participant\Domain\Model\ClientParticipant as ClientParticipant2;
use Participant\Domain\Model\Participant\OKRPeriod as OKRPeriod2;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Query\Application\Service\Client\AsProgramParticipant\ViewOKRPeriod;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use SharedContext\Domain\ValueObject\LabelData;

class OKRPeriodController extends ClientBaseController
{
    public function create($programParticipationId)
    {
        $okrPeriodId = $this->buildCreateService()
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $this->getOKRPeriodData());
        
        $okrPeriod = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $okrPeriodId);
        return $this->commandCreatedResponse($this->arrayDataOfOKRPeriod($okrPeriod));
    }
    public function update($programParticipationId, $okrPeriodId)
    {
        $this->buildUpdateService()->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $okrPeriodId, $this->getOKRPeriodData());
        return $this->show($programParticipationId, $okrPeriodId);
    }
    
    protected function getOKRPeriodData(): OKRPeriodData
    {
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $labelData = new LabelData($name, $description);
        $startDate = $this->dateTimeImmutableOfInputRequest('startDate');
        $endDate = $this->dateTimeImmutableOfInputRequest('endDate');
        $okrPeriodData = new OKRPeriodData($labelData, $startDate, $endDate);
        foreach ($this->request->input('objectives') as $objectiveRequest) {
            $objectiveId = $objectiveRequest['id'] ?? null;
            $okrPeriodData->addObjectiveData($this->getObjectiveData($objectiveRequest), $objectiveId);
        }
        return $okrPeriodData;
    }
    protected function getObjectiveData($objectiveRequest): ObjectiveData
    {
        $name = $this->stripTagsVariable($objectiveRequest['name']);
        $description = $this->stripTagsVariable($objectiveRequest['description']);
        $labelData = new LabelData($name, $description);
        $weight = $this->stripTagsVariable($objectiveRequest['weight']);
        $objectiveData = new ObjectiveData($labelData, $weight);
        foreach ($objectiveRequest['keyResults'] as $keyResultRequest) {
            $keyResultId = $keyResultRequest['id'] ?? null;
            $objectiveData->addKeyResultData($this->getKeyResultData($keyResultRequest), $keyResultId);
        }
        return $objectiveData;
    }
    protected function getKeyResultData($keyResultRequest): KeyResultData
    {
        $name = $this->stripTagsVariable($keyResultRequest['name']);
        $description = $this->stripTagsVariable($keyResultRequest['description']);
        $labelData = new LabelData($name, $description);
        $target = $this->stripTagsVariable($keyResultRequest['target']);
        $weight = $this->stripTagsVariable($keyResultRequest['weight']);
        return new KeyResultData($labelData, $target, $weight);
    }
    
    public function cancel($programParticipationId, $okrPeriodId)
    {
        $this->buildCancelService()->execute($this->firmId(), $this->clientId(), $programParticipationId, $okrPeriodId);
        return $this->show($programParticipationId, $okrPeriodId);
    }
    public function show($programParticipationId, $okrPeriodId)
    {
        $okrPeriod = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programParticipationId, $okrPeriodId);
        return $this->singleQueryResponse($this->arrayDataOfOKRPeriod($okrPeriod));
    }
    public function showAll($programParticipationId)
    {
        $okrPeriods = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($okrPeriods);
        foreach ($okrPeriods as $okrPeriod) {
            $result['list'][] = [
                'id' => $okrPeriod->getId(),
                'name' => $okrPeriod->getName(),
                'description' => $okrPeriod->getDescription(),
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
    protected function arrayDataOfLastApprovedObjectiveProgressReport(?ObjectiveProgressReport $objectiveProgressReport): ?array
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
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod::class);
        return new ViewOKRPeriod($clientParticipantRepository, $okrPeriodRepository);
    }
    protected function buildCreateService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new CreateOKRPeriod($clientParticipantRepository, $okrPeriodRepository);
    }
    protected function buildUpdateService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new UpdateOKRPeriod($clientParticipantRepository, $okrPeriodRepository);
    }
    protected function buildCancelService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new CancelOKRPeriod($clientParticipantRepository, $okrPeriodRepository);
    }
}
