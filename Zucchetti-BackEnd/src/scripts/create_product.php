<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use MyProject\Entity\Product;

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
    'host'     => 'postgres',
    'port'     => '5432', 
    'dbname'   => 'DBZucchetti',
    'user'     => 'admin',
    'password' => 'admin',
];

$connection = DriverManager::getConnection($conn);
$entityManager = new EntityManager($connection, $config);

$product1 = new Product();
$product1->setName('Produto Inicial 1');
$product1->setPrice(5);
$product1->setQuantity(50);

$product2 = new Product();
$product2->setName('Produto Inicial 2');
$product2->setPrice(10);
$product2->setQuantity(50);

$product3 = new Product();
$product3->setName('Produto Inicial 3');
$product3->setPrice(15);
$product3->setQuantity(50);

$entityManager->persist($product1);
$entityManager->persist($product2);
$entityManager->persist($product3);
$entityManager->flush();

echo "Primeiros Produtos criados com sucesso. #tamojunto\n";
