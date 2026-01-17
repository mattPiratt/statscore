<?php

namespace App\Statistics\Infrastructure\Persistence;

use App\Statistics\Domain\Event\GameEventInterface;
use App\Statistics\Domain\Repository\EventsStoreInterface;
use App\Statistics\Infrastructure\File\FileStorage;

class EventsStore implements EventsStoreInterface
{
    private FileStorage $storage;

    public function __construct(string $eventsFile = '../storage/events.txt')
    {
        $this->storage = new FileStorage($eventsFile);
    }

    public function save(GameEventInterface $event): void
    {
        $this->storage->save($event->toArray());
    }
}
