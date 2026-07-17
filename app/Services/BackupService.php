<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupService
{
    protected $backupPath;
    protected $database;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->database = config('database.connections.mysql.database');

        // Créer le dossier de sauvegarde s'il n'existe pas
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Crée une sauvegarde de la base de données
     */
    public function backup(): string
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;

        Log::info('Début de la sauvegarde', ['filename' => $filename]);

        try {
            // Utiliser PDO pour créer le dump
            $sql = $this->getDatabaseDump();

            // Écrire le fichier
            File::put($filepath, $sql);

            Log::info('Sauvegarde réussie', ['filename' => $filename]);

            if (function_exists('logActivite')) {
                logActivite('sauvegarde', 'Création de la sauvegarde ' . $filename);
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde', ['error' => $e->getMessage(), 'filename' => $filename]);
            throw new \Exception('Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Crée une sauvegarde automatique (avec rotation)
     */
    public function autoBackup(): string
    {
        $filename = 'backup_auto_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;

        Log::info('Début de la sauvegarde automatique', ['filename' => $filename]);

        try {
            // Utiliser PDO pour créer le dump
            $sql = $this->getDatabaseDump();

            // Écrire le fichier
            File::put($filepath, $sql);

            Log::info('Sauvegarde automatique réussie', ['filename' => $filename]);

            // Récupérer les paramètres de rotation depuis la base
            $rotationCount = $this->getBackupRotationCount();

            // Rotation des sauvegardes automatiques
            $this->rotateAutoBackups($rotationCount);

            if (function_exists('logActivite')) {
                logActivite('sauvegarde_auto', 'Sauvegarde automatique créée : ' . $filename);
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde automatique', ['error' => $e->getMessage(), 'filename' => $filename]);
            throw new \Exception('Erreur lors de la sauvegarde automatique : ' . $e->getMessage());
        }
    }

    /**
     * Génère le dump SQL de la base de données
     */
    protected function getDatabaseDump(): string
    {
        $pdo = DB::connection()->getPdo();
        $database = $this->database;

        $sql = "-- Database: {$database}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ----------------------------------------\n\n";

        // Désactiver les contraintes de clés étrangères
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Récupérer toutes les tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            Log::debug('Sauvegarde de la table', ['table' => $table]);

            $sql .= "-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            // Créer la structure de la table
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= $createTable['Create Table'] . ";\n\n";

            // Récupérer les données
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columns = array_map(function ($col) {
                    return "`{$col}`";
                }, $columns);
                $columnsStr = implode(', ', $columns);

                $sql .= "INSERT INTO `{$table}` ({$columnsStr}) VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        return $pdo->quote($val);
                    }, $row);
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        // Réactiver les contraintes de clés étrangères
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /**
     * Récupère la liste des sauvegardes
     */
    public function getBackups(): array
    {
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_starts_with($filename, 'backup_') && str_ends_with($filename, '.sql')) {
                $backups[] = [
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'size' => $this->formatFileSize($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'date' => date('d/m/Y H:i', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Trier par date décroissante
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $backups;
    }

    /**
     * Supprime une sauvegarde
     */
    public function deleteBackup(string $filename): bool
    {
        $filepath = $this->backupPath . '/' . $filename;

        Log::info('Tentative de suppression de sauvegarde', ['filename' => $filename]);

        if (File::exists($filepath)) {
            $result = File::delete($filepath);

            if ($result) {
                Log::info('Sauvegarde supprimée avec succès', ['filename' => $filename]);
                if (function_exists('logActivite')) {
                    logActivite('suppression', 'Suppression de la sauvegarde ' . $filename);
                }
            } else {
                Log::error('Échec de la suppression de la sauvegarde', ['filename' => $filename]);
            }

            return $result;
        }

        Log::warning('Fichier de sauvegarde introuvable pour suppression', ['filename' => $filename]);
        return false;
    }

    /**
     * Télécharge une sauvegarde
     */
    public function downloadBackup(string $filename): string
    {
        Log::info('Téléchargement de sauvegarde', ['filename' => $filename]);

        if (function_exists('logActivite')) {
            logActivite('téléchargement', 'Téléchargement de la sauvegarde ' . $filename);
        }

        return $this->backupPath . '/' . $filename;
    }

    /**
     * Restaure une sauvegarde
     */
    public function restore(string $filename): bool
    {
        $filepath = $this->backupPath . '/' . $filename;

        Log::info('Début de la restauration', ['filename' => $filename]);

        if (!File::exists($filepath)) {
            Log::error('Fichier de sauvegarde introuvable pour restauration', ['filename' => $filename]);
            throw new \Exception('Fichier de sauvegarde introuvable.');
        }

        try {
            // Lire le fichier SQL
            $sql = File::get($filepath);

            // Exécuter les commandes SQL
            $pdo = DB::connection()->getPdo();

            // Exécuter le SQL instruction par instruction
            $statements = $this->parseSqlStatements($sql);

            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    $pdo->exec($statement);
                }
            }

            Log::info('Restauration réussie', ['filename' => $filename]);

            if (function_exists('logActivite')) {
                logActivite('restauration', 'Restauration de la sauvegarde ' . $filename);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration', ['error' => $e->getMessage(), 'filename' => $filename]);
            throw new \Exception('Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    /**
     * Parse les instructions SQL
     */
    protected function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $currentStatement = '';
        $inComment = false;
        $inString = false;
        $stringChar = '';
        $lines = explode("\n", $sql);

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Ignorer les lignes de commentaires simples
            if (str_starts_with($trimmedLine, '--') || empty($trimmedLine)) {
                continue;
            }

            // Gérer les blocs de commentaires multi-lignes
            if (str_starts_with($trimmedLine, '/*') || str_ends_with($trimmedLine, '*/')) {
                continue;
            }

            $currentStatement .= $line . "\n";

            // Vérifier si la ligne se termine par un point-virgule (hors des chaînes)
            if (!$inString && str_ends_with($trimmedLine, ';')) {
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            }

            // Gérer les chaînes de caractères pour éviter de couper à l'intérieur
            for ($i = 0; $i < strlen($line); $i++) {
                $char = $line[$i];

                if ($char === "'" || $char === '"') {
                    if (!$inString) {
                        $inString = true;
                        $stringChar = $char;
                    } elseif ($char === $stringChar && $line[$i - 1] !== '\\') {
                        $inString = false;
                        $stringChar = '';
                    }
                }
            }
        }

        // Ajouter la dernière instruction si elle n'est pas vide
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }

        return $statements;
    }

    /**
     * Formate la taille du fichier
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Récupère la taille totale des sauvegardes
     */
    public function getTotalSize(): string
    {
        $backups = $this->getBackups();
        $totalBytes = array_sum(array_column($backups, 'size_bytes'));

        return $this->formatFileSize($totalBytes);
    }

    /**
     * Récupère la date de la dernière sauvegarde
     */
    public function getLastBackupDate(): ?string
    {
        $backups = $this->getBackups();

        if (empty($backups)) {
            return null;
        }

        return $backups[0]['date'];
    }

    /**
     * Vérifie si une sauvegarde automatique récente existe (dans les dernières 24h)
     */
    public function hasRecentAutoBackup(int $hours = 24): bool
    {
        $backups = $this->getBackups();

        if (empty($backups)) {
            return false;
        }

        // Filtrer uniquement les sauvegardes automatiques
        $autoBackups = array_filter($backups, function ($backup) {
            return str_starts_with($backup['filename'], 'backup_auto_');
        });

        if (empty($autoBackups)) {
            return false;
        }

        // Vérifier la plus récente
        $latestBackup = reset($autoBackups);
        $lastBackupTime = $latestBackup['timestamp'];
        $threshold = time() - ($hours * 3600);

        return $lastBackupTime > $threshold;
    }

    /**
     * Récupère le délai de sauvegarde automatique depuis les paramètres
     */
    public function getBackupDelay(): int
    {
        try {
            $parametre = \App\Models\ParametreCalcul::anneeActive()->first();
            return $parametre ? $parametre->sauvegarde_auto_delai ?? 24 : 24;
        } catch (\Exception $e) {
            Log::warning('Impossible de récupérer le délai de sauvegarde, utilisation de la valeur par défaut', [
                'error' => $e->getMessage()
            ]);
            return 24;
        }
    }

    /**
     * Récupère le nombre de sauvegardes automatiques à conserver depuis les paramètres
     */
    public function getBackupRotationCount(): int
    {
        try {
            $parametre = \App\Models\ParametreCalcul::anneeActive()->first();
            return $parametre ? $parametre->sauvegarde_auto_rotation ?? 7 : 7;
        } catch (\Exception $e) {
            Log::warning('Impossible de récupérer la rotation de sauvegarde, utilisation de la valeur par défaut', [
                'error' => $e->getMessage()
            ]);
            return 7;
        }
    }

    /**
     * Rotation des sauvegardes automatiques
     * Garde les $keepCount dernières sauvegardes automatiques
     */
    protected function rotateAutoBackups(int $keepCount = 7): void
    {
        $files = File::files($this->backupPath);
        $autoBackups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_starts_with($filename, 'backup_auto_') && str_ends_with($filename, '.sql')) {
                $autoBackups[] = [
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Trier par date décroissante
        usort($autoBackups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        // Supprimer les sauvegardes au-delà de la limite
        $toDelete = array_slice($autoBackups, $keepCount);

        foreach ($toDelete as $backup) {
            File::delete($backup['path']);
            Log::info('Sauvegarde automatique supprimée par rotation', ['filename' => $backup['filename']]);

            if (function_exists('logActivite')) {
                logActivite('rotation_auto', 'Suppression automatique de la sauvegarde ' . $backup['filename']);
            }
        }
    }
}
