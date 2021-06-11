<?php

namespace Firm\Domain\Task;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Task\BioSearchFilterDataBuilder\BioFormSearchFilterRequest;

class BioSearchFilterDataBuilder
{

    /**
     * 
     * @var BioFormRepository
     */
    protected $bioFormRepository;

    /**
     * 
     * @var BioFormSearchFilterRequest[]
     */
    protected $bioFormSearchFilterRequests;

    public function __construct(BioFormRepository $bioFormRepository)
    {
        $this->bioFormRepository = $bioFormRepository;
        $this->bioFormSearchFilterRequests = [];
    }

    public function addBioFormSearchFilterRequest(BioFormSearchFilterRequest $bioFormSearchFilterRequest): void
    {
        $this->bioFormSearchFilterRequests[] = $bioFormSearchFilterRequest;
    }
    
    public function build(Firm $firm): BioSearchFilterData
    {
        $bioSearchFilterData = new BioSearchFilterData();
        foreach ($this->bioFormSearchFilterRequests as $bioFormSearchFilterRequest) {
            $bioForm = $this->bioFormRepository->ofId($bioFormSearchFilterRequest->getBioFormId());
            $bioForm->assertAccessibleInFirm($firm);
            $bioForm->setFieldFiltersToBioSearchFilterData($bioSearchFilterData, $bioFormSearchFilterRequest);
        }
        return $bioSearchFilterData;
    }

}
