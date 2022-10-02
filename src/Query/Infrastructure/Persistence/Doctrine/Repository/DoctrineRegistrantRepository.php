<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\RegistrantRepository;
use Query\Application\Service\Personnel\RegistrantRepository as InterfaceForPersonnel;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Task\Dependency\Firm\Program\RegistrantRepository as RegistrantRepository2;
use Query\Domain\Task\Dependency\PaginationFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class DoctrineRegistrantRepository extends EntityRepository implements RegistrantRepository, InterfaceForPersonnel, RegistrantRepository2
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
            if ($concludedStatus) {
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
            if ($concludedStatus) {
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

    public function aRegistrantInProgram(string $programId, string $id): Registrant
    {
        $parameters = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('registrant');
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ':id'))
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('registrant not found');
        }
    }

    public function allNewRegistrantManageableByPersonnel(string $personnelId, PaginationFilter $pagination)
    {
        $offset = $pagination->getPageSize() * ($pagination->getPage() - 1);
        $registeredStatus = RegistrationStatus::REGISTERED;

        $parameters = [
            "personnelId" => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT 
    Registrant.id, 
    COALESCE(_b.userName, _c.clientName, _d.teamName) name,
    Registrant.registeredTime,
    _a.coordinatorId,
    _a.programId,
    _a.programName
FROM Registrant
INNER JOIN (
    SELECT Coordinator.id coordinatorId, Program.id programId, Program.name programName
    FROM Coordinator
    INNER JOIN Personnel ON Personnel.id = Coordinator.Personnel_id
    INNER JOIN Program ON Program.id = Coordinator.Program_id
    WHERE Coordinator.active = true
        AND Coordinator.Personnel_id = :personnelId
)_a ON _a.programId = Registrant.Program_id
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserRegistrant.Registrant_id registrantId
    FROM UserRegistrant
        LEFT JOIN User ON User.id = UserRegistrant.User_id
)_b ON _b.registrantId = Registrant.id
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientRegistrant.Registrant_id registrantId
    FROM ClientRegistrant
        LEFT JOIN Client ON Client.id = ClientRegistrant.Client_id
)_c ON _c.registrantId = Registrant.id
LEFT JOIN (
    SELECT Team.name teamName, TeamRegistrant.Registrant_id registrantId
    FROM TeamRegistrant
        LEFT JOIN Team ON Team.id = TeamRegistrant.Team_id
)_d ON _d.registrantId = Registrant.id
WHERE Registrant.status = $registeredStatus
ORDER BY Registrant.registeredTime ASC
LIMIT {$offset}, {$pagination->getPageSize()}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->countOfAllNewRegistrantManageableByPersonnel($personnelId, $pagination),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }

    public function countOfAllNewRegistrantManageableByPersonnel(string $personnelId, PaginationFilter $pagination)
    {
        $registeredStatus = RegistrationStatus::REGISTERED;

        $parameters = [
            "personnelId" => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM Registrant
INNER JOIN (
    SELECT Coordinator.Program_id programId
    FROM Coordinator
    WHERE Coordinator.active = true
        AND Coordinator.Personnel_id = :personnelId
)_a ON _a.programId = Registrant.Program_id
WHERE Registrant.status = $registeredStatus
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
