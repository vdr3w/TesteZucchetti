<?php
namespace MyProject;

require_once "bootstrap.php";

$id = $argv[1];
$product = $entityManager->find('MyProject\Product', $id);

if ($product === null) {
    echo "No product found.\n";
    exit(1);
}

echo sprintf("Product ID: %d, Name: %s\n", $product->getId(), $product->getName());
