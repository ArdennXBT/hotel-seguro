<?php
/**
 * Script de test pour la création de compte
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/User.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("Erreur de connexion à la base de données");
    }
    
    echo "✅ Connexion réussie à la base de données<br>";
    
    $user = new User($db);
    
    // Test de création de compte
    $testEmail = 'test' . time() . '@example.com';
    echo "📧 Test de création de compte avec email: $testEmail<br>";
    
    $result = $user->createAutoAccount('Test', 'Utilisateur', $testEmail, '+22990000000', 'Bénin');
    
    if ($result) {
        echo "✅ Compte créé avec succès !<br>";
        echo "ID: " . $result->id . "<br>";
        echo "Nom: " . $result->nom . "<br>";
        echo "Prénom: " . $result->prenom . "<br>";
        echo "Email: " . $result->email . "<br>";
        echo "Code client: " . $result->code_client . "<br>";
        echo "Rôle: " . $result->role . "<br>";
    } else {
        echo "❌ Échec de la création du compte<br>";
    }
    
    // Test de récupération par email
    echo "<br>📧 Test de récupération par email...<br>";
    $retrieved = $user->getByEmail($testEmail);
    if ($retrieved) {
        echo "✅ Utilisateur récupéré avec succès<br>";
    } else {
        echo "❌ Échec de la récupération<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br>" . $e->getTraceAsString();
}
?>
