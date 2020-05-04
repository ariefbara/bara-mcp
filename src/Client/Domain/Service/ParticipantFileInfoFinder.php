<?php

namespace Client\Domain\Service;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\{
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class ParticipantFileInfoFinder implements IFileInfoFinder
{

    /**
     *
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    /**
     *
     * @var ProgramParticipationCompositionId
     */
    protected $programParticipationCompositionId;

    function __construct(ParticipantFileInfoRepository $participantFileInfoRepository,
            ProgramParticipationCompositionId $programParticipationCompositionId)
    {
        $this->participantFileInfoRepository = $participantFileInfoRepository;
        $this->programParticipationCompositionId = $programParticipationCompositionId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->participantFileInfoRepository
                        ->fileInfoOf($this->programParticipationCompositionId, $fileInfoId);
    }

}
