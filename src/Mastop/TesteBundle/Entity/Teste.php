<?php
namespace Mastop\TesteBundle\Entity;

class Teste
{
    public $name;

    protected $price;

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
}