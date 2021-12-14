<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\SharedModel\ReportSpreadsheet\CustomFieldColumnsPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;

class FirmReportSpreadsheetGroupByFeedbackFormPayload
{

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

    public function getTeamMemberReportSheetPayload(): TeamMemberReportSheetPayload
    {
        return $this->teamMemberReportSheetPayload;
    }

    public function getCustomFieldColumnsPayload(): CustomFieldColumnsPayload
    {
        return $this->customFieldColumnsPayload;
    }

    public function __construct(
            TeamMemberReportSheetPayload $teamMemberReportSheetPayload,
            CustomFieldColumnsPayload $customFieldColumnsPayload)
    {
        $this->teamMemberReportSheetPayload = $teamMemberReportSheetPayload;
        $this->customFieldColumnsPayload = $customFieldColumnsPayload;
    }

}
