<?php

namespace Config;

class EventList
{
    const CLIENT_REGISTERED = "client-registered";
    
    const USER_REGISTERED = "user-registered";
    const REGISTRANT_ACCEPTED = "user-registration-accepted";
    
    const CLIENT_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST = 'client-participant-proposed-consultation-request';
    const CLIENT_PARTICIPANT_CHANGED_CONSULTATION_REQUEST_TIME = 'client-participant-changed-consultation-request-time';
    const CLIENT_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST = 'client-participant-accepted-consultation-request';
    
    const USER_PARTICIPANT_PROPOSED_CONSULTATION_REQUEST = 'user-participant-proposed-consultation-request';
    const USER_PARTICIPANT_CHANGED_CONSULTATION_REQUEST_TIME = 'user-participant-changed-consultation-request-time';
    const USER_PARTICIPANT_ACCEPTED_CONSULTATION_REQUEST = 'user-participant-accepted-consultation-request';
    
    const CONSULTANT_OFFERED_CONSULTATION_REQUEST = 'consultant-offered-consultation-request';
    const CONSULTANT_ACCEPTED_CONSULTATION_REQUEST = 'consultant-accepted-consultation-request';
    const CONSULTANT_SUBMITTED_COMMENT_ON_WORKSHEET = 'consultant-submitted-comment-on-worksheet';
    
    
    const CLIENT_PARTICIPANT_REPLIED_CONSULTANT_COMMENT = 'client-participant-replied-consultant-comment';
    const USER_PARTICIPANT_REPLIED_CONSULTANT_COMMENT = 'user-participant-replied-consultant-comment';
    
}
