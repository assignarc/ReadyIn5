<?php
namespace RI5\Services\Traits;

use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\Lock\LockFactory;

trait CacheAwareTrait{
    use LoggerAwareTrait;
    use LockerTrait;

    private TagAwareCacheInterface $cache;
   
    
    #[Required]
    public function setCache(TagAwareCacheInterface $cache){
        $this->cache = $cache;
    }

     /*CACHE Functions */
    protected function cacheItemUnlocked(string $key, mixed $defaultValue, bool $forceUpdate=false, ?array $tags=null,int $defaultExpiry=43200) : mixed {
        
        if($forceUpdate)
            $this->cache->invalidateTags($tags);

        return $this->cache->get($key,  function (ItemInterface $item) use  ($defaultValue, $defaultExpiry, $tags): mixed {
            if($tags)
                $item->tag($tags);
            if($defaultValue)
                $item->expiresAfter($defaultExpiry);
            else 
                $item->expiresAfter(0);

            return $defaultValue;
        });
    }

     /*CACHE Functions */
     protected function cacheItem(string $key, mixed $defaultValue,bool $forceUpdate=false, ?array $tags=null,int $defaultExpiry=43200) : mixed {
        
        $lock = $this->locker->createLock('cacheLock', ttl:2);
        try{
            $lock->acquire(true);
            if($this->cache){
                if($forceUpdate)
                    $this->cache->delete($key);
            
                return $this->cache->get($key,  function (ItemInterface $item) use  ($defaultValue, $defaultExpiry, $tags): mixed {
                    if($tags)
                        $item->tag($tags);
                    if($defaultValue)
                        $item->expiresAfter($defaultExpiry);
                    else 
                        $item->expiresAfter(0);
                    return $defaultValue;
                });
            }
        }
        catch(Exception $ex){
            $this->logException($ex);
            $lock->release();
        }
        finally{
            $lock->release();
        }
      
        $this->log("Controller Cache : Returning default");
        return $defaultValue;
    }
}
