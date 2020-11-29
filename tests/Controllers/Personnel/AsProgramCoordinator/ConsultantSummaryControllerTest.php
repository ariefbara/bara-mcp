<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Consultant\RecordOfConsultantComment,
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfPersonnel,
    Firm\RecordOfProgram,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultantSummaryControllerTest extends AsProgramCoordinatorTestCase
{
    protected $consultantSummaryUri;
    protected $consultant;
    protected $consultantOne;
    protected $consultantTwo_removed;
    protected $consultantThree;
    protected $consultantFour_otherProgram;
    
    protected $consultationRequest_00_proposed;
    protected $consultationRequest_01_offered;
    protected $consultationRequest_02_rejected;
    protected $consultationRequest_03_cancelled;
    protected $consultationRequest_04_accepted;
    protected $consultationRequest_05_accepted;
    protected $consultationRequest_06_accepted;
    protected $consultationSession_00;
    protected $consultationSession_01;
    protected $consultationSession_02;
    
    protected $consultationRequest_14_accepted;
    protected $consultationSession_10;
    
    protected $consultationRequest_20_proposed;
    protected $consultationRequest_22_rejected;
    protected $consultationRequest_24_accepted;
    protected $consultationSession_20;
    
    protected $consultationRequest_30_proposed;
    protected $consultationRequest_31_offered;
    protected $consultationRequest_33_cancelled;
    protected $consultationRequest_34_accepted;
    protected $consultationRequest_35_accepted;
    protected $consultationSession_30;
    protected $consultationSession_31;
    
    protected $consultationRequest_40_proposed;
    protected $consultationRequest_42_rejected;
    protected $consultationRequest_43_accepted;
    protected $consultationSession_40;
    
    protected $consultantComment_00;
    protected $consultantComment_01;
    protected $consultantComment_02;
    protected $consultantComment_03;
    
    protected $consultantComment_10;
    protected $consultantComment_11;
    protected $consultantComment_12;
    
    protected $consultantComment_20;
    protected $consultantComment_21;
    
    protected $consultantComment_30;
    
    protected $consultantComment_40;
    protected $consultantComment_41;
    protected $consultantComment_42;
    protected $consultantComment_43;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantSummaryUri = $this->asProgramCoordinatorUri . "/consultant-summary";
        
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultantComment")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $this->connection->table("Program")->insert($programOne->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $personnelThree = new RecordOfPersonnel($firm, 3);
        $personnelFour = new RecordOfPersonnel($firm, 4);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelThree->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelFour->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo_removed = new RecordOfConsultant($program, $personnelTwo, 2);
        $this->consultantTwo_removed->active = false;
        $this->consultantThree = new RecordOfConsultant($program, $personnelThree, 3);
        $this->consultantFour_otherProgram = new RecordOfConsultant($programOne, $personnelFour, 4);
        $this->connection->table("Consultant")->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantTwo_removed->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantThree->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantFour_otherProgram->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, $feedbackForm, $feedbackForm, 1);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        $this->connection->table("ConsultationSetup")->insert($consultationSetupOne->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $participantOne = new RecordOfParticipant($programOne, 1);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        
        $this->consultationRequest_00_proposed = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "00");
        $this->consultationRequest_01_offered = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "01");
        $this->consultationRequest_01_offered->status = "offered";
        $this->consultationRequest_02_rejected = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "02");
        $this->consultationRequest_02_rejected->concluded = true;
        $this->consultationRequest_02_rejected->status = "rejected";
        $this->consultationRequest_03_cancelled = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "03");
        $this->consultationRequest_03_cancelled->concluded = true;
        $this->consultationRequest_03_cancelled->status = "cancelled";
        $this->consultationRequest_04_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "04");
        $this->consultationRequest_04_accepted->concluded = true;
        $this->consultationRequest_04_accepted->status = "accepted";
        $this->consultationRequest_05_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "05");
        $this->consultationRequest_05_accepted->concluded = true;
        $this->consultationRequest_05_accepted->status = "accepted";
        $this->consultationRequest_06_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, "06");
        $this->consultationRequest_06_accepted->concluded = true;
        $this->consultationRequest_06_accepted->status = "accepted";
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_00_proposed->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_01_offered->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_02_rejected->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_03_cancelled->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_04_accepted->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_05_accepted->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_06_accepted->toArrayForDbEntry());
        
        $this->consultationRequest_14_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantOne, "14");
        $this->consultationRequest_14_accepted->concluded = true;
        $this->consultationRequest_14_accepted->status = "accepted";
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_14_accepted->toArrayForDbEntry());
        
        $this->consultationRequest_20_proposed = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantTwo_removed, "20");
        $this->consultationRequest_22_rejected = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantTwo_removed, "22");
        $this->consultationRequest_22_rejected->concluded = true;
        $this->consultationRequest_22_rejected->status = "rejected";
        $this->consultationRequest_24_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantTwo_removed, "24");
        $this->consultationRequest_24_accepted->concluded = true;
        $this->consultationRequest_24_accepted->status = "accepted";
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_20_proposed->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_22_rejected->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_24_accepted->toArrayForDbEntry());
        
        $this->consultationRequest_30_proposed = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantThree, "30");
        $this->consultationRequest_31_offered = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantThree, "31");
        $this->consultationRequest_31_offered->status = "offered";
        $this->consultationRequest_33_cancelled = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantThree, "33");
        $this->consultationRequest_33_cancelled->concluded = true;
        $this->consultationRequest_33_cancelled->status = "cancelled";
        $this->consultationRequest_34_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantThree, "34");
        $this->consultationRequest_34_accepted->concluded = true;
        $this->consultationRequest_34_accepted->status = "accepted";
        $this->consultationRequest_35_accepted = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultantThree, "35");
        $this->consultationRequest_35_accepted->concluded = true;
        $this->consultationRequest_35_accepted->status = "accepted";
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_30_proposed->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_31_offered->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_33_cancelled->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_34_accepted->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_35_accepted->toArrayForDbEntry());
        
        $this->consultationRequest_40_proposed = new RecordOfConsultationRequest($consultationSetup, $participantOne, $this->consultantFour_otherProgram, "40");
        $this->consultationRequest_42_rejected = new RecordOfConsultationRequest($consultationSetup, $participantOne, $this->consultantFour_otherProgram, "42");
        $this->consultationRequest_42_rejected->concluded = true;
        $this->consultationRequest_42_rejected->status = "rejected";
        $this->consultationRequest_43_cancelled = new RecordOfConsultationRequest($consultationSetup, $participantOne, $this->consultantFour_otherProgram, "43");
        $this->consultationRequest_43_cancelled->concluded = true;
        $this->consultationRequest_43_cancelled->status = "cancelled";
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_40_proposed->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_42_rejected->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest_43_cancelled->toArrayForDbEntry());
        
        $this->consultationSession_00 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultant, "00");
        $this->consultationSession_01 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultant, "01");
        $this->consultationSession_02 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultant, "02");
        $this->consultationSession_10 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultantOne, "10");
        $this->consultationSession_20 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultantTwo_removed, "20");
        $this->consultationSession_30 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultantThree, "30");
        $this->consultationSession_31 = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultantThree, "31");
        $this->consultationSession_40 = new RecordOfConsultationSession($consultationSetup, $participantOne, $this->consultantFour_otherProgram, "40");
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_00->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_01->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_02->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_10->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_20->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_30->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_31->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSession_40->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($form, 0);
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formTwo);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        $this->connection->table("WorksheetForm")->insert($worksheetFormOne->toArrayForDbEntry());
        
        $mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $this->connection->table("Mission")->insert($mission->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($missionOne->toArrayForDbEntry());
        
        $worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, 0);
        $worksheetOne = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, 1);
        $this->connection->table("Worksheet")->insert($worksheet->toArrayForDbEntry());
        $this->connection->table("Worksheet")->insert($worksheetOne->toArrayForDbEntry());
        
        $comment_00 = new RecordOfComment($worksheet, "_00");
        $comment_00->submitTime = (new \DateTime("-8 days"))->format("Y-m-d H:i:s");
        $comment_01 = new RecordOfComment($worksheet, "_01");
        $comment_01->submitTime = (new \DateTime("-12 hours"))->format("Y-m-d H:i:s");
        $comment_02 = new RecordOfComment($worksheet, "_02");
        $comment_02->submitTime = (new \DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $comment_03 = new RecordOfComment($worksheet, "_03");
        $comment_03->submitTime = (new \DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("Comment")->insert($comment_00->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_01->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_02->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_03->toArrayForDbEntry());
        
        $comment_10 = new RecordOfComment($worksheet, "_10");
        $comment_10->submitTime = (new \DateTime("-11 days"))->format("Y-m-d H:i:s");
        $comment_11 = new RecordOfComment($worksheet, "_11");
        $comment_11->submitTime = (new \DateTime("-4 hours"))->format("Y-m-d H:i:s");
        $comment_12 = new RecordOfComment($worksheet, "_12");
        $comment_12->submitTime = (new \DateTime("-4 days"))->format("Y-m-d H:i:s");
        $this->connection->table("Comment")->insert($comment_10->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_11->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_12->toArrayForDbEntry());
        
        $comment_20 = new RecordOfComment($worksheet, "_20");
        $comment_20->submitTime = (new \DateTime("-18 days"))->format("Y-m-d H:i:s");
        $comment_21 = new RecordOfComment($worksheet, "_21");
        $comment_21->submitTime = (new \DateTime("-8 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("Comment")->insert($comment_20->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_21->toArrayForDbEntry());
        
        $comment_30 = new RecordOfComment($worksheet, "_30");
        $comment_30->submitTime = (new \DateTime("-15 days"))->format("Y-m-d H:i:s");
        $this->connection->table("Comment")->insert($comment_30->toArrayForDbEntry());
        
        $comment_40 = new RecordOfComment($worksheet, "_40");
        $comment_40->submitTime = (new \DateTime("-17 days"))->format("Y-m-d H:i:s");
        $comment_41 = new RecordOfComment($worksheet, "_41");
        $comment_41->submitTime = (new \DateTime("-2 hours"))->format("Y-m-d H:i:s");
        $comment_42 = new RecordOfComment($worksheet, "_42");
        $comment_42->submitTime = (new \DateTime("-5 days"))->format("Y-m-d H:i:s");
        $comment_43 = new RecordOfComment($worksheet, "_43");
        $comment_43->submitTime = (new \DateTime("-2 days"))->format("Y-m-d H:i:s");
        $this->connection->table("Comment")->insert($comment_40->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_41->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_42->toArrayForDbEntry());
        $this->connection->table("Comment")->insert($comment_43->toArrayForDbEntry());
        
        $this->consultantComment_00 = new RecordOfConsultantComment($this->consultant, $comment_00);
        $this->consultantComment_01 = new RecordOfConsultantComment($this->consultant, $comment_01);
        $this->consultantComment_02 = new RecordOfConsultantComment($this->consultant, $comment_02);
        $this->consultantComment_03 = new RecordOfConsultantComment($this->consultant, $comment_03);
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_00->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_01->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_02->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_03->toArrayForDbEntry());
        
        $this->consultantComment_10 = new RecordOfConsultantComment($this->consultantOne, $comment_10);
        $this->consultantComment_11 = new RecordOfConsultantComment($this->consultantOne, $comment_11);
        $this->consultantComment_12 = new RecordOfConsultantComment($this->consultantOne, $comment_12);
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_10->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_11->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_12->toArrayForDbEntry());
        
        $this->consultantComment_20 = new RecordOfConsultantComment($this->consultantTwo_removed, $comment_20);
        $this->consultantComment_21 = new RecordOfConsultantComment($this->consultantTwo_removed, $comment_21);
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_20->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_21->toArrayForDbEntry());
        
        $this->consultantComment_30 = new RecordOfConsultantComment($this->consultantThree, $comment_30);
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_30->toArrayForDbEntry());
        
        $this->consultantComment_40 = new RecordOfConsultantComment($this->consultantFour_otherProgram, $comment_40);
        $this->consultantComment_41 = new RecordOfConsultantComment($this->consultantFour_otherProgram, $comment_41);
        $this->consultantComment_42 = new RecordOfConsultantComment($this->consultantFour_otherProgram, $comment_42);
        $this->consultantComment_43 = new RecordOfConsultantComment($this->consultantFour_otherProgram, $comment_43);
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_40->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_41->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_42->toArrayForDbEntry());
        $this->connection->table("ConsultantComment")->insert($this->consultantComment_43->toArrayForDbEntry());
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Worksheet")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("Comment")->truncate();
        $this->connection->table("ConsultantComment")->truncate();
    }
    public function test_showAll_200()
    {
        $totalResponse = [
            "total" => 3,
        ];
        $listConsultantResponse = [
            "id" => $this->consultant->id,
            "name" => $this->consultant->personnel->getFullName(),
            "consultationRequest" => [
                "total" => "7",
                "unconcluded" => "2",
                "accepted" => "3",
                "rejected" => "1",
                "cancelled" => "1",
            ],
            "comment" => [
                "total" => "4",
                "lastSevenDaysCount" => "3",
                "lastSubmitTime" => $this->consultantComment_01->comment->submitTime,
            ],
        ];
        $listConsultantOneResponse = [
            "id" => $this->consultantOne->id,
            "name" => $this->consultantOne->personnel->getFullName(),
            "consultationRequest" => [
                "total" => "1",
                "unconcluded" => null,
                "accepted" => "1",
                "rejected" => null,
                "cancelled" => null,
            ],
            "comment" => [
                "total" => "3",
                "lastSevenDaysCount" => "2",
                "lastSubmitTime" => $this->consultantComment_11->comment->submitTime,
            ],
        ];
        $listConsultantThreeResponse = [
            "id" => $this->consultantThree->id,
            "name" => $this->consultantThree->personnel->getFullName(),
            "consultationRequest" => [
                "total" => "5",
                "unconcluded" => "2",
                "accepted" => "2",
                "rejected" => null,
                "cancelled" => "1",
            ],
            "comment" => [
                "total" => "1",
                "lastSevenDaysCount" => null,
                "lastSubmitTime" => $this->consultantComment_30->comment->submitTime,
            ],
        ];
        
        $this->get($this->consultantSummaryUri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listConsultantResponse)
                ->seeJsonContains($listConsultantOneResponse)
                ->seeJsonContains($listConsultantThreeResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_withPagination()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->consultantOne->id,
                    "name" => $this->consultantOne->personnel->getFullName(),
                    "consultationRequest" => [
                        "total" => "1",
                        "unconcluded" => null,
                        "accepted" => "1",
                        "rejected" => null,
                        "cancelled" => null,
                    ],
                    "comment" => [
                        "total" => "3",
                        "lastSevenDaysCount" => "2",
                        "lastSubmitTime" => $this->consultantComment_11->comment->submitTime,
                    ],
                ],
            ],
        ];
        $uri = $this->consultantSummaryUri . "?page=2&pageSize=2";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->consultantSummaryUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}
