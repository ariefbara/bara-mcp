<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Model\Firm\ParticipantTypes;

interface IProgramApplicant
{

    public function assertBelongsInFirm(Firm $firm): void;

    public function assertNoActiveParticipationOrOngoingRegistrationInProgram(Program $program): void;

    public function assertTypeIncludedIn(ParticipantTypes $participantTypes): void;

    public function getUserType(): string;

    public function addProgramParticipation(string $participantId, Participant $participant): void;

    public function addProgramRegistration(string $registrantId, Registrant $registrant): void;
}
