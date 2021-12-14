<?php

namespace Query\Domain\Service;

use Query\Domain\SharedModel\ReportSpreadsheet\CustomFieldColumnsPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;

class FeedbackFormReportSheetRequest
{

    /**
     * 
     * @var string
     */
    protected $feedbackFormId;

    /**
     * 
     * @var TeamMemberReportSheetPayload
     */
    protected $teamMemberReportSheetPayload;

    /**
     * 
     * @var CustomFieldColumnsPayload
     */
    protected $customFieldColumnsPayload;

    public function getFeedbackFormId(): string
    {
        return $this->feedbackFormId;
    }

    public function getTeamMemberReportSheetPayload(): TeamMemberReportSheetPayload
    {
        return $this->teamMemberReportSheetPayload;
    }

    public function getCustomFieldColumnsPayload(): CustomFieldColumnsPayload
    {
        return $this->customFieldColumnsPayload;
    }

    public function __construct(
            string $feedbackFormId, TeamMemberReportSheetPayload $teamMemberReportSheetPayload,
            CustomFieldColumnsPayload $customFieldColumnsPayload)
    {
        $this->feedbackFormId = $feedbackFormId;
        $this->teamMemberReportSheetPayload = $teamMemberReportSheetPayload;
        $this->customFieldColumnsPayload = $customFieldColumnsPayload;
    }

}
