<?php
namespace RI5\Services\Subscribers;

use Exception;
use RI5\DB\Events\OtpAdded;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\LoggerAwareTrait;
use RI5\Services\Traits\MessengerTrait;
use RI5\Services\Traits\UtilityTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MessengerEventSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    use MessengerTrait;
    use ConfigAwareTrait;
    use UtilityTrait;
    use LoggerAwareTrait;

    public static function getSubscribedEvents()
    {
        return [
            OtpAdded::NAME => 'onOtpAdded'
        ];
    }
    public function onOtpAdded(OtpAdded $event){
      
        try{
            $this->logEvent($event,"Received");

            $template1 ="Your verification code is {{0}}. For your security, do not share this code.";
            $template2 ="Your verification code is {{0}} or click here {{1}}. For your security, do not share this code.";
            
            $send = boolval($this->getConfigItem("RI5.OTP.send"));
        
            /*TWILLIO*/
            if($event->IncludeDeepLink){
                $message = UtilityTrait::Substitute(
                                $template2,
                                [$event->OtpCode, ($this->router->generate("WL_Home",[], UrlGeneratorInterface::ABSOLUTE_URL) . "q." .$event->Slug) ]);
            }
            else
                $message = UtilityTrait::Substitute(
                                $template1,
                                [$event->OtpCode]);
            if($send)
                $this->sendMessage($event->PhoneNumber, $message ,$event->MessageType);
            
            $this->logEvent($event,"PROCESS");
        }
        catch(Exception $ex){
            $this->logException($ex);
        }
    }
   
}