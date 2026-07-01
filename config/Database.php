<?php
// ============================================================
// config/Database.php
// Connexion PDO centralisée et sécurisée
// ============================================================

declare(strict_types=1);

class Database
{
    // Paramètres de connexion — adaptez selon votre environnement
    private const DB_HOST    = 'localhost';
    private const DB_NAME    = 'fitconnect';
    private const DB_USER    = 'root';
    private const DB_PASS    = '';
    private const DB_CHARSET = 'utf8mb4';

    // Instance unique (Singleton)
    private static ?PDO $instance = null;

    // Constructeur privé : on ne peut pas instancier cette classe directement
    private function __construct() {}

    /**
     * Retourne la connexion PDO unique.
     * Si elle n'existe pas encore, elle est créée ici.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::DB_HOST,
                self::DB_NAME,
                self::DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // On ne redirige pas l'erreur brute vers l'utilisateur pour des raisons de sécurité
                error_log('[FitConnect] Erreur connexion BDD : ' . $e->getMessage());
                throw new RuntimeException('Impossible de se connecter à la base de données. Veuillez réessayer plus tard.');
            }
        }

        return self::$instance;
    }
}
