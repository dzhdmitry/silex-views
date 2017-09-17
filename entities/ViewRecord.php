<?php

namespace Entity;

class ViewRecord
{
    const TYPE_LOADED = 'loaded';
    const TYPE_PROGRESS = 'progress';
    const TYPE_FINISHED = 'finished';

    /**
     * @var string
     */
    public $cookie;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $payload;

    public function __construct($cookie, $type, $payload)
    {
        $this->cookie = $cookie;
        $this->type = $type;
        $this->payload = $payload;
    }

    public static function getTypes()
    {
        return [
            self::TYPE_LOADED,
            self::TYPE_PROGRESS,
            self::TYPE_FINISHED,
        ];
    }
}
