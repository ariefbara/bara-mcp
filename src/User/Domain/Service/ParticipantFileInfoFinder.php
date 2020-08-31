<?php

namespace User\Domain\Service;

use User\Application\Service\User\ProgramParticipation\ProgramParticipationCompositionId;
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
