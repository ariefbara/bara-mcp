<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use Resources\Application\Event\ContainEvents;

interface IProgramApplicant extends ContainEvents
{

    public function assertBelongsInFirm(Firm $firm): void;

    public function getUserType(): string;

    public function addProgramParticipation(string $participantId, Participant $participant): void;

    public function addProgramRegistration(string $registrantId, Registrant $registrant): void;
}
