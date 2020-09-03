<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\{
    ClientParticipantRepository,
    Participant\WorksheetRepository
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
            string $firmId, string $clientId, string $programParticipationId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository
                ->aMissionInProgramWhereClientParticipate($firmId, $clientId, $programParticipationId, $missionId);
        $formRecord = $mission->createWorksheetFormRecord($id, $formRecordData);

        $worksheet = $this->clientParticipantRepository
                ->ofId($firmId, $clientId, $programParticipationId)
                ->createRootWorksheet($id, $name, $mission, $formRecord);

        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
