<?php

namespace Bara\Application\Service;

class FirmSuspend
{
    protected $firmRepository;
    
    function __construct(FirmRepository $firmRepository)
    {
        $this->firmRepository = $firmRepository;
    }
    
    public function execute($firmId): void
    {
        $this->firmRepository->ofId($firmId)
            ->suspend();
        $this->firmRepository->update();
    }

}
