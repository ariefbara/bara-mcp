<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission;
use Tests\TestBase;

class LearningMaterialTest extends TestBase
{
    protected $mission;
    protected $learningMaterial;
    protected $id = 'newId', $name = 'new name', $content = 'new content';
   
    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->learningMaterial = new TestableLearningMaterial($this->mission, 'id', 'name', 'content');
    }
    public function test_construct_setProperties()
    {
        $learningMaterial = new TestableLearningMaterial($this->mission, $this->id, $this->name, $this->content);
        $this->assertEquals($this->mission, $learningMaterial->mission);
        $this->assertEquals($this->id, $learningMaterial->id);
        $this->assertEquals($this->name, $learningMaterial->name);
        $this->assertEquals($this->content, $learningMaterial->content);
        $this->assertFalse($learningMaterial->removed);
    }
    public function test_update_changeNameAndDescription()
    {
        $this->learningMaterial->update($this->name, $this->content);
        $this->assertEquals($this->name, $this->learningMaterial->name);
        $this->assertEquals($this->content, $this->learningMaterial->content);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->learningMaterial->remove();
        $this->assertTrue($this->learningMaterial->removed);
    }
}

class TestableLearningMaterial extends LearningMaterial
{
    public $mission, $id, $name, $content, $removed;
}
