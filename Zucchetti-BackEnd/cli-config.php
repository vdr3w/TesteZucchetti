<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require 'vendor/autoload.php';

$entityManager = require 'bootstrap.php';

return ConsoleRunner::createHelperSet($entityManager);
