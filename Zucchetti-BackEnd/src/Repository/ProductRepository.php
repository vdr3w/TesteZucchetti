<?php

namespace MyProject\Repository;

use Doctrine\ORM\EntityRepository;
use MyProject\Entity\Product;

class ProductRepository extends EntityRepository {
    public function save(Product $product) {
        $this->_em->persist($product);
        $this->_em->flush();
    }

    public function remove(Product $product) {
        $this->_em->remove($product);
        $this->_em->flush();
    }
}
