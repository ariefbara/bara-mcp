<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

interface DedicatedMentorRepository
{

    public function aDedicatedMentorInProgram(string $programId, string $dedicatedMentorId): DedicatedMentor;

    public function allDedicatedMentorsOfParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize, ?bool $cancelledStatus);

    public function allDedicatedMentorsOfConsultantInProgram(
            string $programId, string $consultantId, int $page, int $pageSize, ?bool $cancelledStatus);

    public function aDedicatedMentorBelongsToParticipant(string $participantId, string $dedicatedMentorId): DedicatedMentor;
    
    public function allDedicatedMentorsBelongsToParticipant(string $participantId, int $page, int $pageSize, ?bool $cancelledStatus);
    
    public function aDedicatedMentorBelongsToConsultant(string $consultantId, string $dedicatedMentorId): DedicatedMentor;
    
    public function allDedicatedMentorsBelongsToConsultant(string $consultantId, int $page, int $pageSize, ?bool $cancelledStatus);
}
