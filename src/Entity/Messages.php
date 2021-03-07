<?php
namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Repository\Repository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=Repository::class)
 */
class Messages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Construct message for RabbitMQ
     * Example <gateway eui>.<profile>.<endpoint>.<cluster>.<attribute>
     *
     * @return  string
     */
    public function getRabbitMQMsg(): string
    {
        return $this->getGatewayEui() . "." . $this->getProfileId() . "." . $this->getEndpointId() . "." . $this->getClusterId() . "." . $this->getAttributeId();
    }

    /**
     * @SerializedName("gatewayEui")
     * @ORM\Column(type="decimal", precision=19, scale=0)
     * @var string
     */
    private $gatewayEui;
    /**
     * @SerializedName("profileId")
     * @ORM\Column(type="decimal", precision=19, scale=0)
     * @var string
     */
    private $profileId;
    /**
     * @SerializedName("endpointId")
     * @ORM\Column(type="decimal", precision=19, scale=0)
     * @var string
     */
    private $endpointId;
    /**
     *@SerializedName("clusterId")
     * @ORM\Column(type="decimal", precision=19, scale=0)
     * @var string
     */
    private $clusterId;
    /**
     * @SerializedName("attributeId")
     * @ORM\Column(type="decimal", precision=19, scale=0)
     * @var string
     */
    private $attributeId;
    /**
     * @SerializedName("value")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $value;
    /**
     * @SerializedName("timestamp")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $timestamp;

    /**
     * Get the value of gatewayEui
     *
     * @return  string
     */
    public function getGatewayEui(): string
    {
        $decGatewayEui = hexdec($this->gatewayEui);
        return sprintf("%.0f",$decGatewayEui);
    }

    /**
     * Set the value of gatewayEui
     *
     * @param  string  $gatewayEui
     *
     * @return  self
     */
    public function setGatewayEui(string $gatewayEui): Messages
    {
        $this->gatewayEui = $gatewayEui;

        return $this;
    }

    /**
     * Get the value of profileId
     *
     * @return  string
     */
    public function getProfileId(): string
    {
        return hexdec($this->profileId);
    }

    /**
     * Set the value of profileId
     *
     * @param  string  $profileId
     *
     * @return  self
     */
    public function setProfileId(string $profileId): Messages
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get the value of endpointId
     *
     * @return  string
     */
    public function getEndpointId(): string
    {
        return hexdec($this->endpointId);
    }

    /**
     * Set the value of endpointId
     *
     * @param  string  $endpointId
     *
     * @return  self
     */
    public function setEndpointId(string $endpointId): Messages
    {
        $this->endpointId = $endpointId;

        return $this;
    }

    /**
     * Get *@SerializedName("clusterId")
     *
     * @return  string
     */
    public function getClusterId(): string
    {
        return hexdec($this->clusterId);
    }

    /**
     * Set *@SerializedName("clusterId")
     *
     * @param  string  $clusterId  *@SerializedName("clusterId")
     *
     * @return  self
     */
    public function setClusterId(string $clusterId): Messages
    {
        $this->clusterId = $clusterId;

        return $this;
    }

    /**
     * Get the value of attributeId
     *
     * @return  string
     */
    public function getAttributeId(): string
    {
        return hexdec($this->attributeId);
    }

    /**
     * Set the value of attributeId
     *
     * @param  string  $attributeId
     *
     * @return  self
     */
    public function setAttributeId(string $attributeId): Messages
    {
        $this->attributeId = $attributeId;

        return $this;
    }

    /**
     * Get the value of value
     *
     * @return  float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @param  float  $value
     *
     * @return  self
     */
    public function setValue(float $value): Messages
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of timestamp
     *
     * @return  int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Set the value of timestamp
     *
     * @param  int  $timestamp
     *
     * @return  self
     */
    public function setTimestamp(int $timestamp): Messages
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
