<?php

namespace Firm\Application\Service\Firm\Program;

class ConsultantRemove
{
    protected $consultantRepository;
    
    function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }
    
    public function execute(ProgramCompositionId $programCompositionId, $consultantId): void
    {
        $this->consultantRepository->ofId($programCompositionId, $consultantId)
            ->remove();
        $this->consultantRepository->update();
    }

}
