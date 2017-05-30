<?php namespace Skedify\EventSourcing\Serialization;

class Serializer
{
    /**
     * @param $object
     * @return array
     */
    public static function serialize(Serializable $object)
    {
        return [
            'class' => get_class($object),
            'payload' => $object->serialize()
        ];
    }

    /**
     * @param $class
     * @param $payload
     *
     * @return mixed
     */
    public static function deserialize($class, $payload)
    {
        return $class::deserialize($payload);
    }
}
