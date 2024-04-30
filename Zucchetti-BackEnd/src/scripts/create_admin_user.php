<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use MyProject\Entity\BUser;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;

$isDevMode = true;
$paths = [__DIR__ . '/../src'];

$config = new Configuration();
$driver = new AttributeDriver($paths);
$config->setMetadataDriverImpl($driver);
$config->setProxyDir(__DIR__ . '/../src/Proxies');
$config->setProxyNamespace('MyProject\Proxies');
$config->setAutoGenerateProxyClasses($isDevMode);

$conn = [
    'driver'   => 'pdo_pgsql',
    'host'     => 'localhost',
    'port'     => '5432', 
    'dbname'   => 'DBZucchetti',
    'user'     => 'admin',
    'password' => 'admin',
];

$connection = DriverManager::getConnection($conn);
$entityManager = new EntityManager($connection, $config);

$user = new BUser();
$user->setUsername('ADMIN');
$user->setPassword(password_hash('ADMIN', PASSWORD_DEFAULT));

$entityManager->persist($user);
$entityManager->flush();

echo "Bussiness User admin/admin criado com sucesso. #tamojunto\n";
