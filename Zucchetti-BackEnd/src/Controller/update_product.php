<?php
namespace MyProject;

require_once "bootstrap.php";

$id = $argv[1];
$newName = $argv[2];

$product = $entityManager->find('MyProject\Product', $id);
if ($product === null) {
    echo "Product $id does not exist.\n";
    exit(1);
}

$product->setName($newName);
$entityManager->flush();

echo "Product updated successfully.\n";
