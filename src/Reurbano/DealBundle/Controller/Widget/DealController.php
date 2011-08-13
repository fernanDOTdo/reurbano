<?php
namespace Reurbano\DealBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mastop\SystemBundle\Controller\BaseController;

/*
 * Controller para os Widgets do Deal
 */

class DealController extends BaseController
{
    /**
     * Widget que renderiza as ofertas
     * 
     */
    public function renderAction($cat = null, $limit = 10, $skip = 0, $sort = 'views', $order = 'desc', $template = 'default')
    {
        $dealRepo = $this->mongo("ReurbanoDealBundle:Deal");
        $dealQuery = $dealRepo->createQueryBuilder();
        $dealQuery->field('source.city.$id')->equals(new \MongoId($this->get('session')->get('reurbano.user.cityId')));
        $dealQuery->field('active')->equals(true);
        if($cat){
            $dealQuery->field('source.category.$id')->equals(new \MongoId($cat));
        }
        $dealQuery->sort($sort, $order)->limit($limit);
        if($skip > 0){
            $dealQuery->skip($skip);
        }
        $dealsFound = $dealQuery->getQuery()->execute();
        $deals = array();
        $total = 0;
        if($dealsFound){
            foreach ($dealsFound as $k => $d){
                $deals[$k] = $d;
            }
            $total = count($dealsFound);
        }
        
        

        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findByCategory($categoria);
        return $this->render(
            'ReurbanoDealBundle:Widget/Deal:'.$template.'.html.twig',
            array(
                'total'  => $total,
                'cat'    => $cat,
                'limit'  => $limit,
                'skip'   => $skip,
                'deals'  => $deals,
            )
        );
    }
    
}