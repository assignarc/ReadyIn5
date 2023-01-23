<?php

namespace RI5\Services;

use Psr\Log\LogLevel;
use RI5\Services\IMessengerService;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\LoggerAwareTrait;
use Twilio\Rest;


class TwilioMessageService extends BaseService implements IMessengerService
{
    use ConfigAwareTrait;
    use LoggerAwareTrait;
    public function sendMessage(string $phoneNumber,string $message = null, string $type="sms")  { 

        $send = boolval($this->getConfigItem("RI5.MESSAGE.send"));
       
        if(!$send) 
            return;
       
        $sid = $this->getConfigItem("RI5.TWILIO.sid");// "AC315aef638ff3320ac5489ce3773af68e";
        $token = $this->getConfigItem("RI5.TWILIO.token");// "09ab57748104595a87b154756eee3ed5";
        $fromNumber =$this->getConfigItem("RI5.TWILIO.fromnumber");// "+19124000081";

        $client = new \Twilio\Rest\Client($sid, $token);
        switch($type){
            case  "whatsapp":
                $returnMessage = $client->messages
                  ->create("whatsapp:" . $phoneNumber , // to
                           [
                               "from" => "whatsapp:" . $fromNumber,
                               "body" => $message
                           ]
                  );
                  $this->logDebug("Messenger : " .$returnMessage->sid);
                break;
            case  "sms":
            default:
                 // Use the Client to make requests to the Twilio REST API
                 $returnMessage= $client->messages->create(
                    // The number you'd like to send the message to
                    $phoneNumber,
                    [
                        // A Twilio phone number you purchased at https://console.twilio.com
                        'from' => $fromNumber,
                        // The body of the text message you'd like to send
                        'body' => "ReadyIn5 - [{$message}]"
                    ]
                );
        }
        
    }
    public function printName() : ?string{
        return get_class($this);
    }
}