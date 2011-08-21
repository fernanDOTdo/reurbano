<?php

namespace Reurbano\OrderBundle\Document;

use Mastop\SystemBundle\Document\BaseRepository;

class OrderRepository extends BaseRepository
{

    /**
     * Pega todos ordenado por CREATED
     *
     * @return Order ou null
     **/
    public function findAllByCreated()
    {
        return $this->findBy(array(), array('created'=>'asc'));
    }
    
    /**
     * Cria um novo pedido e setar um id para o pedido
     * 
     * @return Object order
     */
    public function createOrder()
    {
        $control = true;
        $count = 0;
        while ($control){
            if($count < 3){
                $id = round(abs(crc32(uniqid(rand(), true)) / 1000));
                if(!$this->hasId($id)){
                    $control = false;
                }else{
                    $count++;
                }
            }else{
                $id = round(abs(crc32(uniqid(rand(), true))));
                while($this->hasId($id)){
                    $id = round(abs(crc32(uniqid(rand(), true))));
                }
                $control = false;
            }
        }
        $order = new order();
        $order->setId($id);
        return $order;
    }
    public function findByUser($id){
        
        return $this->findBy(array('user.$id' => new \MongoId($id)));
        
    }
}