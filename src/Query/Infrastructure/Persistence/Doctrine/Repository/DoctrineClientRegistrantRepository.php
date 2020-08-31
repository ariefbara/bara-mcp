<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Client\ProgramRegistrationRepository,
    Domain\Model\Firm\Client\ClientRegistrant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientRegistrantRepository extends EntityRepository implements ProgramRegistrationRepository
{

    public function all(string $firmId, string $clientId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->leftJoin('programRegistration.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programRegistrationId' => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
