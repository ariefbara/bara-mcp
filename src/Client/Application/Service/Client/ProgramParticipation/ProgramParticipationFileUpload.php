<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo
};
use Shared\Domain\{
    Model\FileInfoData,
    Service\UploadFile
};

class ProgramParticipationFileUpload
{

    /**
     *
     * @var ProgramParticipationFileInfoRepository
     */
    protected $programParticipationFileInfoRepository;

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

    function __construct(ProgramParticipationFileInfoRepository $programParticipationFileInfoRepository,
            ProgramParticipationRepository $programParticipationRepository, UploadFile $uploadFile)
    {
        $this->programParticipationFileInfoRepository = $programParticipationFileInfoRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(string $clientId, string $programParticipationId, FileInfoData $fileInfoData, $contents): ProgramParticipationFileInfo
    {
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $id = $this->programParticipationFileInfoRepository->nextIdentity();
        $fileInfo = new \Shared\Domain\Model\FileInfo($id, $fileInfoData);

        $this->uploadFile->execute($fileInfo, $contents);

        $programParticipationFileInfo = new ProgramParticipationFileInfo($programParticipation, $id, $fileInfo);
        $this->programParticipationFileInfoRepository->add($programParticipationFileInfo);
        return $programParticipationFileInfo;
    }

}
