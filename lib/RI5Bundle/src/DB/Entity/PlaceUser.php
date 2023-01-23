<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * PlaceUser
 *
 * @ORM\Table(name="place_user", uniqueConstraints={@ORM\UniqueConstraint(name="IX_Place_UserName", columns={"username"}), @ORM\UniqueConstraint(name="placeuserid_UNIQUE", columns={"placeuserid"})}, indexes={@ORM\Index(name="IX_Place_User", columns={"placeid"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceUserRepository")
 */
class PlaceUser extends BaseEntity implements JsonSerializable 
{
    /**
     * @var int
     *
     * @ORM\Column(name="placeuserid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $placeuserid;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=false)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessAdmin", type="string", length=100, nullable=true)
     */
    private $accessadmin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessReservation", type="string", length=100, nullable=true)
     */
    private $accessreservation;

   
    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;
    /**
     * Many PlaceUsers has One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="placeUsers")
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;

    public function getPlaceuserid(): ?int
    {
        return $this->placeuserid;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAccessadmin(): ?string
    {
        return $this->accessadmin;
    }

    public function setAccessadmin(?string $accessadmin): self
    {
        $this->accessadmin = $accessadmin;

        return $this;
    }

    public function getAccessreservation(): ?string
    {
        return $this->accessreservation;
    }

    public function setAccessreservation(?string $accessreservation): self
    {
        $this->accessreservation = $accessreservation;

        return $this;
    }

    public function getPlace(): Place
    {   
        return $this->place;
    }

    public function setPlace(Place $place): self
    {   
        $this->place = $place;
        return $this;
    }
   
   
    public function getPlaceid(): int
    {
        return $this->placeid;
    }

    public function setPlaceid(int $placeid): self
    {
        $this->placeid = $placeid;

        return $this;
    }

    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }
}
