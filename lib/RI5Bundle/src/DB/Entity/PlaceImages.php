<?php

namespace RI5\DB\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * PlaceImages
 *
 * @ORM\Table(name="place_images", uniqueConstraints={@ORM\UniqueConstraint(name="IX_PLACE", columns={"placeid", "image_type"})}, indexes={@ORM\Index(name="IDX_1181419B145ED7B1", columns={"placeid"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceImageRepository")
 */
class PlaceImages extends BaseEntity implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="imageid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $imageid;

    /**
     * @var string
     *
     * @ORM\Column(name="image_type", type="string", length=5, nullable=false)
     */
    private $imageType;

    /**
     * @var array|null
     *
     * @ORM\Column(name="image_data", type="json", nullable=true)
     */
    private $imageData;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="blob", length=0, nullable=true)
     */
    private $image;
    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;
    
    /**
     * @var Place
     * Many PlaceQueues have One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="placeImages")
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;

    public function getImageid(): ?int
    {
        return $this->imageid;
    }

    public function getImageType(): ?string
    {
        return $this->imageType;
    }

    public function setImageType(string $imageType): self
    {
        $this->imageType = $imageType;

        return $this;
    }

    public function getImageData(): array
    {
        return $this->imageData;
    }

    public function setImageData(?array $imageData): self
    {
        $this->imageData = $imageData;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPlaceid(): ?int
    {
        return $this->placeid;
    }

    public function setPlaceid(int $placeid): self
    {
        $this->placeid = $placeid;

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
   
    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

}
