<?php

namespace MyProject\Repository;

use Doctrine\ORM\EntityRepository;
use MyProject\Entity\Customer;

class CustomerRepository extends EntityRepository {
    public function save(Customer $customer) {
        $this->_em->persist($customer);
        $this->_em->flush();
    }

    public function remove(Customer $customer) {
        $this->_em->remove($customer);
        $this->_em->flush();
    }
}
