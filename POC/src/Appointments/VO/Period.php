<?php namespace Skedify\Appointments\VO;

use DateTime;

class Period
{
    private $startTimestamp;

    private $endTimestamp;

    private function __construct(DateTime $start, DateTime $end)
    {
        $this->startTimestamp = $start->getTimestamp();
        $this->endTimestamp = $end->getTimestamp();
    }

    public static function fromTimestamps($start, $end)
    {
        return new self(static::timeToDate($start), static::timeToDate($end));
    }

    private static function timeToDate($time)
    {
        $date = new DateTime();
        $date->setTimestamp($time);
        return $date;
    }

    public function getStartTimestamp()
    {
        return $this->startTimestamp;
    }

    public function getStartDate()
    {
        return static::timeToDate(($this->startTimestamp));
    }

    public function getEndTimestamp()
    {
        return $this->endTimestamp;
    }

    public function getEndDate()
    {
        return static::timeToDate(($this->endTimestamp));
    }

    public function toJson()
    {
        return json_encode([
            'start' => $this->startTimestamp,
            'end' => $this->endTimestamp
        ]);
    }
}
