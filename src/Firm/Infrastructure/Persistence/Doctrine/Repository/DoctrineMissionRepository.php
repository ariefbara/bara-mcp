<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\MissionRepository as InterfaceForManager;
use Firm\Domain\Model\Firm\Program\Mission;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineMissionRepository extends EntityRepository implements MissionRepository, InterfaceForManager
{

    public function add(Mission $mission): void
    {
        $em = $this->getEntityManager();
        $em->persist($mission);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(ProgramCompositionId $programCompositionId, string $missionId): Mission
    {
        $qb = $this->createQueryBuilder('mission');
        $qb->select('mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->setParameter('missionId', $missionId)
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId())
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: mission not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aMissionOfId(string $missionId): Mission
    {
        $mission = $this->findOneBy([
            "id" => $missionId,
        ]);
        if (empty($mission)) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
        return $mission;
    }

}
