<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation\ParticipantFileInfo
};
use Shared\Domain\{
    Model\FileInfoData,
    Service\UploadFile
};

class ParticipantFileUpload
{

    /**
     *
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    function __construct(ParticipantFileInfoRepository $participantFileInfoRepository,
            ProgramParticipationRepository $programParticipationRepository, UploadFile $uploadFile)
    {
        $this->participantFileInfoRepository = $participantFileInfoRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(string $clientId, string $programParticipationId, FileInfoData $fileInfoData, $contents): string
    {
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $id = $this->participantFileInfoRepository->nextIdentity();
        $fileInfo = new \Shared\Domain\Model\FileInfo($id, $fileInfoData);

        $this->uploadFile->execute($fileInfo, $contents);

        $participantFileInfo = new ParticipantFileInfo($programParticipation, $id, $fileInfo);
        $this->participantFileInfoRepository->add($participantFileInfo);
        return $id;
    }

}
