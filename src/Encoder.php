<?php
namespace customsoft\teltonika;

use customsoft\teltonika\model\Command;
use customsoft\teltonika\exception\TeltonikaException;
use customsoft\teltonika\model\Crc;

class Encoder
{
    public function data(int $data): string
    {
        if ($data < 0) {
            throw new TeltonikaException('Incorrect data');
        }

        return dechex($data);
    }

    public static function commands(array $commands):string
    {
        $header = '00000000';

        $codecId = '0C';

        $commandQuantity = str_pad(dechex(\count($commands)), 2, '0', STR_PAD_LEFT);

        $type = '05';

        $hexCommand = [];

        foreach ($commands as $command){
            $command = json_decode(json_encode(Command::encode($command)));

            $hexCommand[] = $command->data;
        }

        $hexCommands = implode('', $hexCommand);

        $data = $codecId.$commandQuantity.$type.$hexCommands.$commandQuantity;

        $crc = Crc::calculate($data);

        $dataSize = str_pad(dechex(\strlen($data)/2), 8, '0', STR_PAD_LEFT);

        return strtoupper($header.$dataSize.$data.$crc);
    }
}