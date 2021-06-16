<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Client\ViewFirm;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\FirmFileInfo;

class FirmController extends ClientBaseController
{
    public function show()
    {
        $firm = $this->buildViewService()->show($this->firmId(), $this->clientId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }
    
    public function showBioSearchFilter()
    {
        $bioSearchFilter = $this->buildViewService()->showBioSearchFilter($this->firmId(), $this->clientId());
        return $this->singleQueryResponse($this->arrayDataOfBioSearchFilter($bioSearchFilter));
    }
    
    protected function arrayDataOfFirm(Firm $firm): array
    {
        return [
            "name" => $firm->getName(),
            "domain" => $firm->getWhitelableUrl(),
            "mailSenderAddress" => $firm->getWhitelableMailSenderAddress(),
            "mailSenderName" => $firm->getWhitelableMailSenderName(),
            "logo" => $this->arrayDataOfLogo($firm->getLogo()),
            "displaySetting" => $firm->getDisplaySetting(),
        ];
    }
    protected function arrayDataOfLogo(?FirmFileInfo $logo): ?array
    {
        return empty($logo)? null: [
            "id" => $logo->getId(),
            "path" => $logo->getFullyQualifiedFileName(),
        ];
    }
    
    protected function arrayDataOfBioSearchFilter(?BioSearchFilter $bioSearchFilter): ?array
    {
        return empty($bioSearchFilter) ? null : [
            'disabled' => $bioSearchFilter->isDisabled(),
            'modifiedTime' => $bioSearchFilter->getModifiedTimeString(),
            'integerFieldSearchFilters' => $this->arrayDataOfIntegerFieldSearchFilter($bioSearchFilter),
            'stringFieldSearchFilters' => $this->arrayDataOfStringFieldSearchFilter($bioSearchFilter),
            'textAreaFieldSearchFilters' => $this->arrayDataOfTextAreaFieldSearchFilter($bioSearchFilter),
            'singleSelectFieldSearchFilters' => $this->arrayDataOfSingleSelectFieldSearchFilter($bioSearchFilter),
            'multiSelectFieldSearchFilters' => $this->arrayDataOfMultiSelectFieldSearchFilter($bioSearchFilter),
        ];
    }
    protected function arrayDataOfIntegerFieldSearchFilter(BioSearchFilter $bioSearchFilter): ?array
    {
        $integerFieldSearchFilters = [];
        foreach ($bioSearchFilter->iterateIntegerFieldSearchFilters() as $integerFieldSearchFilter) {
            $integerFieldSearchFilters[] = [
                'disabled' => $integerFieldSearchFilter->isDisabled(),
                'integerField' => [
                    'id' => $integerFieldSearchFilter->getIntegerField()->getId(),
                    'name' => $integerFieldSearchFilter->getIntegerField()->getName(),
                ],
                'comparisonType' => $integerFieldSearchFilter->getComparisonTypeValue(),
                'comparisonTypeDisplayValue' => $integerFieldSearchFilter->getComparisonTypeDisplayValue(),
            ];
        }
        return $integerFieldSearchFilters;
    }
    protected function arrayDataOfStringFieldSearchFilter(BioSearchFilter $bioSearchFilter): ?array
    {
        $stringFieldSearchFilters = [];
        foreach ($bioSearchFilter->iterateStringFieldSearchFilters() as $stringFieldSearchFilter) {
            $stringFieldSearchFilters[] = [
                'disabled' => $stringFieldSearchFilter->isDisabled(),
                'stringField' => [
                    'id' => $stringFieldSearchFilter->getStringField()->getId(),
                    'name' => $stringFieldSearchFilter->getStringField()->getName(),
                ],
                'comparisonType' => $stringFieldSearchFilter->getComparisonTypeValue(),
                'comparisonTypeDisplayValue' => $stringFieldSearchFilter->getComparisonTypeDisplayValue(),
            ];
        }
        return $stringFieldSearchFilters;
    }
    protected function arrayDataOfTextAreaFieldSearchFilter(BioSearchFilter $bioSearchFilter): ?array
    {
        $textAreaFieldSearchFilters = [];
        foreach ($bioSearchFilter->iterateTextAreaFieldSearchFilters() as $textAreaFieldSearchFilter) {
            $textAreaFieldSearchFilters[] = [
                'disabled' => $textAreaFieldSearchFilter->isDisabled(),
                'textAreaField' => [
                    'id' => $textAreaFieldSearchFilter->getTextAreaField()->getId(),
                    'name' => $textAreaFieldSearchFilter->getTextAreaField()->getName(),
                ],
                'comparisonType' => $textAreaFieldSearchFilter->getComparisonTypeValue(),
                'comparisonTypeDisplayValue' => $textAreaFieldSearchFilter->getComparisonTypeDisplayValue(),
            ];
        }
        return $textAreaFieldSearchFilters;
    }
    protected function arrayDataOfSingleSelectFieldSearchFilter(BioSearchFilter $bioSearchFilter): ?array
    {
        $singleSelectFieldSearchFilters = [];
        foreach ($bioSearchFilter->iterateSingleSelectFieldSearchFilters() as $singleSelectFieldSearchFilter) {
            $singleSelectFieldSearchFilters[] = [
                'disabled' => $singleSelectFieldSearchFilter->isDisabled(),
                'singleSelectField' => [
                    'id' => $singleSelectFieldSearchFilter->getSingleSelectField()->getId(),
                    'name' => $singleSelectFieldSearchFilter->getSingleSelectField()->getName(),
                ],
                'comparisonType' => $singleSelectFieldSearchFilter->getComparisonTypeValue(),
                'comparisonTypeDisplayValue' => $singleSelectFieldSearchFilter->getComparisonTypeDisplayValue(),
            ];
        }
        return $singleSelectFieldSearchFilters;
    }
    protected function arrayDataOfMultiSelectFieldSearchFilter(BioSearchFilter $bioSearchFilter): ?array
    {
        $multiSelectFieldSearchFilters = [];
        foreach ($bioSearchFilter->iterateMultiSelectFieldSearchFilters() as $multiSelectFieldSearchFilter) {
            $multiSelectFieldSearchFilters[] = [
                'disabled' => $multiSelectFieldSearchFilter->isDisabled(),
                'multiSelectField' => [
                    'id' => $multiSelectFieldSearchFilter->getMultiSelectField()->getId(),
                    'name' => $multiSelectFieldSearchFilter->getMultiSelectField()->getName(),
                ],
                'comparisonType' => $multiSelectFieldSearchFilter->getComparisonTypeValue(),
                'comparisonTypeDisplayValue' => $multiSelectFieldSearchFilter->getComparisonTypeDisplayValue(),
            ];
        }
        return $multiSelectFieldSearchFilters;
    }
    
    public function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        return new ViewFirm($clientRepository);
    }
}
