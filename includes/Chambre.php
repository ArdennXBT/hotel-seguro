<?php
/**
 * ════════════════════════════════════════════════════════
 * CLASS CHAMBRE — Gestion des chambres et disponibilités
 * ════════════════════════════════════════════════════════
 */

require_once 'database.php';

class Chambre {
    private $conn;
    private $table_name = "chambres";
    
    public $id;
    public $nom;
    public $type;
    public $superficie_m2;
    public $prix_nuit;
    public $capacite_max;
    public $capacite_enfants;
    public $description;
    public $amenities;
    public $image_principale;
    public $disponible;
    public $etage;
    public $numero;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère toutes les chambres disponibles
     */
    public function getAllAvailable() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE disponible = 1 
                 ORDER BY prix_nuit ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une chambre par son ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->type = $row['type'];
            $this->superficie_m2 = $row['superficie_m2'];
            $this->prix_nuit = $row['prix_nuit'];
            $this->capacite_max = $row['capacite_max'];
            $this->capacite_enfants = $row['capacite_enfants'];
            $this->description = $row['description'];
            $this->amenities = $row['amenities'];
            $this->image_principale = $row['image_principale'];
            $this->disponible = $row['disponible'];
            $this->etage = $row['etage'];
            $this->numero = $row['numero'];
            
            return $this;
        }
        return false;
    }
    
    /**
     * Vérifie la disponibilité d'une chambre pour une période donnée
     */
    public function isAvailable($date_arrivee, $date_depart, $exclude_reservation_id = null) {
        $query = "SELECT COUNT(*) as nb_indispo
                 FROM indisponibilites i
                 WHERE i.chambre_id = ?
                 AND (
                     (i.date_debut <= ? AND i.date_fin > ?) OR
                     (i.date_debut < ? AND i.date_fin >= ?) OR
                     (i.date_debut >= ? AND i.date_fin <= ?)
                 )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $date_arrivee);
        $stmt->bindParam(3, $date_arrivee);
        $stmt->bindParam(4, $date_depart);
        $stmt->bindParam(5, $date_depart);
        $stmt->bindParam(6, $date_arrivee);
        $stmt->bindParam(7, $date_depart);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['nb_indispo'] == 0;
    }
    
    /**
     * Récupère les chambres disponibles pour une période donnée
     */
    public function getAvailableForDates($date_arrivee, $date_depart, $type = null) {
        $query = "SELECT c.* FROM " . $this->table_name . " c
                 WHERE c.disponible = 1
                 AND NOT EXISTS (
                     SELECT 1 FROM indisponibilites i
                     WHERE i.chambre_id = c.id
                     AND (
                         (i.date_debut <= ? AND i.date_fin > ?) OR
                         (i.date_debut < ? AND i.date_fin >= ?) OR
                         (i.date_debut >= ? AND i.date_fin <= ?)
                     )
                 )";
        
        $params = [$date_arrivee, $date_arrivee, $date_depart, $date_depart, $date_arrivee, $date_depart];
        
        if ($type && $type !== 'all') {
            $query .= " AND c.type = ?";
            $params[] = $type;
        }
        
        $query .= " ORDER BY c.prix_nuit ASC";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $i => $param) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère le calendrier des indisponibilités pour une chambre
     */
    public function getIndisponibilitesCalendar($year = null, $month = null) {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        
        $date_debut = "$year-$month-01";
        $date_fin = date('Y-m-t', strtotime($date_debut));
        
        $query = "SELECT i.date_debut, i.date_fin, i.motif
                 FROM indisponibilites i
                 WHERE i.chambre_id = ?
                 AND i.date_fin >= ?
                 AND i.date_debut <= ?
                 ORDER BY i.date_debut";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $date_debut);
        $stmt->bindParam(3, $date_fin);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Formate les amenities en tableau PHP depuis JSON
     */
    public function getAmenitiesArray() {
        if ($this->amenities) {
            return json_decode($this->amenities, true) ?? [];
        }
        return [];
    }
    
    /**
     * Récupère les chambres par type
     */
    public function getByType($type) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE type = ? AND disponible = 1 
                 ORDER BY prix_nuit ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $type);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre de chambres par type
     */
    public function countByType() {
        $query = "SELECT type, COUNT(*) as nb_chambres, MIN(prix_nuit) as prix_min, MAX(prix_nuit) as prix_max
                 FROM " . $this->table_name . " 
                 WHERE disponible = 1 
                 GROUP BY type 
                 ORDER BY prix_min ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Recherche de chambres avec filtres
     */
    public function search($filters = []) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE disponible = 1";
        $params = [];
        
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query .= " AND type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['prix_min'])) {
            $query .= " AND prix_nuit >= ?";
            $params[] = $filters['prix_min'];
        }
        
        if (!empty($filters['prix_max'])) {
            $query .= " AND prix_nuit <= ?";
            $params[] = $filters['prix_max'];
        }
        
        if (!empty($filters['capacite'])) {
            $query .= " AND capacite_max >= ?";
            $params[] = $filters['capacite'];
        }
        
        $query .= " ORDER BY prix_nuit ASC";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $i => $param) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
