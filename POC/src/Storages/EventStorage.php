<?php namespace Skedify\Storages;

interface EventStorage
{
    public function storeEvent($rootId, $version, $event);

    public function loadAll();

    public function searchEventsFor($id, callable $map);
}
