<?php

namespace MyProject\Repository;

use Doctrine\ORM\EntityRepository;
use MyProject\Entity\PaymentMethod;

class PaymentMethodRepository extends EntityRepository {
    public function save(PaymentMethod $paymentMethod) {
        $this->_em->persist($paymentMethod);
        $this->_em->flush();
    }

    public function remove(PaymentMethod $paymentMethod) {
        $this->_em->remove($paymentMethod);
        $this->_em->flush();
    }
}
