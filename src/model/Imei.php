<?php

namespace customsoft\teltonika\model;

use customsoft\teltonika\exception\TeltonikaException;

class Imei
{
    public const LENGTH = 15;

    /**
     * @var string
     */
    private $imei;

    /**
     * @param string $imei
     *
     * @throws TeltonikaException
     */
    public function __construct(string $imei)
    {
        if (!$this->isLuhn($imei) || \strlen($imei) !== self::LENGTH) {
            throw new TeltonikaException('IMEI is not valid.');
        }

        $this->setImei($imei);
    }

    /**
     * @return string
     */
    public function getImei(): string
    {
        return $this->imei;
    }

    /**
     * @param string $imei
     */
    public function setImei(string $imei): void
    {
        $this->imei = $imei;
    }

    public function __toString()
    {
        return $this->imei;
    }

    /**
     * @param string $imei
     *
     * @return bool
     */
    private function isLuhn(string $imei): bool
    {
        $str = '';
        foreach (str_split(strrev($imei)) as $i => $d) {
            $str .= $i % 2 !== 0 ? $d * 2 : $d;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    public static function decode(string $hex): Imei
    {
        $imei = hex2bin($hex);

        return new Imei($imei);
    }
}
