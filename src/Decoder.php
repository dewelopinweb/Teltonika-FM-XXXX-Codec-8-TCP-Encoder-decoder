<?php
namespace customsoft\teltonika;

use customsoft\teltonika\model\Crc;
use customsoft\teltonika\model\Imei;
use customsoft\teltonika\model\AvlData;
use customsoft\teltonika\model\Command;
use customsoft\teltonika\exception\TeltonikaException;

class Decoder
{
    public static function codec(string $payload):string
    {
        $data = substr($payload, 16, -8);

        $codecId = substr($data, 0, 2);

        $codecId = strtoupper($codecId);

        if( $codecId === '08' ){
            return '8';
        }elseif( $codecId === '0C' ){
            return '12';
        }else{
            throw new TeltonikaException('Codec not supported');
        }
    }

    public static function imei(string $payload): Imei
    {
        $hex = substr($payload, 4);

        return Imei::decode($hex);
    }

    public static function data(string $payload): array
    {
        $crc = substr($payload, \strlen($payload) - 8, 8);

        $header = substr($payload, 0, 16);

        $data = substr($payload, 16, -8);

        if( (string)Crc::calculate($data) !== strtoupper($crc) ){
            throw new TeltonikaException('CRC16 does not match');
        }

        $codecId = substr($data, 0, 2);

        /*
        // Validating number of data;
        if (substr($data, 2, 2) !== substr($data, \strlen($data) - 2, 2)) {
            throw new TeltonikaException('First element count check is different than last element count check');
        }
        */

        $numberOfData = hexdec(substr($data, 2, 2));

        $avlData = substr($data, 4, -2);

        $position = 0;
        $avl = [];

        for ($i = 0; $i < $numberOfData; $i++) {
            $result = AvlData::decode($avlData, $position);

            $position += $result['length'];

            $avl[] = $result['data'];
        }

        return [
            'header' => $header,
            'codecId' => $codecId,
            'crc' => $crc,
            'numberOfData' => $numberOfData,
            'avl' => $avl
        ];
    }

    public static function command(string $payload): array
    {
        $header = substr($payload, 0, 16);

        $crc = substr($payload, \strlen($payload) - 8, 8);

        $data = substr($payload, 16, -8);

        if( (string)Crc::calculate($data) !== strtoupper($crc) ){
            throw new TeltonikaException('CRC16 does not match');
        }

        $codecId = substr($data, 0, 2);

        /*
        // Validating number of data;
        if (substr($data, 2, 2) !== substr($data, \strlen($data) - 2, 2)) {
            throw new TeltonikaException('First element count check is different than last element count check');
        }
        */

        $commandQuantity = hexdec(substr($data, 2, 2));

        $type = substr($data, 4, 2);

        if( $type !== '06' ){
            throw new TeltonikaException('0x06 to denote response does not match');
        }

        $commandData = substr($data, 6, -2);

        $position = 0;
        $command = [];

        for ($i = 0; $i < $commandQuantity; $i++) {
            $result = Command::decode($commandData, $position);

            $position += $result['length'];

            $command[] = $result['data'];
        }

        return [
            'header' => $header,
            'codecId' => $codecId,
            'crc' => $crc,
            'numberOfData' => $commandQuantity,
            'command' => $command
        ];
    }
}