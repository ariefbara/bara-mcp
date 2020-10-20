<?php

namespace Query\Application\Service\Firm\Team\AsProgramParticipant;

class ViewAllMissionInProgramWithSubmittedWorksheetSummary
{

    /**
     *
     * @var MissionWithSubmittedWorksheetSummaryRepository
     */
    protected $missionWithSubmittedWorksheetSummaryRepository;

    public function __construct(MissionWithSubmittedWorksheetSummaryRepository $missionWithSubmittedWorksheetSummaryRepository)
    {
        $this->missionWithSubmittedWorksheetSummaryRepository = $missionWithSubmittedWorksheetSummaryRepository;
    }

    public function showAll(string $programId, string $teamId, int $page, int $pageSize): array
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository
                        ->allMissionInProgramIncludeSubmittedWorksheetFromTeam($programId, $teamId, $page, $pageSize);
    }
    
    public function getTotalMission(string $programId): int
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository->getTotalMissionInProgram($programId);
    }

}
