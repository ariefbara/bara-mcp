<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantFileInfoRepository extends DoctrineEntityRepository implements ParticipantFileInfoRepository
{

    public function add(ParticipantFileInfo $participantFileInfo): void
    {
        $this->persist($participantFileInfo);
    }

    public function ofId(string $id): ParticipantFileInfo
    {
        return $this->findOneByIdOrDie($id, 'participant file info');
    }

}
