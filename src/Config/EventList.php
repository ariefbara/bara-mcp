<?php

namespace Config;

class EventList
{
    const CLIENT_REGISTERED = "client-registered";
    const CLIENT_REGISTRATION_ACCEPTED = "client-registration-accepted";
    
    const USER_REGISTERED = "user-registered";
    const USER_REGISTRATION_ACCEPTED = "user-registration-accepted";
    
    const CLIENT_PROPOSED_CONSULTATION_REQUEST = 'client-proposed-consultation-request';
    const CLIENT_CHANGED_CONSULTATION_REQUEST_TIME = 'client-changed-consultation-request-time';
    const CLIENT_ACCEPTED_CONSULTATION_REQUEST = 'client-accepted-consultation-request';
    
    const USER_PROPOSED_CONSULTATION_REQUEST = 'user-proposed-consultation-request';
    const USER_CHANGED_CONSULTATION_REQUEST_TIME = 'user-changed-consultation-request-time';
    const USER_ACCEPTED_CONSULTATION_REQUEST = 'user-accepted-consultation-request';
    
    const CONSULTANT_OFFERED_CONSULTATION_REQUEST = 'consultant-offered-consultation-request';
    const CONSULTANT_ACCEPTED_CONSULTATION_REQUEST = 'consultant-accepted-consultation-request';
    
    
    const CONSULTANT_COMMENT_REPLIED_BY_CLIENT_PARTICIPANT = 'consultation-comment-replied-by-client-participant';
    
}
