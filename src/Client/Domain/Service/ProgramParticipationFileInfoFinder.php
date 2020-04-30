<?php

namespace Client\Domain\Service;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\{
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class ProgramParticipationFileInfoFinder implements IFileInfoFinder
{

    /**
     *
     * @var ProgramParticipationFileInfoRepository
     */
    protected $programParticipationFileInfoRepository;

    /**
     *
     * @var ProgramParticipationCompositionId
     */
    protected $programParticipationCompositionId;

    function __construct(PersonnelFileInfoRepository $programParticipationFileInfoRepository,
            ProgramParticipationCompositionId $programParticipationCompositionId)
    {
        $this->programParticipationFileInfoRepository = $programParticipationFileInfoRepository;
        $this->programParticipationCompositionId = $programParticipationCompositionId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->programParticipationFileInfoRepository
                        ->fileInfoOf($this->programParticipationCompositionId, $fileInfoId);
    }

}
