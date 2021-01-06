<?php

namespace customsoft\teltonika\model;

class Command implements \JsonSerializable
{
    public const TYPE_REQUEST  = 'request';
    public const TYPE_RESPONSE = 'response';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $data;

    public function __construct(
        string $type,
        string $data
    )
    {
        $this->type = $type;
        $this->data = $data;
    }

    public static function decode(string $payload, int $startPosition = 0):array
    {
        $position = $startPosition;

        $commandSize = (int)hexdec(substr($payload, $position, 8));
        $position += 8;

        $command = substr($payload, $position, $commandSize*2);
        $position += $commandSize*2;

        $char = [];

        for($i = 0; $i < $commandSize; $i++){
            $char[] = \chr(hexdec(substr($command, $i*2, 2)));
        }

        $data = implode('', $char);

        return [
            'data' => new self(self::TYPE_RESPONSE, $data),
            'length' => $position - $startPosition,
        ];
    }

    public static function encode(string $command):Command
    {
        $hex = [];

        $chars = str_split($command);

        foreach ($chars as $char){
            $hex[] = dechex(\ord($char));
            //$hex[] = str_pad(dechex(\ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $hex[] = '0D';
        $hex[] = '0A';

        $data = str_pad(dechex(\count($hex)), 8, '0', STR_PAD_LEFT).implode('', $hex);

        return new self(self::TYPE_REQUEST, $data);

        /*
        $hexCommand[] = str_pad(dechex(\count($hex)), 8, '0', STR_PAD_LEFT).implode('', $hex);

        $hexCommand = [];

        foreach ($commands as $command){
            $hex = [];

            $command = str_split($command);

            foreach ($command as $char){
                $hex[] = dechex(\ord($char));
            }

            $hexCommand[] = str_pad(dechex(\count($hex)), 8, '0', STR_PAD_LEFT).implode('', $hex);
        }

        $data = implode('', $hexCommand);

        return new self(self::TYPE_REQUEST, $data);
        */
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}