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
        $userApiUrl = env('USER_API_URL', 'http://user-api:8000');
        $userResponse = $client->get($userApiUrl . '/users/1');
        $user = json_decode($userResponse->getBody(), true);

        // Call Book Service
        $bookApiUrl = env('BOOK_API_URL', 'http://book-api:8000');
        $bookResponse = $client->get($bookApiUrl . '/books/101');
        $book = json_decode($bookResponse->getBody(), true);

        // RabbitMQ Connection
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
