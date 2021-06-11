<?php

namespace Firm\Domain\Task;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\BioForm;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Task\BioSearchFilterDataBuilder\BioFormSearchFilterRequest;
use Tests\TestBase;

class BioSearchFilterDataBuilderTest extends TestBase
{
    protected $bioFormRepository, $bioFormId = 'bio-form-id', $bioForm;
    protected $bioSearchFilterDataBuilder;
    protected $bioFormSearchFilterRequest;
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bioForm = $this->buildMockOfClass(BioForm::class);
        $this->bioFormRepository = $this->buildMockOfInterface(BioFormRepository::class);
        $this->bioFormRepository->expects($this->any())
                ->method('ofId')
                ->with($this->bioFormId)
                ->willReturn($this->bioForm);
        
        $this->bioSearchFilterDataBuilder = new TestableBioSearchFilterDataBuilder($this->bioFormRepository);
        $this->bioFormSearchFilterRequest = new BioFormSearchFilterRequest($this->bioFormId);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    
    public function test_construct_setProperties()
    {
        $bioSearchFilterDataBuilder = new TestableBioSearchFilterDataBuilder($this->bioFormRepository);
        $this->assertSame($this->bioFormRepository, $bioSearchFilterDataBuilder->bioFormRepository);
        $this->assertSame([], $bioSearchFilterDataBuilder->bioFormSearchFilterRequests);
    }
    
    public function test_addBioFormSearchFilterRequest_addBioFormSearchFilterRequestToList()
    {
        $this->bioSearchFilterDataBuilder->addBioFormSearchFilterRequest($this->bioFormSearchFilterRequest);
        $this->assertEquals([$this->bioFormSearchFilterRequest], $this->bioSearchFilterDataBuilder->bioFormSearchFilterRequests);
    }
    
    protected function executeBuild()
    {
        $this->bioSearchFilterDataBuilder->addBioFormSearchFilterRequest($this->bioFormSearchFilterRequest);
        return $this->bioSearchFilterDataBuilder->build($this->firm);
    }
    public function test_build_returnBioSearchFilterData()
    {
        $this->assertInstanceOf(BioSearchFilterData::class, $this->executeBuild());
    }
    public function test_build_bioFormSetFieldFiltersToBioSearchFilterData()
    {
        $this->bioForm->expects($this->once())
                ->method('setFieldFiltersToBioSearchFilterData')
                ->with($this->anything(), $this->bioFormSearchFilterRequest);
        $this->executeBuild();
    }
    public function test_build_assertBioFormIsAccessibleInFirm()
    {
        $this->bioForm->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->executeBuild();
    }
}

class TestableBioSearchFilterDataBuilder extends BioSearchFilterDataBuilder
{
    public $bioFormRepository;
    public $bioFormSearchFilterRequests;
}
