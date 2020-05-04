<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo;

class ParticipantFileInfoView
{

    /**
     *
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    function __construct(ParticipantFileInfoRepository $participantFileInfoRepository)
    {
        $this->participantFileInfoRepository = $participantFileInfoRepository;
    }

    public function showById(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantFileInfoId): ParticipantFileInfo
    {
        return $this->participantFileInfoRepository->ofId($programParticipationCompositionId, $participantFileInfoId);
    }

}
