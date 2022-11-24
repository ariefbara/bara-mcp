<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Resources\Exception\RegularException;
use SharedContext\Domain\Service\FileRepository;

class UploadFile implements ParticipantTask
{

    /**
     * 
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    /**
     * 
     * @var FileRepository
     */
    protected $fileRepository;

    public function __construct(
            ParticipantFileInfoRepository $participantFileInfoRepository, FileRepository $fileRepository)
    {
        $this->participantFileInfoRepository = $participantFileInfoRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param UploadFilePayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->uploadedFileInfoId = $this->participantFileInfoRepository->nextIdentity();
        $fileInfoData = $payload->getFileInfoData();
        $participantFileInfo = $participant->uploadFile($payload->uploadedFileInfoId, $fileInfoData);
        
        $fullyQualifiedFileName = $participantFileInfo->getFullyQualifiedFileName();
        if ($this->fileRepository->has($fullyQualifiedFileName)) {
            throw RegularException::conflict('file name already used');
        }
        $this->fileRepository->write($fullyQualifiedFileName, $payload->getContents());
        $this->participantFileInfoRepository->add($participantFileInfo);
    }

}
