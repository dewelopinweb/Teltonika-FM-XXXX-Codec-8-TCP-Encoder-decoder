<?php

namespace customsoft\teltonika\model;

use customsoft\teltonika\exception\TeltonikaException;

class IoData implements \JsonSerializable
{
    /**
     * @var int
     */
    private $eventId;

    /**
     * @var int
     */
    private $numberOfTotalIO;

    /**
     * @var int
     */
    private $numberOfOneByteIO;

    /**
     * @var array
     */
    private $oneByteIO = [];

    /**
     * @var int
     */
    private $numberOfTwoBytesIO;

    /**
     * @var array
     */
    private $twoBytesIO = [];

    /**
     * @var int
     */
    private $numberOfFourBytesIO;

    /**
     * @var array
     */
    private $fourBytesIO = [];

    /**
     * @var int
     */
    private $numberOfEightBytesIO;

    /**
     * @var array
     */
    private $eightBytesIO = [];

    public function __construct(
        int $eventId,
        int $numberOfTotalIO,
        int $numberOfOneByteIO,
        array $oneByteIO,
        int $numberOfTwoBytesIO,
        array $twoBytesIO,
        int $numberOfFourBytesIO,
        array $fourBytesIO,
        int $numberOfEightBytesIO,
        array $eightBytesIO
    )
    {
        $this->eventId = $eventId;
        $this->numberOfTotalIO = $numberOfTotalIO;
        $this->numberOfOneByteIO = $numberOfOneByteIO;
        $this->oneByteIO = $oneByteIO;
        $this->numberOfTwoBytesIO = $numberOfTwoBytesIO;
        $this->twoBytesIO = $twoBytesIO;
        $this->numberOfFourBytesIO = $numberOfFourBytesIO;
        $this->fourBytesIO = $fourBytesIO;
        $this->numberOfEightBytesIO = $numberOfEightBytesIO;
        $this->eightBytesIO = $eightBytesIO;
    }

    public static function decode(string $payload, int $startPosition):array
    {
        $position = $startPosition;

        $eventId = hexdec(substr($payload, $position, 2));
        $position += 2;

        $numberOfTotalIO = hexdec(substr($payload, $position, 2));
        $position += 2;

        //One Byte IO
        $numberOfOneByteIO = hexdec(substr($payload, $position, 2));
        $position += 2;

        $oneByteIO = [];

        if( $numberOfOneByteIO ){
            for ($i = 0; $i < $numberOfOneByteIO; $i++)
            {
                $id = hexdec(substr($payload, $position, 2));
                $position += 2;

                $value = hexdec(substr($payload, $position, 2));
                $position += 2;

                $oneByteIO[] = [
                    'id' => $id,
                    'value' => $value,
                ];
            }
        }

        //Two Bytes IO
        $numberOfTwoBytesIO = hexdec(substr($payload, $position, 2));
        $position += 2;

        $twoBytesIO = [];

        if( $numberOfTwoBytesIO ){
            for ($i = 0; $i < $numberOfTwoBytesIO; $i++)
            {
                $id = hexdec(substr($payload, $position, 2));
                $position += 2;

                $value = hexdec(substr($payload, $position, 4));
                $position += 4;

                $twoBytesIO[] = [
                    'id' => $id,
                    'value' => $value,
                ];
            }
        }

        //Four Bytes IO
        $numberOfFourBytesIO = hexdec(substr($payload, $position, 2));
        $position += 2;

        $fourBytesIO = [];

        if( $numberOfFourBytesIO ){
            for ($i = 0; $i < $numberOfFourBytesIO; $i++)
            {
                $id = hexdec(substr($payload, $position, 2));
                $position += 2;

                $value = hexdec(substr($payload, $position, 8));
                $position += 8;

                $fourBytesIO[] = [
                    'id' => $id,
                    'value' => $value,
                ];
            }
        }

        //Eight Bytes IO
        $numberOfEightBytesIO = hexdec(substr($payload, $position, 2));
        $position += 2;

        $eightBytesIO = [];

        if( $numberOfEightBytesIO ){
            for ($i = 0; $i < $numberOfEightBytesIO; $i++)
            {
                $id = hexdec(substr($payload, $position, 2));
                $position += 2;

                $value = hexdec(substr($payload, $position, 16));
                $position += 16;

                $eightBytesIO[] = [
                    'id' => $id,
                    'value' => $value,
                ];
            }
        }

        return [
            'data' => new self($eventId, $numberOfTotalIO, $numberOfOneByteIO, $oneByteIO, $numberOfTwoBytesIO, $twoBytesIO, $numberOfFourBytesIO, $fourBytesIO, $numberOfEightBytesIO, $eightBytesIO),
            'length' => $position - $startPosition,
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'eventId' => $this->eventId,
            'numberOfTotalIO' => $this->numberOfTotalIO,
            'numberOfOneByteIO' => $this->numberOfOneByteIO,
            'oneByteIO' => $this->oneByteIO,
            'numberOfTwoBytesIO' => $this->numberOfTwoBytesIO,
            'twoBytesIO' => $this->twoBytesIO,
            'numberOfFourBytesIO' => $this->numberOfFourBytesIO,
            'fourBytesIO' => $this->fourBytesIO,
            'numberOfEightBytesIO' => $this->numberOfEightBytesIO,
            'eightBytesIO' => $this->eightBytesIO,
        ];
    }
}