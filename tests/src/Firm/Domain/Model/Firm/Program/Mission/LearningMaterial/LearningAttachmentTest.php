<?php

namespace Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Tests\TestBase;

class LearningAttachmentTest extends TestBase
{
    protected $learningMaterial;
    protected $firmFileInfo;
    protected $learningAttachment;
    protected $id = 'newId';
    protected $learningMaterialData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterial = $this->buildMockOfClass(LearningMaterial::class);
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->learningAttachment = new TestableLearningAttachment($this->learningMaterial, 'id', $this->firmFileInfo);
        
        $this->learningMaterialData = $this->buildMockOfClass(LearningMaterialData::class);
    }
    
    protected function construct()
    {
        return new TestableLearningAttachment($this->learningMaterial, $this->id, $this->firmFileInfo);
    }
    public function test_construct_setProperties()
    {
        $learningAttachment = $this->construct();
        $this->assertSame($this->learningMaterial, $learningAttachment->learningMaterial);
        $this->assertSame($this->id, $learningAttachment->id);
        $this->assertFalse($learningAttachment->disabled);
        $this->assertSame($this->firmFileInfo, $learningAttachment->firmFileInfo);
    }
    
    protected function update()
    {
        $this->learningAttachment->update($this->learningMaterialData);
    }
    public function test_update_noFirmFileInfoInData_setDisabled()
    {
        $this->learningMaterialData->expects($this->once())
                ->method('removeFirmFileInfoFromList')
                ->with($this->firmFileInfo)
                ->willReturn(false);
        $this->update();
        $this->assertTrue($this->learningAttachment->disabled);
    }
    public function test_update_succesfullyRemovingFirmFileInfoInData_setEnable()
    {
        $this->learningAttachment->disabled = true;
        $this->learningMaterialData->expects($this->once())
                ->method('removeFirmFileInfoFromList')
                ->with($this->firmFileInfo)
                ->willReturn(true);
        $this->update();
        $this->assertFalse($this->learningAttachment->disabled);
    }
}

class TestableLearningAttachment extends LearningAttachment
{
    public $learningMaterial;
    public $id;
    public $disabled;
    public $firmFileInfo;
}
