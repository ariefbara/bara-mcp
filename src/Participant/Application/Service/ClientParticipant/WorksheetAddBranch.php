<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\{
    ClientParticipantRepository,
    Firm\Program\MissionRepository,
    Participant\WorksheetRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class WorksheetAddBranch
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(
            WorksheetRepository $worksheetRepository, ClientParticipantRepository $clientParticipantRepository,
            MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $missionId,
            string $name, FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();
        $parentWorksheet = $this->worksheetRepository->ofId($worksheetId);
        $mission = $this->missionRepository->ofId($missionId);

        $branch = $this->clientParticipantRepository->ofId($firmId, $clientId, $programParticipationId)
                ->submitBranchWorksheet($parentWorksheet, $id, $name, $mission, $formRecordData);

        $this->worksheetRepository->add($branch);
        return $id;
    }

}
