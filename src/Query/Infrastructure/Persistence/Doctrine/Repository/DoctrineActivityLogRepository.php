<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Firm\Program\Participant\ActivityLogRepository;
use Query\Application\Service\Participant\ActivityLogRepository as ActivityLogRepository3;
use Query\Application\Service\TeamMember\ActivityLogRepository as ActivityLogRepository2;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog;
use Query\Domain\Model\Firm\Program\Participant\ViewLearningMaterialActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog;
use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityLogRepository extends EntityRepository implements ActivityLogRepository, ActivityLogRepository2, ActivityLogRepository3
{

    public function allActivityLogsInParticipantOfProgram(
            string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->eq("cr_participant.id", ":participantId"));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->eq("cs_participant.id", ":participantId"));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->eq("wk_participant.id", ":participantId"));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->eq("cm_participant.id", ":participantId"));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->eq("lm_participant.id", ":participantId"));

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allActivityLogsInProgramParticipationOfClient(
            string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->in("cr_participant.id", $this->getParticipantDqlOfClient("cr")));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->in("cs_participant.id", $this->getParticipantDqlOfClient("cs")));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->in("wk_participant.id", $this->getParticipantDqlOfClient("wk")));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->in("cm_participant.id", $this->getParticipantDqlOfClient("cm")));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->in("lm_participant.id", $this->getParticipantDqlOfClient("lm")));

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allActivityLogInProgramParticipationOfTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize)
    {
        $params = [
            "teamId" => $teamId,
            "programParticipationId" => $teamProgramParticipationId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->in("cr_participant.id", $this->getParticipantDqlOfTeam("cr")));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->in("cs_participant.id", $this->getParticipantDqlOfTeam("cs")));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->in("wk_participant.id", $this->getParticipantDqlOfTeam("wk")));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->in("cm_participant.id", $this->getParticipantDqlOfTeam("cm")));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->in("lm_participant.id", $this->getParticipantDqlOfTeam("lm")));

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allActivityLogsInProgramParticipationOfUser(
            string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->in("cr_participant.id", $this->getParticipantDqlOfUser("cr")));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->in("cs_participant.id", $this->getParticipantDqlOfUser("cs")));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->in("wk_participant.id", $this->getParticipantDqlOfUser("wk")));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->in("cm_participant.id", $this->getParticipantDqlOfUser("cm")));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->in("lm_participant.id", $this->getParticipantDqlOfUser("lm")));

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    protected function getActivityLogQbOfConsultationRequest(): QueryBuilder
    {
        $activityLogQb = $this->getEntityManager()->createQueryBuilder();
        $activityLogQb->select("cr_activityLog.id")
                ->from(ConsultationRequestActivityLog::class, "consultationRequestActivityLog")
                ->leftJoin("consultationRequestActivityLog.activityLog", "cr_activityLog")
                ->leftJoin("consultationRequestActivityLog.consultationRequest", "consultationRequest")
                ->leftJoin("consultationRequest.participant", "cr_participant");
        return $activityLogQb;
    }

    protected function getActivityLogQbOfConsultationSession(): QueryBuilder
    {
        $activityLogQb = $this->getEntityManager()->createQueryBuilder();
        $activityLogQb->select("cs_activityLog.id")
                ->from(ConsultationSessionActivityLog::class, "consultationSessionActivityLog")
                ->leftJoin("consultationSessionActivityLog.activityLog", "cs_activityLog")
                ->leftJoin("consultationSessionActivityLog.consultationSession", "consultationSession")
                ->leftJoin("consultationSession.participant", "cs_participant");
        return $activityLogQb;
    }

    protected function getActivityLogQbOfWorksheet(): QueryBuilder
    {
        $activityLogQb = $this->getEntityManager()->createQueryBuilder();
        $activityLogQb->select("wk_activityLog.id")
                ->from(WorksheetActivityLog::class, "worksheetActivityLog")
                ->leftJoin("worksheetActivityLog.activityLog", "wk_activityLog")
                ->leftJoin("worksheetActivityLog.worksheet", "worksheet")
                ->leftJoin("worksheet.participant", "wk_participant");
        return $activityLogQb;
    }

    protected function getActivityLogQbOfComment(): QueryBuilder
    {
        $activityLogQb = $this->getEntityManager()->createQueryBuilder();
        $activityLogQb->select("cm_activityLog.id")
                ->from(CommentActivityLog::class, "commentActivityLog")
                ->leftJoin("commentActivityLog.activityLog", "cm_activityLog")
                ->leftJoin("commentActivityLog.comment", "comment")
                ->leftJoin("comment.worksheet", "cm_worksheet")
                ->leftJoin("cm_worksheet.participant", "cm_participant");
        return $activityLogQb;
    }

    protected function getActivityLogQbOfViewLearningMaterial(): QueryBuilder
    {
        $activityLogQb = $this->getEntityManager()->createQueryBuilder();
        $activityLogQb->select("lm_activityLog.id")
                ->from(ViewLearningMaterialActivityLog::class, "vieLearningMaterialActivityLog")
                ->leftJoin("vieLearningMaterialActivityLog.activityLog", "lm_activityLog")
                ->leftJoin("vieLearningMaterialActivityLog.participant", "lm_participant");
        return $activityLogQb;
    }

    protected function getParticipantDqlOfUser(string $prefix)
    {
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("{$prefix}_programParticipation.id")
                ->from(UserParticipant::class, "{$prefix}_userProgramParticipation")
                ->andWhere($participantQb->expr()->eq("{$prefix}_userProgramParticipation.id", ":programParticipationId"))
                ->leftJoin("{$prefix}_userProgramParticipation.user", "{$prefix}_user")
                ->andWhere($participantQb->expr()->eq("{$prefix}_user.id", ":userId"))
                ->leftJoin("{$prefix}_userProgramParticipation.participant", "{$prefix}_programParticipation")
                ->setMaxResults(1);
        return $participantQb->getDQL();
    }

    protected function getParticipantDqlOfClient(string $prefix)
    {
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("{$prefix}_programParticipation.id")
                ->from(ClientParticipant::class, "{$prefix}_clientProgramParticipation")
                ->andWhere($participantQb->expr()->eq("{$prefix}_clientProgramParticipation.id",
                                ":programParticipationId"))
                ->leftJoin("{$prefix}_clientProgramParticipation.client", "{$prefix}_client")
                ->andWhere($participantQb->expr()->eq("{$prefix}_client.id", ":clientId"))
                ->leftJoin("{$prefix}_clientProgramParticipation.participant", "{$prefix}_programParticipation")
                ->setMaxResults(1);
        return $participantQb->getDQL();
    }

    protected function getParticipantDqlOfTeam(string $prefix)
    {
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("{$prefix}_programParticipation.id")
                ->from(TeamProgramParticipation::class, "{$prefix}_teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("{$prefix}_teamProgramParticipation.id", ":programParticipationId"))
                ->leftJoin("{$prefix}_teamProgramParticipation.team", "{$prefix}_team")
                ->andWhere($participantQb->expr()->eq("{$prefix}_team.id", ":teamId"))
                ->leftJoin("{$prefix}_teamProgramParticipation.programParticipation", "{$prefix}_programParticipation")
                ->setMaxResults(1);
        return $participantQb->getDQL();
    }

    public function allTeamSharedActivityLogsInProgram(string $memberId, string $teamId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "memberId" => $memberId,
            "teamId" => $teamId,
            "programParticipationId" => $participantId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->in("cr_participant.id", $this->getParticipantDqlOfTeam("cr")));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->in("cs_participant.id", $this->getParticipantDqlOfTeam("cs")));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->in("wk_participant.id", $this->getParticipantDqlOfTeam("wk")));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->in("cm_participant.id", $this->getParticipantDqlOfTeam("cm")));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->in("lm_participant.id", $this->getParticipantDqlOfTeam("lm")));
        
        $teamMemberLogQB = $this->getEntityManager()->createQueryBuilder();
        $teamMemberLogQB->select('_f_activityLog.id')
                ->from(TeamMemberActivityLog::class, '_f_teamMemberLog')
                ->leftJoin('_f_teamMemberLog.member', '_f_member')
                ->andWhere($teamMemberLogQB->expr()->eq('_f_member.id', ':memberId'))
                ->leftJoin('_f_teamMemberLog.activityLog', '_f_activityLog');

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->andWhere($qb->expr()->notIn('activityLog.id', $teamMemberLogQB->getDQL()))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allMemberActivityLogsInProgram(string $memberId, string $teamId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "memberId" => $memberId,
            "teamId" => $teamId,
            "programParticipationId" => $participantId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->in("cr_participant.id", $this->getParticipantDqlOfTeam("cr")));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->in("cs_participant.id", $this->getParticipantDqlOfTeam("cs")));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->in("wk_participant.id", $this->getParticipantDqlOfTeam("wk")));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->in("cm_participant.id", $this->getParticipantDqlOfTeam("cm")));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->in("lm_participant.id", $this->getParticipantDqlOfTeam("lm")));
        
        $teamMemberLogQB = $this->getEntityManager()->createQueryBuilder();
        $teamMemberLogQB->select('_f_activityLog.id')
                ->from(TeamMemberActivityLog::class, '_f_teamMemberLog')
                ->leftJoin('_f_teamMemberLog.member', '_f_member')
                ->andWhere($teamMemberLogQB->expr()->eq('_f_member.id', ':memberId'))
                ->leftJoin('_f_teamMemberLog.activityLog', '_f_activityLog');

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->andWhere($qb->expr()->in('activityLog.id', $teamMemberLogQB->getDQL()))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allParticipantActivityLogs(string $participantId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->eq("cr_participant.id", ":participantId"));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->eq("cs_participant.id", ":participantId"));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->eq("wk_participant.id", ":participantId"));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->eq("cm_participant.id", ":participantId"));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->eq("lm_participant.id", ":participantId"));
        
        $consultantLogQB = $this->getEntityManager()->createQueryBuilder();
        $consultantLogQB->select('_f_activityLog.id')
                ->from(ConsultantActivityLog::class, '_f_consultantLog')
                ->leftJoin('_f_consultantLog.activityLog', '_f_activityLog');

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->andWhere($qb->expr()->notIn('activityLog.id', $consultantLogQB->getDQL()))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allSharedActivityLog(string $participantId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantId,
        ];

        $crQb = $this->getActivityLogQbOfConsultationRequest();
        $crQb->andWhere($crQb->expr()->eq("cr_participant.id", ":participantId"));

        $csQb = $this->getActivityLogQbOfConsultationSession();
        $csQb->andWhere($csQb->expr()->eq("cs_participant.id", ":participantId"));

        $wkQb = $this->getActivityLogQbOfWorksheet();
        $wkQb->andWhere($wkQb->expr()->eq("wk_participant.id", ":participantId"));

        $cmQb = $this->getActivityLogQbOfComment();
        $cmQb->andWhere($cmQb->expr()->eq("cm_participant.id", ":participantId"));

        $lmQb = $this->getActivityLogQbOfViewLearningMaterial();
        $lmQb->andWhere($lmQb->expr()->eq("lm_participant.id", ":participantId"));
        
        $consultantLogQB = $this->getEntityManager()->createQueryBuilder();
        $consultantLogQB->select('_f_activityLog.id')
                ->from(ConsultantActivityLog::class, '_f_consultantLog')
                ->leftJoin('_f_consultantLog.activityLog', '_f_activityLog');

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in("activityLog.id", $crQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $csQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $wkQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $cmQb->getDQL()),
                                $qb->expr()->in("activityLog.id", $lmQb->getDQL())
                ))
                ->andWhere($qb->expr()->in('activityLog.id', $consultantLogQB->getDQL()))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
