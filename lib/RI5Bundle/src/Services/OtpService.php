<?php

namespace RI5\Services;

use RI5\DB\Entity\Otp;
use RI5\Exception\OtpException;
use RI5\Services\BaseService;
use RI5\DB\Repository\OtpRepository;
use DateInterval;
use DateTime;
use RI5\DB\Events\OtpAdded;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\EntityAwareTrait;
use RI5\Services\Traits\EventDispatcherTrait;

class OtpService extends BaseService 
{
    use ConfigAwareTrait;
    use EntityAwareTrait;
    use EventDispatcherTrait;

    
    public function __construct(
        OtpRepository $otpRepository) 
    {
        $this->objectRepository = $otpRepository;
    }
   
    public function createRandomKey(int $length) : string{
        $bytes = random_bytes($length/2);
        return  bin2hex($bytes);
    }
    public function findOtp(string $phoneNumber) : ?Otp {
        $otp=  $this->objectRepository->findOneByPhone($phoneNumber);
        return $otp;
    }
    public function findOtpBySlug(string $slug) : ?Otp {
        $otp=  $this->objectRepository->findOneBySlug($slug);
        return $otp;
    }

    public function createOtp(string $phoneNumber,array $notes = null, string $type="sms", bool $includeDeepLink=true) : string {
        
        if($phoneNumber==null || $phoneNumber =="")
            throw new OtpException("Invalid phone number.");
        
        $otpDB = $this->findOtp($phoneNumber);

        if(!$otpDB){
            $send = boolval($this->getConfigItem("RI5.OTP.send"));

            $otpCode = $send ? rand(10000,99999): $this->getConfigItem("RI5.OTP.defaultcode");//;
            $otp = new Otp();
            $key = $this->createRandomKey(6);
            $otp->setPhone($phoneNumber);
            $otp->setOtpcode($otpCode);
            $otp->setNotes($notes);
            $otp->setStatus("SET");
            $otp->setSlug($key);
            $interval = new DateInterval($this->getConfigItem("RI5.OTP.expiryInterval"));
            $otp->setExpritydt((new DateTime())->add($interval));
            $this->objectRepository->save($otp,true);
            //Dispatch Event
            $this->dispatchEvent(new OtpAdded($otp,$type,$includeDeepLink),OtpAdded::NAME);

            return $key;
        }
        else{
            $this->objectRepository->remove($otpDB,true);
            throw new OtpException("One Time Password (OTP) already existed for this phone, request a new one.");
        }
     }

     public function validateOtp(string $phoneNumber, string $otpCode) {
        $otpDB = $this->findOtp($phoneNumber);
        if(!$otpDB){
            throw new OtpException("Otp does not exist for this phone");
        }
        else{
            if($otpDB->getExpirydt() < new DateTime()){
                $this->entityManager->remove($otpDB);
                $this->entityManager->flush();
                throw new OtpException("Expired One Time Password (OTP) Code, try creating a new one");
            }
            if($otpCode != $otpDB->getOtpcode()){
                $this->entityManager->remove($otpDB);
                $this->entityManager->flush();
                throw new OtpException("Invalid One Time Password (OTP) Code.");
            }
            $this->entityManager->remove($otpDB);
            $this->entityManager->flush();
        }
     }

     public function validateOtpSlug(string $slug) : Otp {
        $otpDB = $this->findOtpBySlug($slug);
        if(!$otpDB){
            throw new OtpException("One Time Password (OTP) does not exist for this phone number.");
        }
        else{
            if($otpDB->getExpirydt() < new DateTime()){
                $this->entityManager->remove($otpDB);
                $this->entityManager->flush();
                throw new OtpException("Expired One Time Password (OTP), try creating a new one.");
            }
           
            $this->entityManager->remove($otpDB);
            $this->entityManager->flush();

            return $otpDB;
        }
     }
}