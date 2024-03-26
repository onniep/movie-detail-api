<?php

namespace  App\Models;

use PDO;
use PDOException;
/**
 * This PHP code defines a custom response class named CustomResponse, which provides methods for 
 * formatting and returning different types of API responses with specific HTTP status codes. This class
 * is responsible for creating JSON responses with consistent structures for success and error cases.
 */
class ResourceExists
{
    protected $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Function to check if a resource with a given ID exists
    public function resourceExists($id) {
        $sql = "SELECT COUNT(*) FROM movies WHERE id = :id";

        try {

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $rowCount = $stmt->fetchColumn();
            return $rowCount > 0; // returns true
        } catch (PDOException $e) {
            // Return false to indicate resource does not exist
            return false;
        }
    }

}

