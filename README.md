# Teltonika FM-XXXX TCP Encoder/decoder

Protocol documentation
https://wiki.teltonika.lt/view/Codec

Implementation for Codec 8
https://wiki.teltonika-gps.com/view/Codec#Codec_8

Device
https://wiki.teltonika.lt/view/FMB120

Decoding by IO elements
https://wiki.teltonika.lt/view/FMB_AVL_ID

Links
- https://github.com/uro/teltonika-fm-parser
- https://github.com/cf-git/teltonika-functions/blob/master/teltonika.php

### Usage
```php
<?php
$ip = '0.0.0.0';
$port = '10100';

require_once('vendor/autoload.php');

use React\Socket\ConnectionInterface;
use customsoft\teltonika\Decoder;
use customsoft\teltonika\Encoder;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($ip.':'.$port, $loop);

$socket->on('connection', function(ConnectionInterface $connection){

    logging('CONNECTION', [
        'IP' => $connection->getRemoteAddress()
    ]);

    $imei = '';

    $connection->on('data', function ($data) use ($connection, &$imei) {

        logging('RECEIVING_DATA', [
            'DATA' => bin2hex($data),
            'IP' => $connection->getRemoteAddress()
        ]);

        if(strlen($data) === 17) {
            $data = bin2hex($data);

            $imei = (string)Decoder::imei($data);

            $response = '01';//(Binary packet => 01)
            $connection->write(hex2bin($response));

            logging('RESPONSE_IMEI', [
                'IMEI' => $imei,
                'REQUEST' => $data,
                'RESPONSE' => $response,
                'IP' => $connection->getRemoteAddress()
            ]);
        }else{
            try {
                $data = bin2hex($data);

                $codecVersion = Decoder::codec($data);

                if( $codecVersion !== '8' ){
                    $connection->close();
                }

                $decodeData = Decoder::data($data);

                $response = $decodeData['numberOfData'];

                $response = str_pad(dechex($response), 8, '0', STR_PAD_LEFT);

                $connection->write(hex2bin($response));

                logging('RESPONSE_DATA', [
                    'IMEI' => $imei,
                    'REQUEST' => $data,
                    'DECODE_REQUEST' => $decodeData,
                    'RESPONSE' => $response,
                    'IP' => $connection->getRemoteAddress()
                ]);

                //Send command
                /*
                $command = Encoder::commands(['lvcangetinfo']);

                $connection->write(hex2bin($command));

                logging('SEND_COMMAND', [
                    'IMEI' => $imei,
                    'DATA' => $command,
                    'IP' => $connection->getRemoteAddress()
                ]);
                */
            } catch (Exception $e) {
                logging('ERROR', [
                    'IMEI' => $imei,
                    'REQUEST' => $data,
                    'ERROR' => $e->getMessage(),
                    'IP' => $connection->getRemoteAddress()
                ]);
            }
        }
    });

    $connection->on('end', function () use ($connection) {
        logging('CONNECTION_END', [
            'IP' => $connection->getRemoteAddress()
        ]);
    });

    $connection->on('error', function (Exception $e) use ($connection) {
        logging('CONNECTION_ERROR', [
            'IP' => $connection->getRemoteAddress(),
            'ERROR' => $e->getMessage()
        ]);
    });

    $connection->on('close', function () use ($connection) {
        logging('CONNECTION_CLOSE', [
            'IP' => $connection->getRemoteAddress()
        ]);
    });
});

function logging($event, $data){
    if( is_array($data) || is_object($data) ){
        $data = json_encode($data);
    }

    $log = '[DATE: '.date('Y-m-d H:i:s').']['.$event.'][DATA: '. $data.']'.PHP_EOL;

    file_put_contents('log/teltonika-codec-8.log', $log, FILE_APPEND);
}

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
```
