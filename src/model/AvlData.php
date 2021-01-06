<?php

namespace customsoft\teltonika\model;

class AvlData implements \JsonSerializable
{
    /**
     * @var string
     */
    private $date;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var GpsData
     */
    private $gps;

    /**
     * @var IoData
     */
    private $io;

    public function __construct(
        string $date,
        int $priority,
        GpsData $gps,
        IoData $io
    )
    {
        $this->date = $date;
        $this->priority = $priority;
        $this->gps = $gps;
        $this->io = $io;
    }

    public static function decode(string $payload, int $startPosition = 0):array
    {
        $position = $startPosition;

        $date = date('Y-m-d H:i:s', (int)(hexdec(substr($payload, $position, 16))/1000));
        $position += 16;

        $priority = (int)hexdec(substr($payload, $position, 2));
        $position += 2;

        $gps = GpsData::decode($payload, $position);
        $position += $gps['length'];

        $io = IoData::decode($payload, $position);
        $position += $io['length'];

        return [
            'data' => new self($date, $priority, $gps['data'], $io['data']),
            'length' => $position - $startPosition,
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date,
            'priority' => $this->priority,
            'gps' => $this->gps,
            'io' => $this->io
        ];
    }
}
