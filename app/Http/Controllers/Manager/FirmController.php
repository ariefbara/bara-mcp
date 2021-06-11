<?php

namespace App\Http\Controllers\Manager;

use Firm\Application\Service\Manager\HandleMutationTask;
use Firm\Application\Service\UpdateFirmProfile;
use Firm\Domain\Model\Firm as Firm2;
use Firm\Domain\Model\Firm\BioForm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Task\BioSearchFilterDataBuilder;
use Firm\Domain\Task\SetBioSearchFilter;
use Query\Application\Service\FirmView;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Firm\FirmFileInfo as FirmFileInfo2;

class FirmController extends ManagerBaseController
{
    public function update()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $firmFileInfoId = $this->stripTagsInputRequest("firmFileInfoIdOfLogo");
        $displaySetting = $this->stripTagsInputRequest("displaySetting");
        
        $service->execute($this->firmId(), $firmFileInfoId, $displaySetting);
        
        $firm = $this->buildViewService()->showById($this->firmId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }
    
    protected function getBioSearchFilterDataBuilder(): BioSearchFilterDataBuilder
    {
        $bioFormRepository = $this->em->getRepository(BioForm::class);
        $bioSearchFilterDataBuilder = new BioSearchFilterDataBuilder($bioFormRepository);
        foreach ($this->request->input('bioForms') as $bioForm) {
            $bioFormId = $this->stripTagsVariable($bioForm['id']);
            $bioFormSearchFilterRequest = new BioSearchFilterDataBuilder\BioFormSearchFilterRequest($bioFormId);
            foreach ($bioForm['integerFields'] as $integerField) {
                $fieldId = $this->stripTagsVariable($integerField['id']);
                $comparisonType = $this->integerOfVariable($integerField['comparisonType']);
                $bioFormSearchFilterRequest->addIntegerFieldSearchFilterRequest($fieldId, $comparisonType);
            }
            foreach ($bioForm['stringFields'] as $stringField) {
                $fieldId = $this->stripTagsVariable($stringField['id']);
                $comparisonType = $this->integerOfVariable($stringField['comparisonType']);
                $bioFormSearchFilterRequest->addStringFieldSearchFilterRequest($fieldId, $comparisonType);
            }
            foreach ($bioForm['textAreaFields'] as $textAreaField) {
                $fieldId = $this->stripTagsVariable($textAreaField['id']);
                $comparisonType = $this->integerOfVariable($textAreaField['comparisonType']);
                $bioFormSearchFilterRequest->addTextAreaFieldSearchFilterRequest($fieldId, $comparisonType);
            }
            foreach ($bioForm['singleSelectFields'] as $singleSelectField) {
                $fieldId = $this->stripTagsVariable($singleSelectField['id']);
                $comparisonType = $this->integerOfVariable($singleSelectField['comparisonType']);
                $bioFormSearchFilterRequest->addSingleSelectFieldSearchFilterRequest($fieldId, $comparisonType);
            }
            foreach ($bioForm['multiSelectFields'] as $multiSelectField) {
                $fieldId = $this->stripTagsVariable($multiSelectField['id']);
                $comparisonType = $this->integerOfVariable($multiSelectField['comparisonType']);
                $bioFormSearchFilterRequest->addMultiSelectFieldSearchFilterRequest($fieldId, $comparisonType);
            }
            $bioSearchFilterDataBuilder->addBioFormSearchFilterRequest($bioFormSearchFilterRequest);
        }
        return $bioSearchFilterDataBuilder;
    }
    public function setBioSearchFilter()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $service = new HandleMutationTask($managerRepository);
        
        $task = new SetBioSearchFilter($this->getBioSearchFilterDataBuilder());
        $service->execute($this->firmId(), $this->managerId(), $task);
        
        $firm = $this->buildViewService()->showById($this->firmId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }
    
    public function show()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $firm = $service->showById($this->firmId());
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
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
            "bioSearchFilter" => $this->arrayDataOfBioSearchFilter($firm->getBioSearchFilter()),
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
    protected function arrayDataOfLogo(?FirmFileInfo2 $logo): ?array
    {
        return empty($logo)? null: [
            "id" => $logo->getId(),
            "path" => $logo->getFullyQualifiedFileName(),
        ];
    }
    protected function buildViewService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmView($firmRepository);
    }
    protected function buildUpdateService()
    {
        $firmRepository = $this->em->getRepository(Firm2::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        return new UpdateFirmProfile($firmRepository, $firmFileInfoRepository);
    }
}
