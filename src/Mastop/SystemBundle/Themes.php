<?php

/*
* This file is part of the Mastop/SystemBundle
*
* (c) Mastop Iternet Development
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Mastop\SystemBundle;

/**
* Contém o tema atual e permite a alteração.
*
*/
class Themes
{
    private $name;
    private $themes;
    private $dir;

/**
* @param string $name
* @param array $allowedThemes
*/
    public function __construct($name, array $allowedThemes, $themesDir)
    {
        $this->themes = $allowedThemes;
        $this->dir = $themesDir;
        $this->setName($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if (! in_array($name, $this->themes)) {
            throw new \InvalidArgumentException(sprintf('O tema atual não está na lista de temas permitidos.'));
        }
        $this->name = $name;
    }
    public function getDir(){
        return $this->dir;
    }
    public function getAllowedThemes(){
        return $this->themes;
    }
}

