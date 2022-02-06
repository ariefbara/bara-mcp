<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Tests\TestBase;

class LearningMaterialDataTest extends TestBase
{
    protected $learningMaterialData;
    protected $name = 'new name', $content = 'new content';
    protected $firmFileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialData = new TestableLearningMaterialData('name', 'content');
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
    }
    
    protected function construct()
    {
        return new TestableLearningMaterialData($this->name, $this->content);
    }
    public function test_construct_setProperties()
    {
        $learningMaterialData = $this->construct();
        $this->assertSame($this->name, $learningMaterialData->name);
        $this->assertSame($this->content, $learningMaterialData->content);
    }
    public function test_construct_setAttachmentList()
    {
        $learningMaterialData = $this->construct();
        $this->assertInstanceOf(\SplObjectStorage::class, $learningMaterialData->firmFileInfoAttachmentList);
    }
    
    protected function addAttachment()
    {
        $this->learningMaterialData->addAttachment($this->firmFileInfo);
    }
    public function test_addAttachment_attachFirmFileInfoToStorage()
    {
        $this->addAttachment();
        $this->assertTrue($this->learningMaterialData->firmFileInfoAttachmentList->contains($this->firmFileInfo));
    }
    public function test_addAttachment_alreadyStoreSameFirmFileInfo()
    {
        $this->learningMaterialData->firmFileInfoAttachmentList->attach($this->firmFileInfo);
        $this->addAttachment();
        $this->assertSame(1, $this->learningMaterialData->firmFileInfoAttachmentList->count());
    }
    
    protected function removeFirmFileInfoFromList()
    {
        $this->learningMaterialData->firmFileInfoAttachmentList->attach($this->firmFileInfo);
        return $this->learningMaterialData->removeFirmFileInfoFromList($this->firmFileInfo);
    }
    public function test_removeFirmFileInfoFromList_removeCorrespondingFirmFileInfo()
    {
        $this->removeFirmFileInfoFromList();
        $this->assertEmpty($this->learningMaterialData->firmFileInfoAttachmentList->count());
    }
    public function test_removeFirmFileInfoFromList_returnTrue()
    {
        $this->assertTrue($this->removeFirmFileInfoFromList());
    }
    public function test_removeFirmFileInfoFromList_noCorrespondingFirmFileInfoInList_returnFalse()
    {
        $this->assertFalse($this->learningMaterialData->removeFirmFileInfoFromList($this->firmFileInfo));
    }
}

class TestableLearningMaterialData extends LearningMaterialData
{
    public $name;
    public $content;
    public $firmFileInfoAttachmentList;
}
