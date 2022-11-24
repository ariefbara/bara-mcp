<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class ParticipantFileInfo
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var FileInfo
     */
    protected $fileInfo;

    public function getFullyQualifiedFileName(): string
    {
        return $this->fileInfo->getFullyQualifiedFileName();
    }

    public function __construct(Participant $participant, string $id, FileInfoData $fileInfoData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
    }

    //
    public function assertUsableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('unusable participant file, can only use own file');
        }
    }

}
