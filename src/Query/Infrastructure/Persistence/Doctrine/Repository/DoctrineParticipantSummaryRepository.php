<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\Firm\Program\ParticipantSummaryRepository;

class DoctrineParticipantSummaryRepository implements ParticipantSummaryRepository
{
    /**
     *
     * @var EntityManager
     */
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function allParticipantsSummaryInProgram(string $programId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _b.participantId, 
    COALESCE(_c.userName, _d.clientName, _e.teamName) participantName, 
    _b.totalCompletedMission,
    _a.totalMission,
    _b.lastCompletedTime,
    _b.lastMissionId,
    _b.lastMissionName
FROM (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Program_id = :programId
)_a
LEFT JOIN (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___a.totalCompletedMission, ___a.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___a
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___a.lastCompletedTime AND CM.Participant_id = ___a.Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __a.Mission_id
    WHERE Participant.active = true
)_b ON _b.programId = _a.programId
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', User.lastName) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_c ON _c.participantId = _b.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', Client.lastName) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_d ON _d.participantId = _b.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_e ON _e.participantId = _b.participantId
ORDER BY _b.totalCompletedMission DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalActiveParticipantInProgram(string $programId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(Participant.id) total
FROM Participant
WHERE Participant.Program_id = :programId
    AND Participant.active = true
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "programId" => $programId,
        ];
        $query->execute($params);
        
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }
    
}
