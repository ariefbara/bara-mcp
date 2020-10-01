<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\Participant\Worksheet\RecordOfConsultantComment,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultantCommentControllerTest extends ProgramConsultationTestCase
{

    protected $consultantCommentUri;
    protected $client;
    protected $worksheet;
    protected $consultantComment;
    protected $commentOne;
    protected $submitNewRequest;
    protected $submitReplyRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantCommentUri = $this->programConsultationUri . "/consultant-comments";

        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('ClientNotification')->truncate();

        $participant = new RecordOfParticipant($this->programConsultation->program, 0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $worksheetForm = new RecordOfWorksheetForm($this->programConsultation->program->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());

        $mission = new RecordOfMission($this->programConsultation->program, $worksheetForm, 0, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheet = new RecordOfWorksheet($participant, $formRecord, $mission, 0);
        $this->connection->table('Worksheet')->insert($this->worksheet->toArrayForDbEntry());

        $comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());

        $this->consultantComment = new RecordOfConsultantComment($this->programConsultation, $comment);
        $this->connection->table('ConsultantComment')->insert($this->consultantComment->toArrayForDbEntry());

        $this->submitNewRequest = [
            "participantId" => $participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
        $this->submitReplyRequest = [
            "commentId" => $this->commentOne->id,
            "participantId" => $participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('ClientNotification')->truncate();
    }

    public function test_submitNew()
    {
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Comment')->truncate();

        $response = [
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "message" => $this->submitNewRequest['message'],
            "removed" => false,
            "participant" => null,
        ];

        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitNewRequest['message'],
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "parent_id" => null,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }
    
    public function test_submitReply()
    {
        $response = [
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "message" => $this->submitReplyRequest['message'],
            "removed" => false,
        ];

        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitReplyRequest['message'],
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "parent_id" => $this->commentOne->id,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }
    
    public function test_remove()
    {
        $uri = $this->consultantCommentUri . "/{$this->consultantComment->id}";
        $this->delete($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $commentEntry = [
            "id" => $this->consultantComment->comment->id,
            "removed" => true,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
    }

}
