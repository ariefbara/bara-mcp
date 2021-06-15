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
use Resources\Exception\RegularException;

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
    
    public function modifyClientSearchQuery(string &$dataQuery, string &$totalQuery, ClientSearchRequest $clientSearchRequest): void
    {
        $this->modifyIntegerFieldRecordSearchQuery(
                $dataQuery, $totalQuery, $clientSearchRequest->getIntegerFieldSearchRequest());
        $this->modifyStringFieldRecordSearchQuery(
                $dataQuery, $totalQuery, $clientSearchRequest->getStringFieldSearchRequest());
        $this->modifyTextAreaFieldRecordSearchQuery(
                $dataQuery, $totalQuery, $clientSearchRequest->getTextAreaFieldSearchRequest());
        $this->modifySingleSelectFieldRecordSearchQuery(
                $dataQuery, $totalQuery, $clientSearchRequest->getSingleSelectFieldSearchRequest());
        $this->modifyMultiSelectFieldRecordSearchQuery(
                $dataQuery, $totalQuery, $clientSearchRequest->getMultiSelectFieldSearchRequest());
    }
    
    protected function findIntegerFieldSearchFilterOrDie(string $integerFieldId): IntegerFieldSearchFilter
    {
        $p = function (IntegerFieldSearchFilter $integerFieldSearchFilter) use ($integerFieldId) {
            return $integerFieldSearchFilter->integerFieldIdEquals($integerFieldId);
        };
        $integerFieldSearchFilter = $this->integerFieldSearchFilters->filter($p)->first();
        if (empty($integerFieldSearchFilter)) {
            throw RegularException::notFound('not found: no corresponding integer field search filter found');
        }
        return $integerFieldSearchFilter;
    }
    protected function modifyIntegerFieldRecordSearchQuery(
            string &$dataQuery, string &$totalQuery, array $integerFieldSearchRequests): void
    {
        if (empty($integerFieldSearchRequests)) {
            return;
        }
        
        $integerFilterClause = "";
        foreach ($integerFieldSearchRequests as $integerFieldId => $value) {
            $integerFilterClause .= empty($integerFilterClause) ? 
                "({$this->findIntegerFieldSearchFilterOrDie($integerFieldId)->buildSqlComparisonClause($value)})":
                " OR " .  "({$this->findIntegerFieldSearchFilterOrDie($integerFieldId)->buildSqlComparisonClause($value)})";
        }
        
        $integerFieldRecordSearchQuery = <<<_INTEGER
                
RIGHT JOIN (
    SELECT ClientBio.Client_id clientId
    FROM IntegerFieldRecord
    LEFT JOIN ClientBio ON ClientBio.id = IntegerFieldRecord.FormRecord_id
    WHERE ClientBio.removed = FALSE 
        AND ({$integerFilterClause})
    GROUP BY clientId
)_integer ON _integer.clientId = Client.id
_INTEGER;
        
        $dataQuery .= $integerFieldRecordSearchQuery;
        $totalQuery .= $integerFieldRecordSearchQuery;
    }
    
    protected function findStringFieldSearchFilterOrDie(string $stringFieldId): StringFieldSearchFilter
    {
        $p = function (StringFieldSearchFilter $stringFieldSearchFilter) use ($stringFieldId) {
            return $stringFieldSearchFilter->stringFieldIdEquals($stringFieldId);
        };
        $stringFieldSearchFilter = $this->stringFieldSearchFilters->filter($p)->first();
        if (empty($stringFieldSearchFilter)) {
            throw RegularException::notFound('not found: no corresponding string field search filter found');
        }
        return $stringFieldSearchFilter;
    }
    protected function modifyStringFieldRecordSearchQuery(
            string &$dataQuery, string &$totalQuery, array $stringFieldSearchRequests): void
    {
        if (empty($stringFieldSearchRequests)) {
            return;
        }
        
        $stringFilterClause = "";
        foreach ($stringFieldSearchRequests as $stringFieldId => $value) {
            $stringFilterClause .= empty($stringFilterClause) ? 
                "({$this->findStringFieldSearchFilterOrDie($stringFieldId)->buildSqlComparisonClause($value)})":
                " OR " .  "({$this->findStringFieldSearchFilterOrDie($stringFieldId)->buildSqlComparisonClause($value)})";
        }
        
        $stringFieldRecordSearchQuery = <<<_STRING
                
RIGHT JOIN (
    SELECT ClientBio.Client_id clientId
    FROM StringFieldRecord
    LEFT JOIN ClientBio ON ClientBio.id = StringFieldRecord.FormRecord_id
    WHERE ClientBio.removed = FALSE 
        AND ({$stringFilterClause})
    GROUP BY clientId
)_string ON _string.clientId = Client.id
_STRING;
        
        $dataQuery .= $stringFieldRecordSearchQuery;
        $totalQuery .= $stringFieldRecordSearchQuery;
    }
    
    protected function findTextAreaFieldSearchFilterOrDie(string $textAreaFieldId): TextAreaFieldSearchFilter
    {
        $p = function (TextAreaFieldSearchFilter $textAreaFieldSearchFilter) use ($textAreaFieldId) {
            return $textAreaFieldSearchFilter->textAreaFieldIdEquals($textAreaFieldId);
        };
        $textAreaFieldSearchFilter = $this->textAreaFieldSearchFilters->filter($p)->first();
        if (empty($textAreaFieldSearchFilter)) {
            throw RegularException::notFound('not found: no corresponding textArea field search filter found');
        }
        return $textAreaFieldSearchFilter;
    }
    protected function modifyTextAreaFieldRecordSearchQuery(
            string &$dataQuery, string &$totalQuery, array $textAreaFieldSearchRequests): void
    {
        if (empty($textAreaFieldSearchRequests)) {
            return;
        }
        
        $textAreaFilterClause = "";
        foreach ($textAreaFieldSearchRequests as $textAreaFieldId => $value) {
            $textAreaFilterClause .= empty($textAreaFilterClause) ? 
                "({$this->findTextAreaFieldSearchFilterOrDie($textAreaFieldId)->buildSqlComparisonClause($value)})":
                " OR " .  "({$this->findTextAreaFieldSearchFilterOrDie($textAreaFieldId)->buildSqlComparisonClause($value)})";
        }
        
        $textAreaFieldRecordSearchQuery = <<<_TEXT_AREA
                
RIGHT JOIN (
    SELECT ClientBio.Client_id clientId
    FROM TextAreaFieldRecord
    LEFT JOIN ClientBio ON ClientBio.id = TextAreaFieldRecord.FormRecord_id
    WHERE ClientBio.removed = FALSE 
        AND ({$textAreaFilterClause})
    GROUP BY clientId
)_textArea ON _textArea.clientId = Client.id
_TEXT_AREA;
        
        $dataQuery .= $textAreaFieldRecordSearchQuery;
        $totalQuery .= $textAreaFieldRecordSearchQuery;
    }
    
    protected function findSingleSelectFieldSearchFilterOrDie(string $singleSelectFieldId): SingleSelectFieldSearchFilter
    {
        $p = function (SingleSelectFieldSearchFilter $singleSelectFieldSearchFilter) use ($singleSelectFieldId) {
            return $singleSelectFieldSearchFilter->singleSelectFieldIdEquals($singleSelectFieldId);
        };
        $singleSelectFieldSearchFilter = $this->singleSelectFieldSearchFilters->filter($p)->first();
        if (empty($singleSelectFieldSearchFilter)) {
            throw RegularException::notFound('not found: no corresponding singleSelect field search filter found');
        }
        return $singleSelectFieldSearchFilter;
    }
    protected function modifySingleSelectFieldRecordSearchQuery(
            string &$dataQuery, string &$totalQuery, array $singleSelectFieldSearchRequests): void
    {
        if (empty($singleSelectFieldSearchRequests)) {
            return;
        }
        
        $singleSelectFilterClause = "";
        foreach ($singleSelectFieldSearchRequests as $singleSelectFieldId => $listOfOptionId) {
            $singleSelectFilterClause .= empty($singleSelectFilterClause) ? 
                "({$this->findSingleSelectFieldSearchFilterOrDie($singleSelectFieldId)->buildSqlComparisonClause($listOfOptionId)})":
                " OR " .  "({$this->findSingleSelectFieldSearchFilterOrDie($singleSelectFieldId)->buildSqlComparisonClause($listOfOptionId)})";
        }
        
        $singleSelectFieldRecordSearchQuery = <<<_SINGLE_SELECT
                
RIGHT JOIN (
    SELECT ClientBio.Client_id clientId
    FROM SingleSelectFieldRecord
    LEFT JOIN ClientBio ON ClientBio.id = SingleSelectFieldRecord.FormRecord_id
    WHERE ClientBio.removed = FALSE 
        AND ({$singleSelectFilterClause})
    GROUP BY clientId
)_singleSelect ON _singleSelect.clientId = Client.id
_SINGLE_SELECT;
        
        $dataQuery .= $singleSelectFieldRecordSearchQuery;
        $totalQuery .= $singleSelectFieldRecordSearchQuery;
    }
    
    protected function findMultiSelectFieldSearchFilterOrDie(string $multiSelectFieldId): MultiSelectFieldSearchFilter
    {
        $p = function (MultiSelectFieldSearchFilter $multiSelectFieldSearchFilter) use ($multiSelectFieldId) {
            return $multiSelectFieldSearchFilter->multiSelectFieldIdEquals($multiSelectFieldId);
        };
        $multiSelectFieldSearchFilter = $this->multiSelectFieldSearchFilters->filter($p)->first();
        if (empty($multiSelectFieldSearchFilter)) {
            throw RegularException::notFound('not found: no corresponding multi Select field search filter found');
        }
        return $multiSelectFieldSearchFilter;
    }
    protected function modifyMultiSelectFieldRecordSearchQuery(
            string &$dataQuery, string &$totalQuery, array $multiSelectFieldSearchRequests): void
    {
        if (empty($multiSelectFieldSearchRequests)) {
            return;
        }
        
        $multiSelectFilterClause = "";
        foreach ($multiSelectFieldSearchRequests as $multiSelectFieldId => $listOfOptionId) {
            $multiSelectFilterClause .= empty($multiSelectFilterClause) ? 
                "({$this->findMultiSelectFieldSearchFilterOrDie($multiSelectFieldId)->buildSqlComparisonClause($listOfOptionId)})":
                " OR " .  "({$this->findMultiSelectFieldSearchFilterOrDie($multiSelectFieldId)->buildSqlComparisonClause($listOfOptionId)})";
        }
        
        $multiSelectFieldRecordSearchQuery = <<<_INTEGER
                
RIGHT JOIN (
    SELECT ClientBio.Client_id clientId
    FROM SelectedOption
        LEFT JOIN MultiSelectFieldRecord ON MultiSelectFieldRecord.id = SelectedOption.MultiSelectFieldRecord_id
        LEFT JOIN ClientBio ON ClientBio.id = MultiSelectFieldRecord.FormRecord_id
    WHERE ClientBio.removed = FALSE AND
        ({$multiSelectFilterClause})
    GROUP BY clientId
                
)_multiSelect ON _multiSelect.clientId = Client.id
_INTEGER;
        
        $dataQuery .= $multiSelectFieldRecordSearchQuery;
        $totalQuery .= $multiSelectFieldRecordSearchQuery;
    }

}
