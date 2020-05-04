<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Participant;

class ParticipantView
{

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }
    
    public function showById(ProgramCompositionId $programCompositionId, string $participantId): Participant
    {
        return $this->participantRepository->ofId($programCompositionId, $participantId);
    }
    
    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Participant[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->participantRepository->all($programCompositionId, $page, $pageSize);
    }

}
