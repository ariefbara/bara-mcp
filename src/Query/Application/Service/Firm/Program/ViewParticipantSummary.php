<?php

namespace Query\Application\Service\Firm\Program;

class ViewParticipantSummary
{

    /**
     *
     * @var ParticipantSummaryRepository
     */
    protected $participantSummaryRepository;

    public function __construct(ParticipantSummaryRepository $participantSummaryRepository)
    {
        $this->participantSummaryRepository = $participantSummaryRepository;
    }

    public function showAll(string $programId, int $page, int $pageSize, ?string $searchByParticipantName)
    {
        return $this->participantSummaryRepository
                ->allParticipantsSummaryInProgram($programId, $page, $pageSize, $searchByParticipantName);
    }

    public function getTotalActvieParticipants(string $programId, ?string $searchByParticipantName): int
    {
        return $this->participantSummaryRepository
                ->getTotalActiveParticipantInProgram($programId, $searchByParticipantName);
    }

    public function showAllWithMetricAchievement(
        string $firmId, string $programId, int $page, int $pageSize, string $orderType = "DESC")
    {
        return $this->participantSummaryRepository
                ->allParticipantAchievmentSummaryInProgram($firmId, $programId, $page, $pageSize, $orderType);
    }

    public function showAllWithEvaluationSummary(string $firmId, string $programId, int $page, int $pageSize)
    {
        return $this->participantSummaryRepository
                ->allParticipantEvaluationSummaryInProgram($firmId, $programId, $page, $pageSize);
    }

}
