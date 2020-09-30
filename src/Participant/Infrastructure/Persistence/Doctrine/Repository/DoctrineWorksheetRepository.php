<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet,
    Domain\Model\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository
{

    public function aWorksheetBelongsToClientParticipant(string $firmId, string $clientId,
            string $programParticipationId, string $worksheetId): Worksheet
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetBelongsToUserParticipant(string $userId, string $programParticipationId,
            string $worksheetId): Worksheet
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('tParticipant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->leftJoin('userParticipant.participant', 'tParticipant')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(Worksheet $worksheet): void
    {
        $em = $this->getEntityManager();
        $em->persist($worksheet);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function ofId(string $worksheetId): Worksheet
    {
        $params = [
            "worksheetId" => $worksheetId,
        ];
        
        $qb = $this->createQueryBuilder("worksheet");
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq("worksheet.id", ":worksheetId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
