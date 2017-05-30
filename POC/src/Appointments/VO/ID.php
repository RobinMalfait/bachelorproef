<?php namespace Skedify\Appointments\VO;

class ID
{
    protected $id;

    protected function __construct($id)
    {
        $this->id = $id;
    }

    public static function generate()
    {
        return new self(id());
    }

    public static function fromString($input)
    {
        return new self($input);
    }

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->id;
    }
}
