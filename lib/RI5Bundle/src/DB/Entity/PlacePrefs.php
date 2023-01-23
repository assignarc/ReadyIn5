<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * PlacePrefs
 *
 * @ORM\Table(name="place_prefs", uniqueConstraints={@ORM\UniqueConstraint(name="prefid_UNIQUE", columns={"preferenceid"})}, indexes={@ORM\Index(name="IX_Pref_Place", columns={"placeid"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlacePrefsRepository")
 */
class PlacePrefs
{
    /**
     * @var int
     *
     * @ORM\Column(name="preferenceid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $preferenceid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=2000, nullable=true)
     */
    private $value;

   

     /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;

    /**
     * @var \Place
     * Many PlaceQueues have One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="placePrefs")
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;


    public function getPreferenceid(): ?int
    {
        return $this->preferenceid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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

}
