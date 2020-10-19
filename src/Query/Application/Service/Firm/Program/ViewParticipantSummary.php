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
    
    public function showAll(string $programId, int $page, int $pageSize)
    {
        return $this->participantSummaryRepository->allParticipantsSummaryInProgram($programId, $page, $pageSize);
    }
    
    public function getTotalActvieParticipants(string $programId): int
    {
        return $this->participantSummaryRepository->getTotalActiveParticipantInProgram($programId);
    }

}
