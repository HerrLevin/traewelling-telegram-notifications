<?php

namespace App;

enum NotificationEnum: string
{

    case STATUS_LIKED = 'StatusLiked';
    case USER_JOINED_CONNECTION = 'UserJoinedConnection';
    case USER_FOLLOWED = 'UserFollowed';
    case MASTODON_NOT_SENT = 'MastodonNotSent';
    case FOLLOW_REQUEST_ISSUED = 'FollowRequestIssued';
    case FOLLOW_REQUEST_ACCEPTED = 'FollowRequestAccepted';
    case EVENT_SUGGESTION_PROCESSED = 'EventSuggestionProcessed';
    case DEFAULT = 'default';

    public function getNotificationMessage(array $data): string
    {
        $data = $data['data'];
        return match ($this) {
            self::STATUS_LIKED => sprintf(
                '%s hat deine Fahrt mit %s von %s nach %s geliked',
                $data['liker']['username'],
                $data['trip']['lineName'],
                $data['trip']['origin']['name'],
                $data['trip']['destination']['name']
            ),
            self::USER_JOINED_CONNECTION => sprintf(
                '%s ist auch in deiner Fahrt mit %s von %s nach %s',
                $data['user']['username'],
                $data['checkin']['linename'],
                $data['checkin']['origin'],
                $data['checkin']['destination']
            ),
            self::USER_FOLLOWED => sprintf(
                '%s folgt dir jetzt',
                $data['follower']['username']
            ),
            self::MASTODON_NOT_SENT => sprintf(
                'Dein Status mit der id %s konnte nicht auf Mastodon geteilt werden',
                $data['status']['id']
            ),
            self::FOLLOW_REQUEST_ISSUED => sprintf(
                '%s hat dir eine Anfrage zum Folgen geschickt',
                $data['user']['username']
            ),
            self::FOLLOW_REQUEST_ACCEPTED => sprintf(
                '%s hat deine Anfrage zum Folgen akzeptiert',
                $data['user']['username']
            ),
            self::EVENT_SUGGESTION_PROCESSED => sprintf(
                'Dein Vorschlag für das Event %s wurde %s',
                $data['event']['name'],
                $data['event']['accepted']
                    ? 'angenommen'
                    : sprintf('abgelehnt mit der Begründung: %s', $data['event']['rejectionReason'])
            ),
            self::DEFAULT => $data['lead'],

        };
    }

    public function getEmoji() {
        return match ($this) {
            self::STATUS_LIKED => '⭐️',
            self::USER_JOINED_CONNECTION => '👥',
            self::USER_FOLLOWED => '👤',
            self::MASTODON_NOT_SENT => '❌',
            self::FOLLOW_REQUEST_ISSUED => '👤',
            self::FOLLOW_REQUEST_ACCEPTED => '👤',
            self::EVENT_SUGGESTION_PROCESSED => '📅',
            self::DEFAULT => '❓',
        };
    }
}
