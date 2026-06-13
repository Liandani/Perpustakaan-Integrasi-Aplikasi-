<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQController extends Controller
{
    public function send()
    {
        $client = new Client();

        // Call User Service
        $userResponse = $client->get('http://user-service:8000/users/1');
        $user = json_decode($userResponse->getBody(), true);

        // Call Book Service
        $bookResponse = $client->get('http://book-service:8000/books/101');
        $book = json_decode($bookResponse->getBody(), true);

        // RabbitMQ Connection
        $connection = new AMQPStreamConnection(
            'rabbitmq',
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

        $data = [
            'user' => $user,
            'book' => $book,
            'message' => 'Book Borrowed Successfully'
        ];

        $msg = new AMQPMessage(json_encode($data));

        $channel->basic_publish($msg, '', 'book_queue');

        $channel->close();
        $connection->close();

        return response()->json([
            'status' => 'Message Sent',
            'data' => $data
        ]);
    }
}
