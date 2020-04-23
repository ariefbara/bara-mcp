<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ConsultationFeedbackForm;

class ConsultationFeedbackFormView
{
    /**
     *
     * @var ConsultationFeedbackFormRepository
     */
    protected $consultationFeedbackFormRepository;
    
    function __construct(ConsultationFeedbackFormRepository $consultationFeedbackFormRepository)
    {
        $this->consultationFeedbackFormRepository = $consultationFeedbackFormRepository;
    }
    
    public function showById(string $firmId, string $consultationFeedbackFormId): ConsultationFeedbackForm
    {
        return $this->consultationFeedbackFormRepository->ofId($firmId, $consultationFeedbackFormId);
    }
    
    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationFeedbackForm[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->consultationFeedbackFormRepository->all($firmId, $page, $pageSize);
    }

}
