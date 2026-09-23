<?php
/**
 * ════════════════════════════════════════════════════════
 * CLASS USER — Gestion des clients et admins
 * ════════════════════════════════════════════════════════
 */

require_once 'database.php';

class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $code_client;
    public $telephone;
    public $pays;
    public $role;
    public $created_at;
    public $last_login;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crée automatiquement un compte client lors de la première réservation
     */
    public function createAutoAccount($nom, $prenom, $email, $telephone = null, $pays = null) {
        try {
            // Vérifier si l'email existe déjà
            if ($this->emailExists($email)) {
                $existingUser = $this->getByEmail($email);
                if ($existingUser) {
                    return $existingUser;
                }
            }
            
            // Générer un code client unique
            $this->code_client = $this->generateCodeClient();
            
            $query = "INSERT INTO " . $this->table_name . "
                    (nom, prenom, email, code_client, telephone, pays, role)
                    VALUES (?, ?, ?, ?, ?, ?, 'client')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $nom);
            $stmt->bindParam(2, $prenom);
            $stmt->bindParam(3, $email);
            $stmt->bindParam(4, $this->code_client);
            $stmt->bindParam(5, $telephone);
            $stmt->bindParam(6, $pays);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                $this->nom = $nom;
                $this->prenom = $prenom;
                $this->email = $email;
                $this->role = 'client';
                
                // Logger la création automatique (sans bloquer si ça échoue)
                try {
                    $this->logAction('COMPTE_AUTO_CREE', 'users', $this->id);
                } catch (Exception $logError) {
                    // Continuer même si le log échoue
                    error_log("Erreur log création compte: " . $logError->getMessage());
                }
                
                return $this;
            } else {
                error_log("Erreur insertion user: " . implode(", ", $stmt->errorInfo()));
            }
        } catch(PDOException $exception) {
            error_log("Erreur création compte: " . $exception->getMessage());
            echo "Erreur lors de la création du compte: " . $exception->getMessage();
        }
        return false;
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Récupère un utilisateur par email
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->code_client = $row['code_client'];
            $this->telephone = $row['telephone'];
            $this->pays = $row['pays'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->last_login = $row['last_login'];
            
            return $this;
        }
        return false;
    }
    
    /**
     * Récupère un utilisateur par ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->code_client = $row['code_client'];
            $this->telephone = $row['telephone'];
            $this->pays = $row['pays'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->last_login = $row['last_login'];
            
            return $this;
        }
        return false;
    }
    
    /**
     * Authentification par email et code client
     */
    public function login($email, $code_client) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE email = ? AND code_client = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $code_client);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->code_client = $row['code_client'];
            $this->telephone = $row['telephone'];
            $this->pays = $row['pays'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            
            // Mettre à jour last_login
            $this->updateLastLogin();
            
            // Logger la connexion
            $this->logAction('CONNEXION', 'users', $this->id);
            
            return $this;
        }
        return false;
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public function updateLastLogin() {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
    
    /**
     * Génère un code client unique
     */
    private function generateCodeClient() {
        do {
            $code = 'CLI-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $query = "SELECT id FROM " . $this->table_name . " WHERE code_client = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $code);
            $stmt->execute();
        } while ($stmt->rowCount() > 0);
        
        return $code;
    }
    
    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin() {
        return in_array($this->role, ['admin', 'super_admin']);
    }
    
    /**
     * Enregistre une action dans les logs
     */
    private function logAction($action, $table_cible, $cible_id, $avant = null, $apres = null) {
        try {
            $query = "INSERT INTO logs_actions (user_id, action, table_cible, cible_id, avant, apres, ip_address)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            // Permettre user_id null (important pour les actions système)
            $stmt->bindParam(1, $this->id, PDO::PARAM_NULL);
            $stmt->bindParam(2, $action);
            $stmt->bindParam(3, $table_cible);
            $stmt->bindParam(4, $cible_id);
            $stmt->bindParam(5, $avant);
            $stmt->bindParam(6, $apres);
            $stmt->bindParam(7, $ip);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur logAction: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les réservations d'un client
     */
    public function getReservations() {
        $query = "SELECT r.*, c.nom as chambre_nom, c.type as chambre_type
                 FROM reservations r
                 JOIN chambres c ON r.chambre_id = c.id
                 WHERE r.user_id = ?
                 ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère tous les clients (pour l'admin)
     */
    public function getAllClients() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Recherche des clients
     */
    public function searchClients($search) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR code_client LIKE ?
                 ORDER BY created_at DESC";
        
        $searchTerm = '%' . $search . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $searchTerm);
        $stmt->bindParam(2, $searchTerm);
        $stmt->bindParam(3, $searchTerm);
        $stmt->bindParam(4, $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
}
?>
