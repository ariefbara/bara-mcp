<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\RegistrantRepository;
use Query\Application\Service\Personnel\RegistrantRepository as InterfaceForPersonnel;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Registrant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class DoctrineRegistrantRepository extends EntityRepository implements RegistrantRepository, InterfaceForPersonnel
{

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->addOrderBy('registrant.registeredTime', 'ASC')
                ->setParameters($parameters);
        
        if (isset($concludedStatus)) {
            if($concludedStatus) {
                $status = [
                    RegistrationStatus::ACCEPTED,
                    RegistrationStatus::REJECTED,
                    RegistrationStatus::CANCELLED,
                ];
            } else {
                $status = [
                    RegistrationStatus::REGISTERED,
                    RegistrationStatus::SETTLEMENT_REQUIRED,
                ];
            }
            $qb->andWhere($qb->expr()->in('registrant.status.value', ':concludedStatus'))
                    ->setParameter('concludedStatus', $status);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $registrantId): Registrant
    {
        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
            "registrantId" => $registrantId,
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ":registrantId"))
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allRegistrantsAccessibleByPersonnel(
            string $personnelId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        $parameters = [
            "personnelId" => $personnelId,
        ];
        
        $coordinatorQB = $this->getEntityManager()->createQueryBuilder();
        $coordinatorQB->select('a_program.id')
                ->from(Coordinator::class, 'coordinator')
                ->andWhere($coordinatorQB->expr()->eq('coordinator.active', "true"))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($coordinatorQB->expr()->eq('personnel.id', ":personnelId"))
                ->leftJoin('coordinator.program', 'a_program');
        
        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->in('program.id', $coordinatorQB->getDQL()))
                ->addOrderBy('registrant.registeredTime', 'ASC')
                ->setParameters($parameters);
        
        if (isset($concludedStatus)) {
            if($concludedStatus) {
                $status = [
                    RegistrationStatus::ACCEPTED,
                    RegistrationStatus::REJECTED,
                    RegistrationStatus::CANCELLED,
                ];
            } else {
                $status = [
                    RegistrationStatus::REGISTERED,
                    RegistrationStatus::SETTLEMENT_REQUIRED,
                ];
            }
            $qb->andWhere($qb->expr()->in('registrant.status.value', ':concludedStatus'))
                    ->setParameter('concludedStatus', $status);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
