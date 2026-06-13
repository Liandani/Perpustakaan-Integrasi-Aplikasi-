<?php

namespace App\Http\Controllers;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConsumerController extends Controller
{
    public function consume()
    {
        $rabbitHost = env('RABBITMQ_HOST', 'rabbitmq');
        $connection = new AMQPStreamConnection(
            $rabbitHost,
            5672,
            'guest',
            'guest'
        );

        $channel = $connection->channel();

        $channel->queue_declare(
            'book_queue',
            false,
            true,
            false,
            false
        );

        $messageData = null;

        $callback = function ($msg) use (&$messageData) {
            $messageData = json_decode($msg->body, true);
        };

        $channel->basic_consume(
            'book_queue',
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        if ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return response()->json([
            'status' => 'Message Consumed',
            'data' => $messageData
        ]);
    }
}
