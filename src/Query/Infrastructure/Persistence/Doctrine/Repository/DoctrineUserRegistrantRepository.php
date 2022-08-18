<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Auth\UserRegistrantRepository as InterfaceForAuthorization;
use Query\Application\Service\User\ProgramRegistrationRepository;
use Query\Domain\Model\User\UserRegistrant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class DoctrineUserRegistrantRepository extends EntityRepository implements ProgramRegistrationRepository, InterfaceForAuthorization
{

    public function aProgramRegistrationOfUser(string $userId, string $programRegistrationId): UserRegistrant
    {
        $params = [
            'userId' => $userId,
            'programRegistrationId' => $programRegistrationId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->andWhere($qb->expr()->eq('programRegistration.id', ':programRegistrationId'))
                ->leftJoin('programRegistration.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program registration not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProgramRegistrationsOfUser(string $userId, int $page, int $pageSize)
    {
        $params = [
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('programRegistration');
        $qb->select('programRegistration')
                ->leftJoin('programRegistration.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfUnconcludedRegistrationToProgram(string $userId, string $firmId, string $programId): bool
    {
        $params = [
            "userId" => $userId,
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        
        $unconcludedStatus = [
            RegistrationStatus::REGISTERED,
            RegistrationStatus::SETTLEMENT_REQUIRED,
        ];
        
        $qb = $this->createQueryBuilder("userRegistrant");
        $qb->select("1")
                ->leftJoin("userRegistrant.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.status.value", ":status"))
                ->setParameter('status', $unconcludedStatus)
                ->leftJoin("registrant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->leftJoin("userRegistrant.user", "user")
                ->andWhere($qb->expr()->eq("user.id", ":userId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

}
