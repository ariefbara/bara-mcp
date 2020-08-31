<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Client\ProgramParticipationRepository,
    Domain\Model\Firm\Client\ClientParticipant
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientParticipantRepository extends EntityRepository implements ProgramParticipationRepository
{

    public function all(string $firmId, string $clientId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
        ];

        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program participation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
