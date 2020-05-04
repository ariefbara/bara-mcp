<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Consultant;

class ConsultantView
{

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function showById(ProgramCompositionId $programCompositionId, string $consultantId): Consultant
    {
        return $this->consultantRepository->ofId($programCompositionId, $consultantId);
    }

    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Consultant[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->consultantRepository->all($programCompositionId, $page, $pageSize);
    }

}
