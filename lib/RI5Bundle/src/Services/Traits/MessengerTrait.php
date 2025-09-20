<?php
namespace RI5\Services\Traits;

use RI5\Services\IMessengerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait MessengerTrait{
    use CacheAwareTrait;
    use LoggerAwareTrait;

    private IMessengerService $messenger;
    private UrlGeneratorInterface $router;
    
 
    #[Required]
    /**
     * Summary of setMessenger
     * @param \RI5\Services\IMessengerService $messenger
     * @return void
     */
    public function setMessenger(IMessengerService $messenger)
    {
        $this->messenger = $messenger;
    }
    #[Required]
    /**
     * Summary of setUrlGenerator
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router
     * @return void
     */
    public function setUrlGenerator(UrlGeneratorInterface $router){
        $this->router = $router;
    }

    function sendMessage(string $phoneNumber,string $message = "", string $type="sms"){
        if(boolval($this->getConfigItem("RI5.MESSAGE.send")))
            $this->messenger->sendMessage($phoneNumber,$message,$type);
        else
            $this->logWarning("Message : To=>{$type}:{$phoneNumber};Text=>{$message}");
    }
}
