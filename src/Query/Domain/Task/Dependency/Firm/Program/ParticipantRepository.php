<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function allActiveIndividualAndTeamProgramParticipationBelongsToClient(string $clientId);

    public function allProgramParticipantsInFirm(string $firmId, ParticipantFilter $filter);

    public function aProgramParticipantInFirm(string $firmId, string $id): Participant;

    public function summaryOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel(
            string $personnelId, int $page, int $pageSize, string $orderType = "DESC");

    public function countOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel(string $personnelId);

    public function summaryListInAllProgramsCoordinatedByPersonnel(
            string $personnelId, ParticipantSummaryListFilterForCoordinator $filter);

    public function listOfParticipantInAllProgramCoordinatedByPersonnel(string $personnelId,
            ParticipantListFilter $filter);

    public function listOfParticipantInAllProgramConsultedByPersonnel(string $personnelId,
            ParticipantListFilter $filter);
}
