<?php


namespace App\Helpers;

use App\Entity\Messages;
use ErrorException;
use Exception;
use mysqli;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RabbitMQHelper
{

    /**
     * @var AMQPChannel
     */
    private $channel;
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection($_SERVER['RABBITMQ_HOSTNAME'],$_SERVER['RABBITMQ_PORT'],$_SERVER['RABBITMQ_USERNAME'],$_SERVER['RABBITMQ_PASSWORD']);
        $this->channel = $this->connection->channel();
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * @param AMQPStreamConnection $connection
     */
    public function setConnection(AMQPStreamConnection $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Function that given a string message and a string routing key it will create an
     * instance of AMQPMessage and publish the message to the RabbitMQ configured in .env
     *
     * @param string $message
     * @param string $routing_key
     */
    public function publishMessage(string $message, string $routing_key) {
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg,$_SERVER['RABBITMQ_EXCHANGE'],$routing_key);
        echo '<h5>' . $routing_key . ' was published!</h5>';
    }

    /**
     * Function that given a string routing key and message it will subscribe to a defined queue and
     * publish the message. The connection would wait to receive a reply for RABBITMQ_TIMEOUT seconds
     * which is configured in .env
     *
     * @param string $routing_key
     * @param string $context
     */
    public function subscribeToQueueNPublish(string $routing_key, string $context) {
        $this->channel->queue_bind($_SERVER['RABBITMQ_QUEUE_NAME'], $_SERVER['RABBITMQ_EXCHANGE'], $routing_key);

        // Closure function to be used as callback function for subscription in the queue
        $consumerCallback = function($msg) {

            // Create a Message object through deserialization of msg body
            $encoder = [new JsonEncoder()];
            $normalizer = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizer, $encoder);
            $message = new Messages();
            $serializer->deserialize($msg->body, 'App\Entity\Messages', 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $message]);

            $conn = new mysqli($_SERVER['MYSQL_HOSTNAME'], $_SERVER['MYSQL_USERNAME'], $_SERVER['MYSQL_PASSWORD'], $_SERVER['MYSQL_DB_NAME']);

            echo 'Message received = ',$message->getRabbitMQMsg(),"\n";

            // Insert Values to MySQL
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } else {
                $query = "INSERT INTO messages
               VALUES ('NULL', '".$message->getGatewayEui()."', '".$message->getProfileId()."', '".$message->getEndpointId()."', '".$message->getClusterId()."', '".$message->getAttributeId()."', '".$message->getValue()."', '".$message->getTimestamp()."')";
            }
            if ($conn->query($query) === TRUE) {
                echo '<h5>New record created successfully</h5>';
            } else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }

            $conn->close();
            $this->closeConnection();
        };

        $this->channel->basic_consume($_SERVER['RABBITMQ_QUEUE_NAME'],'',false,false,false,false, $consumerCallback);
        try {
            // Publish the message and wait
            $this->publishMessage($context, $routing_key);
            if (count($this->channel->callbacks)) {
                $this->channel->wait(null, false, $_SERVER['RABBITMQ_TIMEOUT']);
            }
        } catch (ErrorException $e) {
            $this->closeConnection();
            new Response(
                '<html lang="en"><body>' . $e . '</body></html>'
            );
        }
    }

    /**
     * Function to close the existing AMQPStreamConnection
     */
    public function closeConnection() {
        $this->channel->close();
        try {
            $this->connection->close();
        } catch (Exception $e) {
            new Response(
                '<html lang="en"><body>' . $e . '</body></html>'
            );
        }
    }

}