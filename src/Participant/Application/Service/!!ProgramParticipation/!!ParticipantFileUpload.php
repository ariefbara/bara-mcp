<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\{
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\ProgramParticipation\ParticipantFileInfo
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

    public function execute(string $userId, string $programParticipationId, FileInfoData $fileInfoData, $contents): string
    {
        $programParticipation = $this->programParticipationRepository->ofId($userId, $programParticipationId);
        $id = $this->participantFileInfoRepository->nextIdentity();
        $fileInfo = new \Shared\Domain\Model\FileInfo($id, $fileInfoData);

        $this->uploadFile->execute($fileInfo, $contents);

        $participantFileInfo = new ParticipantFileInfo($programParticipation, $id, $fileInfo);
        $this->participantFileInfoRepository->add($participantFileInfo);
        return $id;
    }

}
