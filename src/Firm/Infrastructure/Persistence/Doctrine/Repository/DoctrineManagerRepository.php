<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\ManagerRepository;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Task\MeetingInitiator\UserRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;
use Resources\Uuid;

class DoctrineManagerRepository extends DoctrineEntityRepository implements ManagerRepository, UserRepository
{

    public function add(Manager $manager): void
    {
        $em = $this->getEntityManager();
        $em->persist($manager);
        $em->flush();
    }

    public function isEmailAvailable(string $firmId, string $email): bool
    {
        $params = [
            "firmId" => $firmId,
            "email" => $email,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('manager.email', ":email"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        return empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $managerId): Manager
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aManagerOfId(string $managerId): Manager
    {
        $manager = $this->findOneBy(["id" => $managerId]);
        if (empty($manager)) {
            $errorDetail = "not found: manager not found";
            throw RegularException::forbidden($errorDetail);
        }
        return $manager;
    }

    public function aManagerInFirm(string $firmId, string $managerId): Manager
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aUserOfId(string $id): CanAttendMeeting
    {
        return $this->findOneByIdOrDie($id, 'manager');
    }

}
