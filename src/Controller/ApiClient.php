<?php
namespace App\Controller;

use App\Entity\Messages;
use App\Helpers\RabbitMQHelper;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Function responsible to gather the results from external API, filter the data by sending the data to
     * a RabbitMQ queue and subscribe to receive the filtered results. The function is triggered through the
     * route /messages/getNewMessage
     * @return Response
     */
    public function getMessage(): Response
    {
        $rabbitMQ = new RabbitMQHelper();
        try {
            // Get Sample results data
            $response = $this->client->request('GET', $_SERVER['RESULTS_URL']);
            $statusCode = $response->getStatusCode();
            if (200 !== $statusCode) {
                throw new Exception('Error Code: ' . $response->getStatusCode() . " Message: " . $response->getInfo());
            }

            // Create a Message object through deserialization of msg body
            $encoder = [new JsonEncoder()];
            $normalizer = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizer, $encoder);
            $message = new Messages();
            $serializer->deserialize($response->getContent(), 'App\Entity\Messages', 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $message]);

            // Publish/Subscribe to RabbitMQ
            $rabbitMQ->publishMessage($response->getContent(), $message->getRabbitMQMsg());
            $rabbitMQ->subscribeToQueue($message->getRabbitMQMsg());

            return new Response(
                '<html lang="en"><body>' . $message->getRabbitMQMsg() . '</body></html>'
            );
        } catch (Exception | ClientExceptionInterface | RedirectionExceptionInterface | TransportExceptionInterface | ServerExceptionInterface $e) {
            $rabbitMQ->closeConnection();
            return new Response(
                '<html lang="en"><body>' . $e->getMessage() . '</body></html>'
            );
        }
    }

}
