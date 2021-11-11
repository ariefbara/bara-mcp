<?php

namespace Query\Domain\Model\Firm\Program\Participant\MentoringRequest;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use SharedContext\Domain\Model\Mentoring;

class ApprovedMentoringRequest
{
    /**
     * 
     * @var MentoringRequest
     */
    protected $mentoringRequest;
    
    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var string
     */
    protected $cancelled;
    
    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;
}
