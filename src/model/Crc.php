<?php

namespace customsoft\teltonika\model;

use customsoft\teltonika\exception\TeltonikaException;

class Crc
{
    /**
     * @var string
     */
    private $crc;

    /**
     * @param string $crc
     *
     */
    public function __construct(string $crc)
    {
        $this->setCrc($crc);
    }

    /**
     * @return string
     */
    public function getCrc(): string
    {
        return $this->crc;
    }

    /**
     * @param string $crc
     */
    public function setCrc(string $crc): void
    {
        $this->crc = $crc;
    }

    public function __toString()
    {
        return $this->crc;
    }

    /**
     * @param string $str
     * @param int $polynom
     * @param int $preset
     *
     * @return Crc
     */
    public static function calculate(string $str, int $polynom = 0xA001, int $preset = 0): Crc
    {
        $numberOfBytes = (int)(\strlen($str)/2);

        $preset &= 0xFFFF;
        $polynom &= 0xFFFF;
        $crc = $preset;

        for ($i = 0; $i < $numberOfBytes; $i++){
            $hexCode = substr($str, $i*2, 2);

            $data = hexdec($hexCode) & 0xFF;

            $crc ^= $data;

            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x0001) !== 0) {
                    $crc = ($crc >> 1) ^ $polynom;
                } else {
                    $crc >>= 1;
                }
            }
        }

        $crc = sprintf('%04X', $crc & 0xFFFF);

        $crc = str_pad($crc, 8, '0', STR_PAD_LEFT);

        $crc = strtoupper($crc);

        return new self($crc);
    }
}
