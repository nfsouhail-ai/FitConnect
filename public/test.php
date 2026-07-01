<?php
// ============================================================
// public/test.php
// Script de test pour vérifier la connexion à la base de données
// ============================================================

declare(strict_types=1);

define('ROOT', dirname(__DIR__));

require_once ROOT . '/config/Database.php';

echo "<h1>Test de connexion à la base de données FitConnect</h1>";

try {
    $pdo = Database::getConnection();
    echo "<p style='color: green; font-weight: bold;'>Connexion réussie ! PDO est bien configuré.</p>";
    
    // Test d'une requête simple
    $stmt = $pdo->query("SELECT COUNT(*) FROM salles");
    $count = $stmt->fetchColumn();
    
    echo "<p>Nombre de salles dans la base de données : <strong>" . $count . "</strong></p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
}
