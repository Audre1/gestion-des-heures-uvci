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
        // Séparer les instructions par point-virgule
        $statements = explode(';', $sql);

        // Filtrer les instructions vides et les commentaires
        $statements = array_filter($statements, function ($statement) {
            $statement = trim($statement);
            return !empty($statement) && !str_starts_with($statement, '--') && !str_starts_with($statement, '/*');
        });

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
}
