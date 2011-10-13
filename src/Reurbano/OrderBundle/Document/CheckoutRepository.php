<?php

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;
use Reurbano\OrderBundle\Document\Checkout;

class CheckoutRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Checkout ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'asc'));
    }
    
    public function findByUser($id) {
        return $this->findBy(array('user.id' => $id), array('created' => 'desc'));
    }
    
    /**
     * Cria um novo checkout e setar um id
     * 
     * @return Object Checkout
     */
    public function createCheckout() {
        $control = true;
        $count = 0;
        while ($control) {
            if ($count < 3) {
                $id = round(abs(crc32(uniqid(rand(), true)) / 1000000));
                if ($id > 0 && !$this->hasId($id)) {
                    $control = false;
                } else {
                    $count++;
                }
            } else {
                $id = round(abs(crc32(uniqid(rand(), true))));
                while ($this->hasId($id)) {
                    $id = round(abs(crc32(uniqid(rand(), true))));
                }
                $control = false;
            }
        }
        $checkout = new Checkout();
        $checkout->setId($id);
        return $checkout;
    }

}