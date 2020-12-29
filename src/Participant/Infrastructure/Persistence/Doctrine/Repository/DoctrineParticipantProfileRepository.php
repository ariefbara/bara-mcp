<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\Application\Service\ParticipantProfileRepository;
use Participant\Domain\Model\Participant\ParticipantProfile;

class DoctrineParticipantProfileRepository extends EntityRepository implements ParticipantProfileRepository
{

    public function aParticipantProfileCorrespondWithProgramsProfileForm(
            string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        $params = [
            "participantId" => $programParticipationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", 'false'))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ':participantId'))
                ->leftJoin("participantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ':programsProfileFormId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw \Resources\Exception\RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
