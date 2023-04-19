<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Participant\Application\Service\Client\AsTeamMember\CancelObjectiveProgressReportSubmission;
use Participant\Application\Service\Client\AsTeamMember\SubmitObjectiveProgressReport;
use Participant\Application\Service\Client\AsTeamMember\UpdateObjectiveProgressReport;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport as ObjectiveProgressReport2;
use Participant\Domain\Model\TeamProgramParticipation as TeamProgramParticipation2;
use Query\Application\Service\TeamMember\ViewObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport\KeyResultProgressReportAttachment;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Service\ObjectiveProgressReportFinder;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ObjectiveProgressReportController extends AsTeamMemberBaseController
{

    public function submit($teamId, $teamProgramParticipationId, $objectiveId)
    {
        $objectiveProgressReportId = $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveId,
                $this->getObjectiveProgressReportData());

        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveProgressReportId);
        return $this->commandCreatedResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function update($teamId, $teamProgramParticipationId, $objectiveProgressReportId)
    {
        $this->buildUpdateService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveProgressReportId,
                $this->getObjectiveProgressReportData());
        return $this->show($teamId, $teamProgramParticipationId, $objectiveProgressReportId);
    }

    protected function getObjectiveProgressReportData()
    {
        $reportDate = $this->dateTimeImmutableOfInputRequest('reportDate');
        $data = new Objective\ObjectiveProgressReportData($reportDate);
        foreach ($this->request->input('keyResultProgressReports') as $keyResultProgressReport) {
            $value = $keyResultProgressReport['value'];
            $keyResultProgressReportData = new ObjectiveProgressReport2\KeyResultProgressReportData($value);
            foreach ($keyResultProgressReport['fileInfoIdListOfAttachment'] as $fileInfoId) {
                $keyResultProgressReportData->addFileInfoIdAsAttachment($fileInfoId);
            }
            $keyResultId = $keyResultProgressReport['keyResultId'];
            $data->addKeyResultProgressReportData($keyResultProgressReportData, $keyResultId);
        }
        return $data;
    }

    public function cancel($teamId, $teamProgramParticipationId, $objectiveProgressReportId)
    {
        $this->buildCancelService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveProgressReportId);
        return $this->show($teamId, $teamProgramParticipationId, $objectiveProgressReportId);
    }

    public function show($teamId, $teamProgramParticipationId, $objectiveProgressReportId)
    {
        $objectiveProgressReport = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveProgressReportId);
        return $this->singleQueryResponse($this->arrayDataOfObjectiveProgressReport($objectiveProgressReport));
    }

    public function showAll($teamId, $teamProgramParticipationId, $objectiveId)
    {
        $objectiveProgressReports = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $objectiveId, $this->getPage(),
                $this->getPageSize());
        
        $result = [];
        $result['total'] = count($objectiveProgressReports);
        foreach ($objectiveProgressReports as $objectiveProgressReport) {
            $result['list'][] = $this->arrayDataOfObjectiveProgressReport($objectiveProgressReport, false);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfObjectiveProgressReport(ObjectiveProgressReport $objectiveProgressReport, $includeAttachment = true): array
    {
        $keyResultProgressReports = [];
        foreach ($objectiveProgressReport->iterateKeyResultProgressReports() as $keyResultProgressReport) {
            $keyResultProgressReports[] = $this->arrayDataOfKeyResultProgressReport($keyResultProgressReport, $includeAttachment);
        }
        return [
            'id' => $objectiveProgressReport->getId(),
            'reportDate' => $objectiveProgressReport->getReportDateString(),
            'submitTime' => $objectiveProgressReport->getSubmitTimeString(),
            'approvalStatus' => $objectiveProgressReport->getApprovalStatusValue(),
            'cancelled' => $objectiveProgressReport->isCancelled(),
            'keyResultProgressReports' => $keyResultProgressReports,
        ];
    }
    protected function arrayDataOfKeyResultProgressReport(KeyResultProgressReport $keyResultProgressReport, $includeAttachment = true): array
    {
        $result = [
            'id' => $keyResultProgressReport->getId(),
            'value' => $keyResultProgressReport->getValue(),
            'disabled' => $keyResultProgressReport->isDisabled(),
            'keyResult' => [
                'id' => $keyResultProgressReport->getKeyResult()->getId(),
                'name' => $keyResultProgressReport->getKeyResult()->getName(),
                'target' => $keyResultProgressReport->getKeyResult()->getTarget(),
                'weight' => $keyResultProgressReport->getKeyResult()->getWeight(),
            ],
        ];
        if ($includeAttachment) {
            $attachments = [];
            foreach ($keyResultProgressReport->getAttachments() as $attachment) {
                $attachments[] = $this->arrayDataOfAttachment($attachment);
            }
            $result['attachments'] = $attachments;
        }
        return $result;
    }
    private function arrayDataOfAttachment(KeyResultProgressReportAttachment $attachment): array
    {
        return [
            'fileInfo' => [
                'id' => $attachment->getFileInfo()->getId(),
                'path' => $attachment->getFileInfo()->getFullyQualifiedFileName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport::class);
        $objectiveProgressReportFinder = new ObjectiveProgressReportFinder($objectiveProgressReportRepository);
        return new ViewObjectiveProgressReport($teamMemberRepository, $teamParticipantRepository,
                $objectiveProgressReportFinder);
    }

    protected function buildSubmitService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $objectiveRepository = $this->em->getRepository(Objective::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        return new SubmitObjectiveProgressReport($teamMemberRepository, $teamParticipantRepository,
                $objectiveRepository, $objectiveProgressReportRepository, $fileInfoRepository);
    }

    protected function buildUpdateService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        return new UpdateObjectiveProgressReport($teamMemberRepository, $teamParticipantRepository,
                $objectiveProgressReportRepository, $fileInfoRepository);
    }

    protected function buildCancelService()
    {
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        $objectiveProgressReportRepository = $this->em->getRepository(ObjectiveProgressReport2::class);
        return new CancelObjectiveProgressReportSubmission($teamMemberRepository, $teamParticipantRepository,
                $objectiveProgressReportRepository);
    }

}
