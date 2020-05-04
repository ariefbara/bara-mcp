<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation;
use Shared\Domain\Model\FileInfo;

class ParticipantFileInfo
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

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

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function __construct(ProgramParticipation $programParticipation, string $id, FileInfo $fileInfo)
    {
        $this->programParticipation = $programParticipation;
        $this->id = $id;
        $this->fileInfo = $fileInfo;
        $this->removed = false;
    }

}
