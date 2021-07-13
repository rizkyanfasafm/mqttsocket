<?php

namespace App\Console\Commands;

use App\Events\NewMessage;
use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;

class SubscribeToBroker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sub:broker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command subscribe to broker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server   = env('MQTT_HOST');
        $port     = env('MQTT_PORT');
        $username = env('MQTT_AUTH_USERNAME');
        $password = env('MQTT_AUTH_PASSWORD');
        // $clientId = 'test-publisher';

        $mqtt = new MQTTClient($server, $port);
        $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)->setUsername('anfasa')->setPassword('root');
        $mqtt->connect($connectionSettings, true);
        $mqtt->subscribe('test', function ($topic, $message) {
            // echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);7
            $result = json_decode($message);
            event(new NewMessage($result->id_controller));
        }, 2);
        $mqtt->loop(true);
        $mqtt->disconnect();
    }
}
