<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial\LearningAttachment;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

class LearningMaterial
{

    /**
     *
     * @var Mission
     */
    protected $mission;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $content;

    /**
     *
     * @var bool
     */
    protected $removed = false;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $learningAttachments;
    
    protected function setName(string $name): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, 'bad request: learning material name is mandatory');
        $this->name = $name;
    }

    function __construct(Mission $mission, string $id, LearningMaterialData $data)
    {
        $this->mission = $mission;
        $this->id = $id;
        $this->setName($data->getName());
        $this->content = $data->getContent();
        $this->removed = false;
        
        $this->learningAttachments = new ArrayCollection();
        $this->addAttachments($data);
    }
    
    protected function addAttachments(LearningMaterialData $data): void
    {
        foreach ($data->iterateFirmFileInfoInAttachmentList() as $firmFileInfo) {
            $id = Uuid::generateUuid4();
            $learningAttachment = new LearningAttachment($this, $id, $firmFileInfo);
            $this->learningAttachments->add($learningAttachment);
        }
    }

    public function update(LearningMaterialData $data): void
    {
        $this->setName($data->getName());
        $this->content = $data->getContent();
        
        foreach ($this->learningAttachments->getIterator() as $learningAttachment) {
            $learningAttachment->update($data);
        }
        
        $this->addAttachments($data);
    }

    public function remove(): void
    {
        $this->removed = true;
    }
    
    public function assertAccessibleInFirm(Firm $firm): void
    {
        $this->mission->assertAccessibleInFirm($firm);
    }

}
