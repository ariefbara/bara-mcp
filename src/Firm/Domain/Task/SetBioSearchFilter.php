<?php

namespace Firm\Domain\Task;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\MutationTaskExecutableByManager;
use Firm\Domain\Task\BioSearchFilterDataBuilder;

class SetBioSearchFilter implements MutationTaskExecutableByManager
{

    /**
     * 
     * @var BioSearchFilterDataBuilder
     */
    protected $bioSearchFilterDataBuilder;

    public function __construct(BioSearchFilterDataBuilder $bioSearchFilterDataBuilder)
    {
        $this->bioSearchFilterDataBuilder = $bioSearchFilterDataBuilder;
    }

    public function execute(Firm $firm): void
    {
        $bioSearchFilterData = $this->bioSearchFilterDataBuilder->build($firm);
        $firm->setBioSearchFilter($bioSearchFilterData);
    }

}
