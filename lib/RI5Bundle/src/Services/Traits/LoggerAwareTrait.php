<?php
namespace RI5\Services\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;

trait LoggerAwareTrait{
    private LoggerInterface $logger;
   
    #[Required]
    /**
     * Summary of setLogger
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Summary of logException
     * @param \Throwable $ex
     * @return void
     */
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
    /**
     * Summary of logDebug
     * @param string $message
     * @return void
     */
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
    /**
     * Summary of logMessageArray
     * @param array $message
     * @param mixed $level
     * @return void
     */
    public function logMessageArray(array $message, ?string $level=null){   
        switch($level){
            case LogLevel::CRITICAL:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::DEBUG:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::ALERT:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::ERROR:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::WARNING:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::NOTICE:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
            case LogLevel::INFO:
            default:
                foreach($message as $msg)
                    $this->logger->critical($msg);
                break;
        }
    } 
    /**
     * Summary of logMessage
     * @param string $message
     * @param mixed $level
     * @return void
     */
    private function logMessage(string $message, $level=LogLevel::DEBUG){   
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
