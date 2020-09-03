<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\ClientParticipantRepository,
    Domain\Model\ClientParticipant
};
use Resources\Exception\RegularException;

class DoctrineClientParticipantRepository extends EntityRepository implements ClientParticipantRepository
{
    
    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
        ];
        
        $qb = $this->createQueryBuilder('clientParticipant');
        $qb->select('clientParticipant')
                ->andWhere($qb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->andWhere($qb->expr()->eq('client.firmId', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client participant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
