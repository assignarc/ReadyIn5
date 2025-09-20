<?php

namespace RI5\Services;

use RI5\Services\BaseService;
use RI5\DB\Entity\Url;
use RI5\DB\Repository\UrlRepository;
use RI5\Exception\UrlException;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\EntityAwareTrait;
use RI5\Services\Traits\EventDispatcherTrait;

class UrlService extends BaseService 
{
    use ConfigAwareTrait;
    use EntityAwareTrait;
    use EventDispatcherTrait;

    
    public function __construct(UrlRepository $urlRepository) 
    {
        $this->objectRepository = $urlRepository;
    }
    /**
     * Summary of createRandomKey
     * @param int $length
     * @return string
     */
    public function createRandomKey(int $length) : string{
        $bytes = random_bytes($length/2);
        return  bin2hex($bytes);
    }
    /**
     * Summary of findUrlByUrlSlug
     * @param string $slug
     */
    public function findUrlByUrlSlug(string $slug) : ?Url {
        $otp=  $this->objectRepository->findOneByUrlslug($slug);
        return $otp;
    }
    /**
     * Summary of findUrlByEntity
     * @param string $entityType
     * @param string $entityId
     * @return Url|null
     */
    public function findUrlByEntity(string $entityType, string $entityId) : ?Url {
       return $this->objectRepository->findOneByEntity($entityType,$entityId);
    }
    /**
     * Summary of createUpdateUrl
     * @param string $entityType
     * @param string $entityId
     * @param string $redirectUrl
     * @param mixed $notes
     * @param bool $forceCreate
     * @throws \RI5\Exception\UrlException
     * @return string|null
     */
    public function createUpdateUrl(string $entityType,string $entityId, string $redirectUrl, ?array $notes, bool $forceCreate = false) : string {
        
        if($entityType==null || $entityType =="")
            throw new UrlException("Invalid URL.");
        
        $urlDB = $this->findUrlByEntity($entityType,$entityId);

        if($urlDB)
            return $urlDB->getUrlslug();
        else{
            $url = new Url();
            $urlSlug = $this->createRandomKey(20);
            $url->setEntityid($entityId);
            $url->setEntitytype($entityType);
            $url->setNotes($notes);
            $url->setUrlslug($urlSlug);
            $url->setRedirecturl($redirectUrl);
            $this->objectRepository->save($url,true);
            //Dispatch Event
            // $this->dispatchEvent(new UrlAdded($otp,$type,$includeDeepLink),OtpAdded::NAME);

            return $urlSlug;
        }
     }
}