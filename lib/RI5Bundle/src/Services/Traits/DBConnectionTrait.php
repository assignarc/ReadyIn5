<?php
namespace RI5\Services\Traits;

use Exception;
use RI5\Services\ConnectionService;
use Symfony\Contracts\Service\Attribute\Required;

trait DBConnectionTrait{
    use LoggerAwareTrait;
    
    private $db;
    #[Required]
    public function setConnectionService(ConnectionService $db){
        $this->db = $db;
    }

    protected function getConnection() : ?\PDO {
        try {
            if($this->dbConnection){
                return $this->db->getConnection();
            }
            else{
                $this->logError("DB Connection Service not set");
                return null;
            }
        } catch (Exception $e) {
            $this->logError("DB Connection failed: " . $e->getMessage());
            return null;
        }
    }
   
}
