<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    QueryBuilder
};
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ActivityLogRepository,
    Application\Service\Firm\Program\Participant\ActivityLogRepository as ActivityLogRepository2,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Domain\Model\Firm\Program\Participant\ViewLearningMaterialActivityLog,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog,
    Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog,
    Domain\Model\Firm\Team\Member,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityLogRepository extends EntityRepository implements ActivityLogRepository, ActivityLogRepository2
{

//    public function allActivityLogsBelongsToTeamParticipantWhereClientIsMember(
//            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
//            int $pageSize)
//    {
//        $params = [
//            "firmId" => $firmId,
//            "clientId" => $clientId,
//            "teamMembershipId" => $teamMembershipId,
//            "teamProgramParticipationId" => $teamProgramParticipationId,
//        ];
//
//        $qb = $this->createQueryBuilder("activityLog");
//        $qb->select("activityLog")
//                ->andWhere($qb->expr()->orX(
//                                $qb->expr()->in("activityLog.id", $this->getConsultationRequestActivityDQL()),
//                                $qb->expr()->in("activityLog.id", $this->getConsultationSessionActivityDQL()),
//                                $qb->expr()->in("activityLog.id", $this->getWorksheetActivityDQL()),
//                                $qb->expr()->in("activityLog.id", $this->getCommentActivityDQL()),
//                                $qb->expr()->in("activityLog.id", $this->getAccessingLearningMaterialActivtyDQL())
//                ))
//                ->orderBy("activityLog.occuredTime", "DESC")
//                ->setParameters($params);
//
//        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
//    }
//
//    protected function getConsultationRequestActivityDQL()
//    {
//        $teamQb = $this->getEntityManager()->createQueryBuilder();
//        $teamQb->select("t_cr_team.id")
//                ->from(Member::class, "cr_teamMembership")
//                ->andWhere($teamQb->expr()->eq("cr_teamMembership.id", ":teamMembershipId"))
//                ->leftJoin("cr_teamMembership.team", "t_cr_team")
//                ->leftJoin("cr_teamMembership.client", "cr_client")
//                ->andWhere($teamQb->expr()->eq("cr_client.id", ":clientId"))
//                ->leftJoin("cr_client.firm", "cr_firm")
//                ->andWhere($teamQb->expr()->eq("cr_firm.id", ":firmId"))
//                ->setMaxResults(1);
//
//        $participantQb = $this->getEntityManager()->createQueryBuilder();
//        $participantQb->select("cr_programParticipation.id")
//                ->from(TeamProgramParticipation::class, "cr_teamProgramParticipation")
//                ->andWhere($participantQb->expr()->eq("cr_teamProgramParticipation.id", ":teamProgramParticipationId"))
//                ->leftJoin("cr_teamProgramParticipation.programParticipation", "cr_programParticipation")
//                ->leftJoin("cr_teamProgramParticipation.team", "cr_team")
//                ->andWhere($participantQb->expr()->in("cr_team.id", $teamQb->getDQL()))
//                ->setMaxResults(1);
//
//        $consultationRequestActivityLogQb = $this->getEntityManager()->createQueryBuilder();
//        $consultationRequestActivityLogQb->select("cr_activityLog.id")
//                ->from(ConsultationRequestActivityLog::class, "consultationRequestActivityLog")
//                ->leftJoin("consultationRequestActivityLog.activityLog", "cr_activityLog")
//                ->leftJoin("consultationRequestActivityLog.consultationRequest", "consultationRequest")
//                ->leftJoin("consultationRequest.participant", "cr_participant")
//                ->andWhere($consultationRequestActivityLogQb->expr()->in("cr_participant.id", $participantQb->getDQL()));
//
//        return $consultationRequestActivityLogQb->getDQL();
//    }
//
//    protected function getConsultationSessionActivityDQL()
//    {
//        $teamQb = $this->getEntityManager()->createQueryBuilder();
//        $teamQb->select("t_cs_team.id")
//                ->from(Member::class, "cs_teamMembership")
//                ->andWhere($teamQb->expr()->eq("cs_teamMembership.id", ":teamMembershipId"))
//                ->leftJoin("cs_teamMembership.team", "t_cs_team")
//                ->leftJoin("cs_teamMembership.client", "cs_client")
//                ->andWhere($teamQb->expr()->eq("cs_client.id", ":clientId"))
//                ->leftJoin("cs_client.firm", "cs_firm")
//                ->andWhere($teamQb->expr()->eq("cs_firm.id", ":firmId"))
//                ->setMaxResults(1);
//
//        $participantQb = $this->getEntityManager()->createQueryBuilder();
//        $participantQb->select("cs_programParticipation.id")
//                ->from(TeamProgramParticipation::class, "cs_teamProgramParticipation")
//                ->andWhere($participantQb->expr()->eq("cs_teamProgramParticipation.id", ":teamProgramParticipationId"))
//                ->leftJoin("cs_teamProgramParticipation.programParticipation", "cs_programParticipation")
//                ->leftJoin("cs_teamProgramParticipation.team", "cs_team")
//                ->andWhere($participantQb->expr()->in("cs_team.id", $teamQb->getDQL()))
//                ->setMaxResults(1);
//
//        $consultationSessionActivityLogQb = $this->getEntityManager()->createQueryBuilder();
//        $consultationSessionActivityLogQb->select("cs_activityLog.id")
//                ->from(ConsultationSessionActivityLog::class, "consultationSessionActivityLog")
//                ->leftJoin("consultationSessionActivityLog.activityLog", "cs_activityLog")
//                ->leftJoin("consultationSessionActivityLog.consultationSession", "consultationSession")
//                ->leftJoin("consultationSession.participant", "cs_participant")
//                ->andWhere($consultationSessionActivityLogQb->expr()->in("cs_participant.id", $participantQb->getDQL()));
//
//        return $consultationSessionActivityLogQb->getDQL();
//    }
//
//    protected function getWorksheetActivityDQL()
//    {
//        $teamQb = $this->getEntityManager()->createQueryBuilder();
//        $teamQb->select("t_wr_team.id")
//                ->from(Member::class, "wr_teamMembership")
//                ->andWhere($teamQb->expr()->eq("wr_teamMembership.id", ":teamMembershipId"))
//                ->leftJoin("wr_teamMembership.team", "t_wr_team")
//                ->leftJoin("wr_teamMembership.client", "wr_client")
//                ->andWhere($teamQb->expr()->eq("wr_client.id", ":clientId"))
//                ->leftJoin("wr_client.firm", "wr_firm")
//                ->andWhere($teamQb->expr()->eq("wr_firm.id", ":firmId"))
//                ->setMaxResults(1);
//
//        $participantQb = $this->getEntityManager()->createQueryBuilder();
//        $participantQb->select("wr_programParticipation.id")
//                ->from(TeamProgramParticipation::class, "wr_teamProgramParticipation")
//                ->andWhere($participantQb->expr()->eq("wr_teamProgramParticipation.id", ":teamProgramParticipationId"))
//                ->leftJoin("wr_teamProgramParticipation.programParticipation", "wr_programParticipation")
//                ->leftJoin("wr_teamProgramParticipation.team", "wr_team")
//                ->andWhere($participantQb->expr()->in("wr_team.id", $teamQb->getDQL()))
//                ->setMaxResults(1);
//
//        $worksheetActivityLogQb = $this->getEntityManager()->createQueryBuilder();
//        $worksheetActivityLogQb->select("wr_activityLog.id")
//                ->from(WorksheetActivityLog::class, "worksheetActivityLog")
//                ->leftJoin("worksheetActivityLog.activityLog", "wr_activityLog")
//                ->leftJoin("worksheetActivityLog.worksheet", "worksheet")
//                ->leftJoin("worksheet.participant", "wr_participant")
//                ->andWhere($worksheetActivityLogQb->expr()->in("wr_participant.id", $participantQb->getDQL()));
//
//        return $worksheetActivityLogQb->getDQL();
//    }
//
//    protected function getCommentActivityDQL()
//    {
//        $teamQb = $this->getEntityManager()->createQueryBuilder();
//        $teamQb->select("t_cm_team.id")
//                ->from(Member::class, "cm_teamMembership")
//                ->andWhere($teamQb->expr()->eq("cm_teamMembership.id", ":teamMembershipId"))
//                ->leftJoin("cm_teamMembership.team", "t_cm_team")
//                ->leftJoin("cm_teamMembership.client", "cm_client")
//                ->andWhere($teamQb->expr()->eq("cm_client.id", ":clientId"))
//                ->leftJoin("cm_client.firm", "cm_firm")
//                ->andWhere($teamQb->expr()->eq("cm_firm.id", ":firmId"))
//                ->setMaxResults(1);
//
//        $participantQb = $this->getEntityManager()->createQueryBuilder();
//        $participantQb->select("cm_programParticipation.id")
//                ->from(TeamProgramParticipation::class, "cm_teamProgramParticipation")
//                ->andWhere($participantQb->expr()->eq("cm_teamProgramParticipation.id", ":teamProgramParticipationId"))
//                ->leftJoin("cm_teamProgramParticipation.programParticipation", "cm_programParticipation")
//                ->leftJoin("cm_teamProgramParticipation.team", "cm_team")
//                ->andWhere($participantQb->expr()->in("cm_team.id", $teamQb->getDQL()))
//                ->setMaxResults(1);
//
//        $commentActivityLogQb = $this->getEntityManager()->createQueryBuilder();
//        $commentActivityLogQb->select("cm_activityLog.id")
//                ->from(CommentActivityLog::class, "commentActivityLog")
//                ->leftJoin("commentActivityLog.activityLog", "cm_activityLog")
//                ->leftJoin("commentActivityLog.comment", "comment")
//                ->leftJoin("comment.worksheet", "cm_worksheet")
//                ->leftJoin("cm_worksheet.participant", "cm_participant")
//                ->andWhere($commentActivityLogQb->expr()->in("cm_participant.id", $participantQb->getDQL()));
//
//        return $commentActivityLogQb->getDQL();
//    }
//
//    protected function getAccessingLearningMaterialActivtyDQL()
//    {
//        $teamQb = $this->getEntityManager()->createQueryBuilder();
//        $teamQb->select("t_lm_team.id")
//                ->from(Member::class, "lm_teamMembership")
//                ->andWhere($teamQb->expr()->eq("lm_teamMembership.id", ":teamMembershipId"))
//                ->leftJoin("lm_teamMembership.team", "t_lm_team")
//                ->leftJoin("lm_teamMembership.client", "lm_client")
//                ->andWhere($teamQb->expr()->eq("lm_client.id", ":clientId"))
//                ->leftJoin("lm_client.firm", "lm_firm")
//                ->andWhere($teamQb->expr()->eq("lm_firm.id", ":firmId"))
//                ->setMaxResults(1);
//
//        $participantQb = $this->getEntityManager()->createQueryBuilder();
//        $participantQb->select("lm_programParticipation.id")
//                ->from(TeamProgramParticipation::class, "lm_teamProgramParticipation")
//                ->andWhere($participantQb->expr()->eq("lm_teamProgramParticipation.id", ":teamProgramParticipationId"))
//                ->leftJoin("lm_teamProgramParticipation.programParticipation", "lm_programParticipation")
//                ->leftJoin("lm_teamProgramParticipation.team", "lm_team")
//                ->andWhere($participantQb->expr()->in("lm_team.id", $teamQb->getDQL()))
//                ->setMaxResults(1);
//
//        $viewLearningMaterialLogQb = $this->getEntityManager()->createQueryBuilder();
//        $viewLearningMaterialLogQb->select("lm_activityLog.id")
//                ->from(ViewLearningMaterialActivityLog::class, "viewLearningMaterialActivityLog")
//                ->leftJoin("viewLearningMaterialActivityLog.activityLog", "lm_activityLog")
//                ->leftJoin("viewLearningMaterialActivityLog.participant", "lm_participant")
//                ->andWhere($viewLearningMaterialLogQb->expr()->in("lm_participant.id", $participantQb->getDQL()));
//    }

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
                ->andWhere($participantQb->expr()->eq("{$prefix}_clientProgramParticipation.id", ":programParticipationId"))
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

    public function allActivityLogsBelongsToTeamParticipantWhereClientIsMember(string $firmId, string $clientId,
            string $teamMembershipId, string $teamProgramParticipationId, int $page, int $pageSize)
    {
        
    }

}
