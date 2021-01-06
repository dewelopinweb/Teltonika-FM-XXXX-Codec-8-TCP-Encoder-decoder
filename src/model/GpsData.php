<?php

namespace customsoft\teltonika\model;

class GpsData implements \JsonSerializable
{
    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var int
     */
    private $altitude;

    /**
     * @var int
     */
    private $angle;

    /**
     * @var int
     */
    private $satellites;

    /**
     * @var int
     */
    private $speed;

    public function __construct(
        float $longitude,
        float $latitude,
        int $altitude,
        int $angle,
        int $satellites,
        int $speed
    )
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->altitude = $altitude;
        $this->angle = $angle;
        $this->satellites = $satellites;
        $this->speed = $speed;
    }

    public static function decode(string $payload, int $startPosition):array
    {
        $position = $startPosition;

        $longitude = hexdec(substr($payload, $position, 8))/10000000;
        $position += 8;

        $latitude = hexdec(substr($payload, $position, 8))/10000000;
        $position += 8;

        $altitude = hexdec(substr($payload, $position, 4));
        $position += 4;

        $angle = hexdec(substr($payload, $position, 4));
        $position += 4;

        $satellites = hexdec(substr($payload, $position, 2));
        $position += 2;

        $speed = hexdec(substr($payload, $position, 4));
        $position += 4;

        return [
            'data' => new self($longitude, $latitude, $altitude, $angle, $satellites, $speed),
            'length' => $position - $startPosition,
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'altitude' => $this->altitude,
            'angle' => $this->angle,
            'satellites' => $this->satellites,
            'speed' => $this->speed
        ];
    }
}
