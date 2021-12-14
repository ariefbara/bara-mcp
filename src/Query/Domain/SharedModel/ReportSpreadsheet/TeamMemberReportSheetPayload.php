<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

class TeamMemberReportSheetPayload
{

    /**
     * 
     * @var bool|null
     */
    protected $teamInspected;

    /**
     * 
     * @var int|null
     */
    protected $teamColNumber;

    /**
     * 
     * @var bool|null
     */
    protected $individualInspected;

    /**
     * 
     * @var int|null
     */
    protected $individualColNumber;

    /**
     * 
     * @var ReportSheetPayload
     */
    protected $reportSheetPayload;

    public function isTeamInspected(): ?bool
    {
        return $this->teamInspected;
    }

    public function getTeamColNumber(): ?int
    {
        return $this->teamColNumber;
    }

    public function isIndividualInspected(): ?bool
    {
        return $this->individualInspected;
    }

    public function getIndividualColNumber(): ?int
    {
        return $this->individualColNumber;
    }

    public function getReportSheetPayload(): ReportSheetPayload
    {
        return $this->reportSheetPayload;
    }

    public function __construct(ReportSheetPayload $reportSheetPayload)
    {
        $this->reportSheetPayload = $reportSheetPayload;
    }

    public function inspectTeam(int $colNumber): self
    {
        $this->teamInspected = true;
        $this->teamColNumber = $colNumber;
        return $this;
    }

    public function inspectIndividual(int $colNumber): self
    {
        $this->individualInspected = true;
        $this->individualColNumber = $colNumber;
        return $this;
    }

}
