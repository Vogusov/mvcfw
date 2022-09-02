<?php

namespace app\core\db;

use \app\core\Application;

class Database
{

    public \PDO $pdo;

    /**
     * Конструктор класса Database
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'];
        $user = $config['user'];
        $password = $config['password'];
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Сравнивает существующие миграции из БД с файлами миграций в /migrations и запускает несуществующие
     * @return void
     */
    public function applyMigration()
    {
        $this->creteMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR . '/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        $newMigrations = [];
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied");
        }

        exit;
    }

    /**
     * Создает в БД таблицу с миграциями, если ее нет
     * @return void
     */
    private function creteMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ) ENGINE=INNODB;");
    }

    /**
     * Получает список миграций из БД
     * @return array|false
     */
    private function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations;");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Записывает миграции в БД
     * @param array $migrations
     * @return void
     */
    private function saveMigrations(array $migrations)
    {
        $migrations_str = implode(',', array_map(fn($migration) => "('$migration')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrations_str");
        $statement->execute();
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    protected function log($message) {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}