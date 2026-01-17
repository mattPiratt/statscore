<?php

namespace App\Statistics\Infrastructure\Service;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Service\RealTimeNotifierInterface;

class LogRealTimeNotifier implements RealTimeNotifierInterface
{
    public function notify(GameEventInterface $event): void
    {
        // TODO: In real application this could be WebSocket, RabbitMQ/SQS, Mobile Push Notifications, etc.
        error_log(
            sprintf(
                "Real-time notification sent for event: %s (Match: %s, Team: %s)",
                $event->type()->value,
                $event->matchId(),
                $event->teamId()
            )
        );
    }
}
