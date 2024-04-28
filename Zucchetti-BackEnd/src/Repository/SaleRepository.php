<?php

namespace MyProject\Repository;

use Doctrine\ORM\EntityRepository;
use MyProject\Entity\Sale;

class SaleRepository extends EntityRepository {
    public function save(Sale $sale) {
        $this->_em->persist($sale);
        $this->_em->flush();
    }

    public function remove(Sale $sale) {
        $this->_em->remove($sale);
        $this->_em->flush();
    }
}
