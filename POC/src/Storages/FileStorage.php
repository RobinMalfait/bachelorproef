<?php namespace Skedify\Storages;

use DateTime;

final class FileStorage implements EventStorage
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function storeEvent($rootId, $version, $event)
    {
        $events = $this->loadAll();

        $events[] = [
            'id' => count($events),
            'stream_id' => $rootId,
            'stream_version' => $version,
            'event_name' => $event['class'],
            'payload' => json_encode($event['payload']),
            'recorded_at' => (new DateTime())->getTimestamp(),
        ];

        file_put_contents($this->file, json_encode($events, JSON_PRETTY_PRINT));
    }

    public function loadAll()
    {
        $contents = file_get_contents($this->file);
        $events = [];

        if ($contents) {
            $events = json_decode($contents, true);
        }

        return $events;
    }

    public function searchEventsFor($id, callable $map)
    {
        $events = [];

        foreach ($this->loadAll() as $event) {
            $event = $this->objectifyEvent($event);

            if ($event->stream_id == $id) {
                $events[] = $map($event->event_name, $event->payload);
            }
        }

        return $events;
    }

    private function objectifyEvent($event)
    {
        return json_decode(json_encode($event));
    }
}
