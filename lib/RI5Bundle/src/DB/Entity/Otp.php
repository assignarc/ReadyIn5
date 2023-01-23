<?php

namespace RI5\DB\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Otp
 *
 * @ORM\Table(name="otp", indexes={@ORM\Index(name="IX_Phone", columns={"phone"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\OtpRepository")
 */
class Otp extends BaseEntity implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="otpid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $otpid;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=100, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="otpcode", type="string", length=6, nullable=false)
     */
    private $otpcode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiryDt", type="datetime", nullable=false)
     */
    private $expirydt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=5, nullable=true)
     */
    private $status;

    /**
     * @var array|null
     *
     * @ORM\Column(name="notes", type="json", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=6, nullable=false)
     */
    private $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOtpid(): ?int
    {
        return $this->otpid;
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

    public function getOtpcode(): ?string
    {
        return $this->otpcode;
    }

    public function setOtpcode(string $otpcode): self
    {
        $this->otpcode = $otpcode;

        return $this;
    }

    public function getExpirydt(): ?\DateTimeInterface
    {
        return $this->expirydt;
    }

    public function setExpritydt(\DateTimeInterface $expirydt): self
    {
        $this->expirydt = $expirydt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): array
    {
        return $this->notes;
    }

    public function setNotes(?array $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function jsonSerialize() :mixed
    {
        return get_object_vars($this);
    }
}
