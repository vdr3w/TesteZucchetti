<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use MyProject\Entity\PaymentMethod;

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

$paymentMethod1 = new PaymentMethod();
$paymentMethod1->setName('Boleto');
$paymentMethod1->setInstallments(1);

$paymentMethod2 = new PaymentMethod();
$paymentMethod2->setName('PIX');
$paymentMethod2->setInstallments(1);

$paymentMethod3 = new PaymentMethod();
$paymentMethod3->setName('Cartão de Crédito');
$paymentMethod3->setInstallments(10);

$entityManager->persist($paymentMethod1);
$entityManager->persist($paymentMethod2);
$entityManager->persist($paymentMethod3);
$entityManager->flush();

echo "Métodos de Pagamento criados com sucesso. #tamojunto\n";
