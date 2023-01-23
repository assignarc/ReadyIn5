<?php
namespace RI5\Services\Traits;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;

trait LoggerAwareTrait{
    private LoggerInterface $logger;
   
    #[Required]
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function logException(Throwable $ex){
        $exceptionClass = get_class($ex);
        switch($exceptionClass){
            case "RI5\Exception\CustomerNotFoundException":
            case "RI5\Exception\InvalidRequestException":
            case "RI5\Exception\MessageException":
            case "RI5\Exception\OtpException":
            case "RI5\Exception\PlaceInvalidRequestException":
            case "RI5\Exception\PlaceNotFoundException":
            case "RI5\Exception\ReservationNotFoundException":
            case "RI5\Exception\ReservationStatusException":
            case "RI5\Exception\SecurityException":
                $this->logWarning($ex);
                break;
            default:
                $this->logError("{$exceptionClass}:{$ex->getMessage()} - {$ex->getTraceAsString()}");
                break;
        }
       
    }
     
    public function logDebug(string $message){
        $this->logMessage($message,LogLevel::DEBUG);
    }
    public function logInfo(string $message){
        $this->logMessage($message,LogLevel::INFO);
    }
    public function logCritical(string $message){
        $this->logMessage($message,LogLevel::CRITICAL);
    }
    public function logAlert(string $message){
        $this->logMessage($message,LogLevel::ALERT);
    }
    public function logError(string $message){
        $this->logMessage($message,LogLevel::ERROR);
    }
    public function logWarning(string $message){
        $this->logMessage($message,LogLevel::WARNING);
    }
    public function logNotice(string $message){
        $this->logMessage($message,LogLevel::NOTICE);
    }

    private function logMessage(string $message, ?string $level=null){   
        switch($level){
            case LogLevel::CRITICAL:
                $this->logger->critical($message);
                break;
            case LogLevel::DEBUG:
                $this->logger->debug($message);
                break;
            case LogLevel::ALERT:
                $this->logger->alert($message);
                break;
            case LogLevel::ERROR:
                $this->logger->error($message);
                break;
            case LogLevel::WARNING:
                $this->logger->warning($message);
                break;
            case LogLevel::NOTICE:
                $this->logger->notice($message);
                break;
            case LogLevel::INFO:
            default:
                $this->logger->info($message);
                break;
        }
    } 
}
