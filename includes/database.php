<?php
/**
 * ════════════════════════════════════════════════════════
 * CONNEXION BASE DE DONNÉES — Hôtel SEGURO
 * ════════════════════════════════════════════════════════
 */

class Database {
   private $host = 'sql303.infinityfree.com';
private $db_name = 'if0_42989258_seguro_hotel';
private $username = 'if0_42989258';
private $password = 'GZNJkc1Ua5';
    private $charset = 'utf8mb4';
    
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    /**
     * Exécute le script SQL de création de la base de données
     */
    public function createDatabase() {
        try {
            $conn = $this->getConnection();
            
            // Lire et exécuter le script SQL
            $sql_file = __DIR__ . '/../database/seguro_hotel.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                $conn->exec($sql);
                return true;
            }
        } catch(PDOException $exception) {
            echo "Erreur lors de la création de la base: " . $exception->getMessage();
        }
        return false;
    }
}
?>
