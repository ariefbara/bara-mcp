<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ParticipantProfileRepository;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantProfileRepository extends EntityRepository implements ParticipantProfileRepository
{

    public function aParticipantProfileInProgram(string $firmId, string $programId, string $participantProfileId): ParticipantProfile
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantProfileId" => $participantProfileId,
        ];
        
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->andWhere($qb->expr()->eq("participantProfile.id", ":participantProfileId"))
                ->leftJoin("participantProfile.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1)
                ->setParameters($params);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProfilesBelongsToParticipantInProgram(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];
        
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
