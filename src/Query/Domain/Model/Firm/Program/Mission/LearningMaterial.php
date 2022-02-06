<?php

namespace Query\Domain\Model\Firm\Program\Mission;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial\LearningAttachment;

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

    function getMission(): Mission
    {
        return $this->mission;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getContent(): string
    {
        return $this->content;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }

    /**
     * 
     * @return LearningAttachment[]
     */
    public function iterateAllLearningAttachments()
    {
        return $this->learningAttachments->getIterator();
    }

    /**
     * 
     * @return LearningAttachment[]
     */
    public function iterateAllActiveLearningAttachments()
    {
        $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('disabled', false));
        return $this->learningAttachments->matching($criteria)->getIterator();
    }

}
