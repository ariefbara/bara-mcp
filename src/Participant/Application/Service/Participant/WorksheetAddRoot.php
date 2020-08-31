<?php

namespace Participant\Application\Service\Participant;

use Participant\{
    Application\Service\ClientParticipantRepository,
    Domain\Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class WorksheetAddRoot
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

    function __construct(WorksheetRepository $worksheetRepository,
            ClientParticipantRepository $clientParticipantRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository->ofId($firmId, $programId, $missionId);
        $formRecord = $mission->createWorksheetFormRecord($id, $formRecordData);

        $worksheet = $this->clientParticipantRepository
                ->ofId($firmId, $clientId, $programId)
                ->createRootWorksheet($id, $name, $mission, $formRecord);

        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
