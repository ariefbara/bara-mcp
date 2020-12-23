<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\UserParticipantRepository;
use Participant\Application\Service\User\UserParticipantRepository as InterfaceForUser;
use Participant\Domain\Model\UserParticipant;
use Resources\Exception\RegularException;

class DoctrineUserParticipantRepository extends EntityRepository implements UserParticipantRepository, InterfaceForUser
{
    
    public function ofId(string $userId, string $userParticipantId): UserParticipant
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
        ];
        
        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->andWhere($qb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->andWhere($qb->expr()->eq('userParticipant.userId', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user participant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aUserParticipant(string $userId, string $programParticipationId): UserParticipant
    {
        $userParticipant = $this->findOneBy([
            "id" => $programParticipationId,
            "userId" => $userId
        ]);
        if (empty($userParticipant)) {
            $errorDetail = "not found: user participant not found";
            throw RegularException::notFound($errorDetail);
        }
        return $userParticipant;
    }

}
