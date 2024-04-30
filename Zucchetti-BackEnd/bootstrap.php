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

// Configuração de CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 204 No Content');
    exit;
}

$isDevMode = true;

$config = ORMSetup::createAttributeMetadataConfiguration(
    [__DIR__ . "/src"], 
    $isDevMode
);

$conn = DriverManager::getConnection([
    'driver'   => 'pdo_pgsql',
    'host'     => 'localhost',
    'port'     => '5432', 
    'dbname'   => 'DBZucchetti',
    'user'     => 'admin',
    'password' => 'admin',
], $config);

$entityManager = new EntityManager($conn, $config);

$migrationsConfig = new PhpFile(__DIR__ . '/migrations.php');
$entityManagerLoader = new ExistingEntityManager($entityManager);
$dependencyFactory = DependencyFactory::fromEntityManager($migrationsConfig, $entityManagerLoader);

function GetEntityManager(): EntityManager {
    global $entityManager;
    return $entityManager;
}

set_exception_handler(function ($exception) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unexpected error occurred: ' . $exception->getMessage()]);
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Fatal error: ' . $error['message']]);
        exit;
    }
});

return $entityManager;