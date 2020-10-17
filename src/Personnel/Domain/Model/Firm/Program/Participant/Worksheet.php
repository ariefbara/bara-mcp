<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use Personnel\Domain\Model\Firm\ {
    Personnel\AssetBelongsToParticipantInProgram,
    Program\Participant
};

class Worksheet implements AssetBelongsToParticipantInProgram
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
     * @var bool
     */
    protected $removed;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
    }

    public function belongsToParticipantInProgram(string $programId): bool
    {
        return $this->participant->programEquals($programId);
    }

}
