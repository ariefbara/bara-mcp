<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\FeedbackForm;

class FeedbackFormView
{
    /**
     *
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;
    
    function __construct(FeedbackFormRepository $feedbackFormRepository)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
    }
    
    public function showById(string $firmId, string $feedbackFormId): FeedbackForm
    {
        return $this->feedbackFormRepository->ofId($firmId, $feedbackFormId);
    }
    
    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return FeedbackForm[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->feedbackFormRepository->all($firmId, $page, $pageSize);
    }

}
