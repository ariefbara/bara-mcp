<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantAttendeeRepository extends DoctrineEntityRepository implements ParticipantAttendeeRepository
{
    
    public function ofId(string $id): ParticipantAttendee
    {
        return $this->findOneByIdOrDie($id, 'participant attendee');
    }

}
