<?php

namespace Query\Application\Service\User\AsProgramParticipant;

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

    public function showAll(string $programId, string $userId, int $page, int $pageSize): array
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository
                        ->allMissionInProgramIncludeSubmittedWorksheetFromUser($programId, $userId, $page, $pageSize);
    }

    public function getTotalMission($programId): int
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository->getTotalMissionInProgram($programId);
    }

}
