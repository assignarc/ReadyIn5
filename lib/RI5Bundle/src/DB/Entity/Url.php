<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Url
 *
 * @ORM\Table(name="url", uniqueConstraints={@ORM\UniqueConstraint(name="urlSlug_UNIQUE", columns={"urlSlug"}), @ORM\UniqueConstraint(name="entityId_UNIQUE", columns={"entityType", "entityId"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\UrlRepository")
 */
class Url
{
    /**
     * @var int
     *
     * @ORM\Column(name="urlId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $urlid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entityType", type="string", length=10, nullable=true)
     */
    private $entitytype;

    /**
     * @var int|null
     *
     * @ORM\Column(name="entityId", type="bigint", nullable=true)
     */
    private $entityid;

    /**
     * @var string
     *
     * @ORM\Column(name="urlSlug", type="string", length=100, nullable=false)
     */
    private $urlslug;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectUrl", type="string", length=1000, nullable=false)
     */
    private $redirecturl;

    /**
     * @var array|null
     *
     * @ORM\Column(name="notes", type="json", nullable=true)
     */
    private $notes;

    public function getUrlid(): ?int
    {
        return $this->urlid;
    }

    public function getEntitytype(): ?string
    {
        return $this->entitytype;
    }

    public function setEntitytype(?string $entitytype): self
    {
        $this->entitytype = $entitytype;

        return $this;
    }

    public function getEntityid(): ?string
    {
        return $this->entityid;
    }

    public function setEntityid(?string $entityid): self
    {
        $this->entityid = $entityid;

        return $this;
    }

    public function getUrlslug(): ?string
    {
        return $this->urlslug;
    }

    public function setUrlslug(string $urlslug): self
    {
        $this->urlslug = $urlslug;

        return $this;
    }

    public function getRedirecturl(): ?string
    {
        return $this->redirecturl;
    }

    public function setRedirecturl(string $redirecturl): self
    {
        $this->redirecturl = $redirecturl;

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


}
