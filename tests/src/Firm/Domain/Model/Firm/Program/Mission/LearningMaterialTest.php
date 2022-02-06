<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial\LearningAttachment;
use Tests\TestBase;

class LearningMaterialTest extends TestBase
{
    protected $mission;
    protected $learningMaterial;
    protected $learningAttachment;
    protected $id = 'newId', $name = 'new name', $content = 'new content', $firmFileInfo;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->buildMockOfClass(Mission::class);
        $data = new LearningMaterialData('name', 'contenct');
        $this->learningMaterial = new TestableLearningMaterial($this->mission, 'id', $data);
        $this->learningAttachment = $this->buildMockOfClass(LearningAttachment::class);
        $this->learningMaterial->learningAttachments->add($this->learningAttachment);
        
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    protected function getLearningMaterialData()
    {
        $data = new LearningMaterialData($this->name, $this->content);
        $data->addAttachment($this->firmFileInfo);
        return $data;
    }
    protected function construct()
    {
        return new TestableLearningMaterial($this->mission, $this->id, $this->getLearningMaterialData());
    }
    public function test_construct_setProperties()
    {
        $learningMaterial = $this->construct();
        $this->assertEquals($this->mission, $learningMaterial->mission);
        $this->assertEquals($this->id, $learningMaterial->id);
        $this->assertEquals($this->name, $learningMaterial->name);
        $this->assertEquals($this->content, $learningMaterial->content);
        $this->assertFalse($learningMaterial->removed);
    }
    public function test_construct_setLearningAttachments()
    {
        $learningMaterial = $this->construct();
        $this->assertEquals(1, $learningMaterial->learningAttachments->count());
        $this->assertInstanceOf(LearningAttachment::class, $learningMaterial->learningAttachments->first());
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = '';
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: learning material name is mandatory');
    }
    
    protected function update()
    {
        $this->learningMaterial->update($this->getLearningMaterialData());
    }
    public function test_update_changeNameAndDescription()
    {
        $this->update();
        $this->assertEquals($this->name, $this->learningMaterial->name);
        $this->assertEquals($this->content, $this->learningMaterial->content);
    }
    public function test_update_emptyName_badRequest()
    {
        $this->name = '';
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: learning material name is mandatory');
    }
    public function test_update_updateExistingLearningAttachments()
    {
        $this->learningAttachment->expects($this->once())
                ->method('update')
                ->with($this->getLearningMaterialData());
        $this->update();
    }
    public function test_update_addLeftOverAttachmentToLearningAttachmentList()
    {
        $this->update();
        $this->assertEquals(2, $this->learningMaterial->learningAttachments->count());
        $this->assertInstanceOf(LearningAttachment::class, $this->learningMaterial->learningAttachments->last());
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->learningMaterial->remove();
        $this->assertTrue($this->learningMaterial->removed);
    }
    
    protected function assertAccessibleInFirm()
    {
        $this->learningMaterial->assertAccessibleInFirm($this->firm);
    }
    public function test_assertAccessibleInFirm_assertMissionAccessibleInFirm()
    {
        $this->mission->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->assertAccessibleInFirm();
    }
}

class TestableLearningMaterial extends LearningMaterial
{
    public $mission;
    public $id;
    public $name;
    public $content;
    public $removed = false;
    public $learningAttachments;
}
