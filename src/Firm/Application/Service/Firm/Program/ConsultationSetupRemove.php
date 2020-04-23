<?php

namespace Firm\Application\Service\Firm\Program;

class ConsultationSetupRemove
{
    protected $consultationSetupRepository;
    
    function __construct(ConsultationSetupRepository $consultationSetupRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
    }
    
    public function execute(ProgramCompositionId $programCompositionId, string $consultationSetupId): void
    {
        $this->consultationSetupRepository->ofId($programCompositionId, $consultationSetupId)
            ->remove();
        $this->consultationSetupRepository->update();
    }

}
