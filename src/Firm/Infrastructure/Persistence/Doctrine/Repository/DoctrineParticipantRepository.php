<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Coordinator\ParticipantRepository as InterfaceForCoordinator;
use Firm\Application\Service\Firm\Program\ParticipantRepository;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Task\Dependency\Firm\Program\ParticipantRepository as ParticipantRepository2;
use Firm\Domain\Task\MeetingInitiator\UserRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantRepository extends DoctrineEntityRepository implements ParticipantRepository, InterfaceForCoordinator, UserRepository, ParticipantRepository2
{
    
    public function ofId(string $participantId): Participant
    {
        $participant = $this->findOneBy(["id" => $participantId]);
        if (empty($participant)) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
        return $participant;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aUserOfId(string $id): CanAttendMeeting
    {
        return $this->findOneByIdOrDie($id, 'participant');
    }

}
