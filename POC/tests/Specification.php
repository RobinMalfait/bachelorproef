<?php

use Skedify\Core\AggregateRoot;
use Skedify\EventSourcing\AggregateClassNotFoundException;
use Skedify\EventSourcing\EventSourcingRepository;

abstract class Specification extends PHPUnit_Framework_TestCase
{
    /**
     * If an exception is thrown, it will be caught in here
     */
    protected $caughtException;

    /**
     * The events generated by the command
     *
     * @var array
     */
    protected $producedEvents = [];

    /**
     * Current state
     *
     * @var AggregateRoot
     */
    protected $aggregate;

    /**
     * Given events to build the aggregate
     *
     * @return array
     */
    abstract public function given();

    /**
     * Command to fire
     *
     * @return Command
     */
    abstract public function when();

    /**
     * The command handler
     *
     * @param $repository
     * @return mixed
     */
    abstract public function handler($repository);

    /**
     * @param String $exception
     */
    protected function throws($exception)
    {
        $this->assertInstanceOf($exception, $this->caughtException);
    }

    /**
     * Setup
     */
    public function setUp()
    {
        try {
            $events = $this->given();

            $fakeRepository = new FakeRepository($events);

            $this->handler($fakeRepository)->handle($this->when());

            $this->producedEvents = $fakeRepository->produced;
            $this->aggregate = $fakeRepository->aggregate;
        } catch (Exception $e) {
            $this->caughtException = $e;
        }
    }
}

class FakeRepository implements EventSourcingRepository
{
    public $aggregate;
    public $previousEvents;
    public $produced;
    public $aggregateClass;

    /**
     * @param $events
     */
    public function __construct($events)
    {
        $this->previousEvents = $events;
    }

    /**
     * @param $class
     * @return mixed
     */
    public function setAggregateClass($class)
    {
        $this->aggregateClass = $class;
    }

    /**
     * @param $id
     * @return mixed
     * @throws AggregateClassNotFoundException
     */
    public function load($id)
    {
        $subject = $this->aggregateClass;

        if (! $subject) {
            throw new AggregateClassNotFoundException();
        }

        return $subject::replayEvents($this->previousEvents);
    }

    /**
     * @param $aggregate
     * @return void
     */
    public function save($aggregate)
    {
        $this->produced = $aggregate->releaseEvents();

        foreach ($this->produced as $event) {
            $aggregate->applyAnEvent($event);
        }

        $this->aggregate = $aggregate;
    }
}
