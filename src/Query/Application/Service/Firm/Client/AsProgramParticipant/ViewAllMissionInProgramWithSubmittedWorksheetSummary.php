<?php

namespace Query\Application\Service\Firm\Client\AsProgramParticipant;

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

    public function showAll(string $programId, string $clientId, int $page, int $pageSize): array
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository
                        ->allMissionInProgramIncludeSubmittedWorksheetFromClient($programId, $clientId, $page, $pageSize);
    }

    public function getTotalMission($programId): int
    {
        return $this->missionWithSubmittedWorksheetSummaryRepository->getTotalMissionInProgram($programId);
    }

}
