<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use MyProject\Entity\Customer;

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

$customer1 = new Customer();
$customer1->setName('Cliente 1');
$customer1->setCpf('99990000001');
$customer1->setEmail('cliente1@email.com');
$customer1->setCep('00000-001');
$customer1->setAddress('Rua Exemplo 1, 123');

$customer2 = new Customer();
$customer2->setName('Cliente 2');
$customer2->setCpf('99990000002');
$customer2->setEmail('cliente2@email.com');
$customer2->setCep('00000-002');
$customer2->setAddress('Rua Exemplo 2, 123');

$customer3 = new Customer();
$customer3->setName('Cliente 3');
$customer3->setCpf('99990000003');
$customer3->setEmail('cliente3@email.com');
$customer3->setCep('00000-003');
$customer3->setAddress('Rua Exemplo 3, 123');

$entityManager->persist($customer1);
$entityManager->persist($customer2);
$entityManager->persist($customer3);
$entityManager->flush();

echo "Primeiros Clientes criados com sucesso. #tamojunto\n";
