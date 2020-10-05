<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\{
    Participant\WorksheetRepository,
    UserParticipantRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitRootWorksheet
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(
            WorksheetRepository $worksheetRepository, UserParticipantRepository $userParticipantRepository,
            MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $userId, string $userParticipantId, string $missionId, string $name, FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository
                ->aMissionInProgramWhereUserParticipate($userId, $userParticipantId, $missionId);

        $worksheet = $this->userParticipantRepository->ofId($userId, $userParticipantId)
                ->createRootWorksheet($id, $name, $mission, $formRecordData);

        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
