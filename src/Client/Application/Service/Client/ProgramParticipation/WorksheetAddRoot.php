<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipationRepository,
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet
};
use Shared\Domain\Model\FormRecordData;

class WorksheetAddRoot
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(WorksheetRepository $worksheetRepository,
            ProgramParticipationRepository $programParticipationRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->programParticipationRepository = $programParticipationRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $clientId, string $programParticipationId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository->aMissionInProgramWhereClientParticipate($clientId, $programParticipationId, $missionId);
        $formRecord = $mission->createWorksheetFormRecord($id, $formRecordData);

        $worksheet = Worksheet::createRootWorksheet($programParticipation, $id, $name, $mission, $formRecord);
        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
