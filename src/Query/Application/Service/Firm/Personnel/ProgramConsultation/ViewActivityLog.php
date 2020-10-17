<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultation;

class ViewActivityLog
{

    /**
     *
     * @var ConsultantActivityLogRepository
     */
    protected $consultantActivityLogRepository;

    public function __construct(ConsultantActivityLogRepository $consultantActivityLogRepository)
    {
        $this->consultantActivityLogRepository = $consultantActivityLogRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param string $programConsultationId
     * @param int $page
     * @param int $pageSize
     * @return ConsultantActivityLog[]
     */
    public function showAll(string $personnelId, string $programConsultationId, int $page, int $pageSize)
    {
        return $this->consultantActivityLogRepository
                        ->allActivityLogsBelongsToConsultant($personnelId, $programConsultationId, $page, $pageSize);
    }

}
