<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// Carrega o autoload e o bootstrap
require 'vendor/autoload.php';

// Use o EntityManager definido no seu bootstrap.php
$entityManager = require 'bootstrap.php';  // Mudança de require_once para require

// Retorna o HelperSet necessário para o console do Doctrine
return ConsoleRunner::createHelperSet($entityManager);
