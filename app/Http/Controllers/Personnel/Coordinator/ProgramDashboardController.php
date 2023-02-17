<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantSummaryListFilter;
use Query\Domain\Task\GenericQueryPayload;
use Query\Domain\Task\InProgram\ViewConsultantSummaryList;
use Query\Domain\Task\InProgram\ViewParticipantMetricAchievementSummaryList;
use Query\Domain\Task\InProgram\ViewParticipantMissionSummaryList;
use Query\Domain\Task\InProgram\ViewProgramSummary;
use Resources\PaginationFilter;

class ProgramDashboardController extends CoordinatorBaseController
{

    public function view($coordinatorId)
    {
        $programRepository = $this->em->getRepository(Program::class);
        $task = new ViewProgramSummary($programRepository);
        $payload = new GenericQueryPayload();
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);

        $result = $payload->result;
        $result['consultants'] = $this->viewConsultantSummaryList($coordinatorId);
        $result['participants'] = [
            'topMissionCompletion' => $this->viewTopThreeParticipantMissionSummaryList($coordinatorId),
            'bottomMissionCompletion' => $this->viewBottomThreeParticipantMissionSummaryList($coordinatorId),
            'topMetricAchievement' => $this->viewTopThreeParticipantMetricAchievementSummaryList($coordinatorId),
            'bottomMetricAchievement' => $this->viewBottomThreeParticipantMetricAchievementSummaryList($coordinatorId),
        ];

        return $this->singleQueryResponse($result);
    }
    
    protected function viewConsultantSummaryList($coordinatorId)
    {
        $consultantRepository = $this->em->getRepository(Program\Consultant::class);
        $task = new ViewConsultantSummaryList($consultantRepository);
        $payload = new CommonViewListPayload();
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        
        return $payload->result;
    }
    
    protected function viewTopThreeParticipantMissionSummaryList($coordinatorId)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantMissionSummaryList($participantRepository);
        
        $filter = (new ParticipantSummaryListFilter($this->listOfThreePagination()))
                ->setOrder(ParticipantSummaryListFilter::ORDER_BY_MISSION_COMPLETION_DESC);
        
        $payload = new CommonViewListPayload($filter);
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        return $payload->result;
    }
    
    protected function viewBottomThreeParticipantMissionSummaryList($coordinatorId)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantMissionSummaryList($participantRepository);
        
        $filter = (new ParticipantSummaryListFilter($this->listOfThreePagination()))
                ->setOrder(ParticipantSummaryListFilter::ORDER_BY_MISSION_COMPLETION_ASC);
        
        $payload = new CommonViewListPayload($filter);
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        return $payload->result;
    }
    
    protected function viewTopThreeParticipantMetricAchievementSummaryList($coordinatorId)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantMetricAchievementSummaryList($participantRepository);
        
        $filter = (new ParticipantSummaryListFilter($this->listOfThreePagination()))
                ->setOrder(ParticipantSummaryListFilter::ORDER_BY_METRIC_ACHIEVEMENT_DESC);
        
        $payload = new CommonViewListPayload($filter);
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        return $payload->result;
    }
    
    protected function viewBottomThreeParticipantMetricAchievementSummaryList($coordinatorId)
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantMetricAchievementSummaryList($participantRepository);
        
        $filter = (new ParticipantSummaryListFilter($this->listOfThreePagination()))
                ->setOrder(ParticipantSummaryListFilter::ORDER_BY_METRIC_ACHIEVEMENT_ASC);
        
        $payload = new CommonViewListPayload($filter);
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        return $payload->result;
    }
    
    protected function listOfThreePagination()
    {
        return new PaginationFilter(1, 3);
    }

}
