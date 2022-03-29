<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ProgramData;

class ProgramUpdate
{

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    public function __construct(ProgramRepository $programRepository, FirmFileInfoRepository $firmFileInfoRepository)
    {
        $this->programRepository = $programRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
    }

    public function execute(string $firmId, string $programId, ProgramRequest $programRequest): void
    {
        $illustration = empty($programRequest->getFirmFileInfoIdOfIllustration()) ?
                null : $this->firmFileInfoRepository->ofId($programRequest->getFirmFileInfoIdOfIllustration());
        $programData = new ProgramData(
                $programRequest->getName(), $programRequest->getDescription(), $programRequest->getStrictMissionOrder(),
                $illustration, $programRequest->getProgramType(), $programRequest->getPrice());
        foreach ($programRequest->getParticipantTypes() as $participantType) {
            $programData->addParticipantType($participantType);
        }

        $this->programRepository->ofId($firmId, $programId)
                ->update($programData);
        $this->programRepository->update();
    }

}
