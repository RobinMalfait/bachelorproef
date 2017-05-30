<?php

use Skedify\EventSourcing\Events\Dispatcher;
use Skedify\EventSourcing\EventSourcingRepository;
use Skedify\EventSourcing\EventStore;
use Skedify\EventSourcing\EventStoreRepository;
use Skedify\Storages\EventStorage;
use Skedify\Storages\FileStorage;

require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set("Europe/Brussels");

// Some 'Databases'
$eventStorageDatabase = 'database/.events';
$appointments = 'database/appointments.db.json';

/* ---- DO NOT DO THIS IN PRODUCTION ---- */
setupDatabase($eventStorageDatabase);
setupDatabase($appointments);
/* ---- DO NOT DO THIS IN PRODUCTION ---- */

// Doing some bindings
app()->bind(EventStorage::class, function () use ($eventStorageDatabase) {
    return new FileStorage($eventStorageDatabase);
});

app()->bind(EventSourcingRepository::class, function () {
    return app()->make(EventStoreRepository::class);
});

app()->singleton(Dispatcher::class);

// Setup the event store and event dispatcher
$eventStore = app(EventStore::class);
$eventDispatcher = app(Dispatcher::class);
