<?php

namespace SharedContext\Domain\ValueObject;

class NotificationMessageBuilder
{

    public static function buildConsultationNotificationMessageForMentor(int $state, ?string $participantName): string
    {
        switch ($state) {
            case MailMessageBuilder::CONSULTATION_REQUESTED:
                return "participant {$participantName} requested consultation";
                break;
            case MailMessageBuilder::CONSULTATION_CANCELLED:
                return "participant {$participantName} cancelled consultation request";
                break;
            case MailMessageBuilder::CONSULTATION_SCHEDULE_CHANGED:
                return "participant {$participantName} changed consultation request schedule";
                break;
            case MailMessageBuilder::CONSULTATION_ACCEPTED_BY_PARTICIPANT:
                return "participant {$participantName} accepted consultation schedule suggestion";
                break;
            default:
                break;
        }
    }

    public static function buildConsultationNotificationMessageForTeamMember(
            int $state, ?string $mentorName, ?string $memberName, ?string $teamName): string
    {
        switch ($state) {
            case MailMessageBuilder::CONSULTATION_REQUESTED:
                return "partner {$memberName} of team {$teamName} requested consultation with mentor {$mentorName}";
                break;
            case MailMessageBuilder::CONSULTATION_CANCELLED:
                return "partner {$memberName} of team {$teamName} cancelled consultation request to mentor {$mentorName}";
                break;
            case MailMessageBuilder::CONSULTATION_SCHEDULE_CHANGED:
                return "partner {$memberName} of team {$teamName} changed consultation request schedule to mentor {$mentorName}";
                break;
            case MailMessageBuilder::CONSULTATION_ACCEPTED_BY_MENTOR:
                return "mentor {$mentorName} accepted consultation request with your team {$teamName}";
                break;
            case MailMessageBuilder::CONSULTATION_ACCEPTED_BY_PARTICIPANT:
                return "partner {$memberName} of team {$teamName} accepted consultation schedule offered by mentor {$mentorName}";
                break;
            default:
                break;
        }
    }

    public static function buildConsultationNotificationMessageForParticipant(
            int $state, ?string $mentorName): string
    {
        switch ($state) {
            case MailMessageBuilder::CONSULTATION_REJECTED:
                return "mentor {$mentorName} rejected consultation request";
                break;
            case MailMessageBuilder::CONSULTATION_SCHEDULE_CHANGED:
                return "mentor {$mentorName} offered different consultation schedule time";
                break;
            case MailMessageBuilder::CONSULTATION_ACCEPTED_BY_MENTOR:
                return "mentor {$mentorName} accepted consultation request";
                break;
            default:
                break;
        }
    }

    public static function buildConsultationNotificationMessageForCoordinator(?string $mentorName,
            ?string $participantName): string
    {
        return "a new consultation between mentor {$mentorName} and participant {$participantName} has been scheduled";
    }

    public static function buildWorksheetCommentNotificationForMentor(?string $participantName): string
    {
        return "participant {$participantName} replied your comment";
    }

    public static function buildWorksheetCommentNotificationForParticipant(?string $mentorName): string
    {
        return "mentor {$mentorName} submit new comment on your worksheet";
    }
    
    public static function buildMeetingNotification(int $state): string 
    {
        switch ($state) {
            case MailMessageBuilder::MEETING_CREATED:
                return "new meeting scheduled";
                break;
            case MailMessageBuilder::MEETING_INVITATION_SENT:
                return "meeting invitation received";
                break;
            case MailMessageBuilder::MEETING_INVITATION_CANCELLED:
                return "meeting invitation cancelled";
                break;
            case MailMessageBuilder::MEETING_SCHEDULE_CHANGED:
                return "meeting scheduled changed";
                break;
            default:
                break;
        }
    }

}
