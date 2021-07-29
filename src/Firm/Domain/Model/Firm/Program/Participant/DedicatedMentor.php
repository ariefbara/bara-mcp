<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;

class DedicatedMentor implements AssetInProgram
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
     * @var Consultant
     */
    protected $consultant;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Participant $participant, string $id, Consultant $consultant)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultant = $consultant;
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->cancelled = false;
        $this->assertConsultantActive();
    }
    protected function assertConsultantActive(): void
    {
        if (!$this->consultant->isActive()) {
            throw RegularException::forbidden('forbidden: unable to dedicate inactive consultant');
        }
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->participant->belongsToProgram($program);
    }

    public function consultantEquals(Consultant $consultant): bool
    {
        return $this->consultant === $consultant;
    }

    public function cancel(): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: dedicated mentor already cancelled');
        }
        $this->cancelled = true;
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public function reassign(): void
    {
        if ($this->cancelled) {
            $this->cancelled = false;
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
            $this->assertConsultantActive();
        }
    }
    
    public function isActiveAssignment(): bool
    {
        return !$this->cancelled;
    }
    
    public function inviteParticipantToMeeting($meeting): void
    {
        $this->participant->inviteToMeeting($meeting);
    }

}
