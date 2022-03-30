<?php

namespace Firm\Application\Service\Firm;

use Firm\Application\Service\FirmRepository;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\ProgramData;

class ProgramAdd
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     * 
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    public function __construct(
            ProgramRepository $programRepository, FirmRepository $firmRepository,
            FirmFileInfoRepository $firmFileInfoRepository)
    {
        $this->programRepository = $programRepository;
        $this->firmRepository = $firmRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
    }

    public function execute(string $firmId, ProgramRequest $programRequest): string
    {
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->programRepository->nextIdentity();
        
        $illustration = empty($programRequest->getFirmFileInfoIdOfIllustration())? 
                null: $this->firmFileInfoRepository->ofId($programRequest->getFirmFileInfoIdOfIllustration());
        $programData = new ProgramData(
                $programRequest->getName(), $programRequest->getDescription(), $programRequest->getStrictMissionOrder(),
                $illustration, $programRequest->getProgramType(), $programRequest->getPrice(), 
                $programRequest->getAutoAccept());
        foreach ($programRequest->getParticipantTypes() as $participantType) {
            $programData->addParticipantType($participantType);
        }
        
        $program = new Program($firm, $id, $programData);
        $this->programRepository->add($program);
        
        return $id;
    }

}
