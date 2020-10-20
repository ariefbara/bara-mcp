<?php

namespace Query\Application\Service\Firm\Program;

class ViewConsultantSummary
{
    /**
     *
     * @var ConsultantSummaryRepository
     */
    protected $consultantSummaryRepository;
    
    public function __construct(ConsultantSummaryRepository $consultantSummaryRepository)
    {
        $this->consultantSummaryRepository = $consultantSummaryRepository;
    }
    
    public function showAll(string $programId, int $page, int $pageSize)
    {
        return $this->consultantSummaryRepository->allConsultantSummaryInProgram($programId, $page, $pageSize);
    }
    
    public function getTotalActiveConsultant(string $programId): int
    {
        return $this->consultantSummaryRepository->getTotalActiveConsultantInProgram($programId);
    }

}
