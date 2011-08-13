<?php

namespace Reurbano\DealBundle\Controller\Widget;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reurbano\DealBundle\Document\Category;

/**
 * Controller para administrar (CRUD) categorias.
 */
class CategoryController extends BaseController {

    /**
     * Menu com as categorias
     * @Template()
     */
    public function menuAction($selected = null) {
        $categories = $this->mongo('ReurbanoDealBundle:Category')->findAllByOrder();
        return array('categories' => $categories, 'selected' => $selected);
    }

    
}