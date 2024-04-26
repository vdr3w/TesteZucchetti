<?php
namespace MyProject;

require_once "bootstrap.php";

$productRepository = $entityManager->getRepository('MyProject\Product');
$products = $productRepository->findAll();

foreach ($products as $product) {
    echo sprintf("Product ID: %d, Name: %s\n", $product->getId(), $product->getName());
}
