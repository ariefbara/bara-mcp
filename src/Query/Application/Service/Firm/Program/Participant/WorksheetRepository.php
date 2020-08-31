<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Application\Service\Firm\Program\ClientParticipant\WorksheetRepository as InterfaceForClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet
};

interface WorksheetRepository extends InterfaceForClientParticipant
{

//    public function ofId(ParticipantCompositionId $participantCompositionId, string $worksheetId): Worksheet;
//
//    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize);
}
