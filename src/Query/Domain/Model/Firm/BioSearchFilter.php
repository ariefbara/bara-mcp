<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilter;
use Query\Domain\Model\Firm\BioSearchFilter\MultiSelectFieldSearchFilter;
use Query\Domain\Model\Firm\BioSearchFilter\SingleSelectFieldSearchFilter;
use Query\Domain\Model\Firm\BioSearchFilter\StringFieldSearchFilter;
use Query\Domain\Model\Firm\BioSearchFilter\TextAreaFieldSearchFilter;

class BioSearchFilter
{

    /**
     * 
     * @var Firm
     */
    protected $firm;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var ArrayCollection
     */
    protected $integerFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $stringFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $textAreaFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $singleSelectFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $multiSelectFieldSearchFilters;

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

    /**
     * 
     * @return IntegerFieldSearchFilter[]
     */
    public function iterateIntegerFieldSearchFilters()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->integerFieldSearchFilters->matching($criteria)->getIterator();
    }

    /**
     * 
     * @return StringFieldSearchFilter[]
     */
    public function iterateStringFieldSearchFilters()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->stringFieldSearchFilters->matching($criteria)->getIterator();
    }

    /**
     * 
     * @return TextAreaFieldSearchFilter[]
     */
    public function iterateTextAreaFieldSearchFilters()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->textAreaFieldSearchFilters->matching($criteria)->getIterator();
    }

    /**
     * 
     * @return SingleSelectFieldSearchFilter[]
     */
    public function iterateSingleSelectFieldSearchFilters()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->singleSelectFieldSearchFilters->matching($criteria)->getIterator();
    }

    /**
     * 
     * @return MultiSelectFieldSearchFilter[]
     */
    public function iterateMultiSelectFieldSearchFilters()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->multiSelectFieldSearchFilters->matching($criteria)->getIterator();
    }

}
