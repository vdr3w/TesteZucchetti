<?php
// bootstrap.php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

require_once "vendor/autoload.php";

// Configurações para ambiente de desenvolvimento
$isDevMode = true;

// Configuração usando atributos de metadados
$config = ORMSetup::createAttributeMetadataConfiguration(
    [__DIR__ . "/src"], 
    $isDevMode
);

// Configurações de conexão para PostgreSQL
$conn = DriverManager::getConnection([
    'driver'   => 'pdo_pgsql',  // Driver do PostgreSQL
    'host'     => 'localhost',  // ou o endereço IP do servidor PostgreSQL
    'port'     => '5432',       // Porta padrão do PostgreSQL
    'dbname'   => 'DBZucchetti', // Nome do banco de dados
    'user'     => 'admin',     // Nome do usuário
    'password' => 'admin', // Senha do usuário
], $config);

// Criando o EntityManager com a conexão configurada
$entityManager = new EntityManager($conn, $config);

// Configuração das Migrations
$migrationsConfig = new PhpFile(__DIR__ . '/migrations.php');
$entityManagerLoader = new ExistingEntityManager($entityManager);
$dependencyFactory = DependencyFactory::fromEntityManager($migrationsConfig, $entityManagerLoader);
