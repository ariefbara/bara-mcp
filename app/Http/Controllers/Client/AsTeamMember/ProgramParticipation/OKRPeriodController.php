<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Participant\Application\Service\Client\AsTeamMember\CancelOKRPeriod;
use Participant\Application\Service\Client\AsTeamMember\CreateOKRPeriod;
use Participant\Application\Service\Client\AsTeamMember\UpdateOKRPeriod;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\OKRPeriod as OKRPeriod2;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\TeamProgramParticipation as TeamProgramParticipation2;
use Query\Application\Service\TeamMember\ViewOKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use SharedContext\Domain\ValueObject\LabelData;

class OKRPeriodController extends AsTeamMemberBaseController
{

    public function create($teamId, $teamProgramParticipationId)
    {
        $okrPeriodId = $this->buildCreateService()
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $this->getOKRPeriodData());

        $okrPeriod = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $okrPeriodId);
        return $this->commandCreatedResponse($this->arrayDataOfOKRPeriod($okrPeriod));
    }

    public function update($teamId, $teamProgramParticipationId, $okrPeriodId)
    {
        $this->buildUpdateService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $okrPeriodId, $this->getOKRPeriodData());
        return $this->show($teamId, $teamProgramParticipationId, $okrPeriodId);
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

    public function cancel($teamId, $teamProgramParticipationId, $okrPeriodId)
    {
        $this->buildCancelService()
                ->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $okrPeriodId);
        return $this->show($teamId, $teamProgramParticipationId, $okrPeriodId);
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $okrPeriods = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize());
        
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

    public function show($teamId, $teamProgramParticipationId, $okrPeriodId)
    {
        $okrPeriod = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $okrPeriodId);
        return $this->singleQueryResponse($this->arrayDataOfOKRPeriod($okrPeriod));
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

    protected function buildViewService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod::class);
        return new ViewOKRPeriod($teamMemberRepository, $teamParticipantRepository, $okrPeriodRepository);
    }

    protected function buildCreateService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new CreateOKRPeriod($teamMemberRepository, $teamParticipantRepository, $okrPeriodRepository);
    }

    protected function buildUpdateService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new UpdateOKRPeriod($teamMemberRepository, $teamParticipantRepository, $okrPeriodRepository);
    }

    protected function buildCancelService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $okrPeriodRepository = $this->em->getRepository(OKRPeriod2::class);
        return new CancelOKRPeriod($teamMemberRepository, $teamParticipantRepository, $okrPeriodRepository);
    }

}
