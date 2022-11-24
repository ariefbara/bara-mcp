<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Resources\Exception\RegularException;

class DoctrineParticipantFileInfoRepository extends EntityRepository implements ParticipantFileInfoRepository
{

    public function aParticipantFileInfoBelongsToParticipant(string $participantId, string $id): ParticipantFileInfo
    {
        $parameters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('participantFileInfo');
        $qb->select('participantFileInfo')
                ->andWhere($qb->expr()->eq('participantFileInfo.id', ':id'))
                ->leftJoin('participantFileInfo.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('participant file not found');
        }
    }

}
