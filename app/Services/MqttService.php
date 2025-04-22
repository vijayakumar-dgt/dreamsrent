<?php
namespace App\Services;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
class MqttService
{
    protected $client;
    protected $connected = false;

    public function __construct()
    {
        $server   = env('MQTT_HOST', 'broker.emqx.io');
        $port     = env('MQTT_PORT', 1883);
        $clientId = 'dreamsrent_' . uniqid();
        $connectionSettings = (new ConnectionSettings)
                            ->setKeepAliveInterval(60)
                            ->setLastWillTopic(null)
                            ->setUsername(null)
                            ->setPassword(null);
        $this->client = new MqttClient($server, $port, $clientId);
        $this->connectionSettings = $connectionSettings;
    }

    public function connect()
    {
        if (!$this->connected) {
            $this->client->connect(null, true);
            $this->connected = true;
        }
    }

    public function publish($topic, $message)
    {
        $this->connect();
        $this->client->publish($topic, $message);
    }

    public function disconnect()
    {
        if ($this->connected) {
            $this->client->disconnect();
            $this->connected = false;
        }
    }
}
