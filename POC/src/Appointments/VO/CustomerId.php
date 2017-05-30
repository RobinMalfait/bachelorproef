<?php namespace Skedify\Appointments\VO;

class CustomerId
{
    private $id;

    public function __construct(ID $id)
    {
        $this->id = $id;
    }

    public static function generate()
    {
        return new self(ID::generate());
    }

    public static function fromString($input)
    {
        return new self(ID::fromString($input));
    }

    public function toString()
    {
        return $this->id->getId();
    }

    public function __toString()
    {
        return $this->toString();
    }
}
