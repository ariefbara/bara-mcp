<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

class UploadFileControllerTest extends ExtendedTeamParticipantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('ParticipantFileInfo');
        $this->connection->table('FileInfo');
        
        $this->setupFileUpload('participant');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('ParticipantFileInfo');
        $this->connection->table('FileInfo');
        
        $this->clearFileUpload('participant');
    }
    
    protected function upload()
    {
        $this->prepareRecord();
        
        $uri = $this->teamParticipantUri . "/upload-file";
// echo $uri;
        
        $header = $this->client->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($uri, $this->fileUploadInput, $header);
    }
    public function test_upload_201()
    {
$this->disableExceptionHandling();
        $this->upload();
        $this->seeStatusCode(201);
        
        $this->seeJsonContains([
            'path' => DIRECTORY_SEPARATOR . 'participant' . DIRECTORY_SEPARATOR . $this->teamParticipant->participant->id . DIRECTORY_SEPARATOR . 'cat_pile.jpg',
        ]);
        
        $participantFileInfoEntry = [
            'Participant_id' => $this->teamParticipant->participant->id,
        ];
        $this->seeInDatabase('ParticipantFileInfo', $participantFileInfoEntry);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
    public function test_upload_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        
        $this->upload();
        $this->seeStatusCode(403);
    }
}
