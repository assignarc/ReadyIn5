<?php
namespace RI5\DB\Events;

use RI5\DB\Entity\Otp;

/**
 * The RI5.Otp.Added is dispatched each time an Otp is added in the system.
 */
class OtpAdded extends BaseEvent {
  
    public string $OtpId;
    public string $PhoneNumber;
    public string $MessageType;
    public string $Slug;
    public string $IncludeDeepLink;
    public string $OtpCode;
    public const NAME = 'RI5.Otp.Added';

    public function __construct(Otp $otp, ?string $messageType="sms", bool $includeDeepLink=true) {
       
        $this->OtpId= $otp->getOtpId();
        $this->Slug = $otp->getSlug();
        $this->PhoneNumber = $otp->getPhone();
        $this->OtpCode = $otp->getOtpcode();

        $this->MessageType = $messageType;
        $this->IncludeDeepLink = $includeDeepLink;
    }

    public function __toString(): string
    {   
        return $this::NAME . " - OTP Id:{$this->OtpId}";
    }

  
}
    
   