<?php

namespace Participant\Domain\Service;

use DateTimeImmutable;
use Participant\Domain\ {
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValueData,
    SharedModel\FileInfo
};

class MetricAssignmentReportDataProvider
{

    /**
     *
     * @var LocalFileInfoRepository
     */
    protected $fileInfoRepository;

    /**
     *
     * @var array
     */
    protected $assignmentFieldValueDataCollection;

    function __construct(LocalFileInfoRepository $fileInfoRepository)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->assignmentFieldValueDataCollection = [];
    }

    public function addAssignmentFieldValueData(
            string $assignmentFieldId, ?float $value, ?string $note, ?string $fileInfoId): void
    {
        $attachmentFileInfo = isset($fileInfoId) ? $this->fileInfoRepository->ofId($fileInfoId) : null;
        $this->assignmentFieldValueDataCollection[$assignmentFieldId] = new AssignmentFieldValueData(
                $value, $note, $attachmentFileInfo);
    }

    public function getAssignmentFieldValueDataCorrespondWithAssignmentField(string $assignmentFieldId): ?AssignmentFieldValueData
    {
        return isset($this->assignmentFieldValueDataCollection[$assignmentFieldId]) ?
                $this->assignmentFieldValueDataCollection[$assignmentFieldId] : null;
    }
    
    /**
     * @return FileInfo[]
     */
    public function iterateAllAttachedFileInfo(): array
    {
        $attachedFileInfos = [];
        foreach ($this->assignmentFieldValueDataCollection as $assignmentFieldValueData) {
            if (!empty($attachedFileInfo = $assignmentFieldValueData->getAttachedFileInfo())) {
                $attachedFileInfos[] = $attachedFileInfo;
            }
        }
        return $attachedFileInfos;
    }

}
