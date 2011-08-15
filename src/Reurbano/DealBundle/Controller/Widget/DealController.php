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
    public function renderAction($cat = null, $limit = 4, $pg = 1, $orderBy = 'createdAt', $template = 'default', $showSort = true, $pagination = true)
    {
        switch ($orderBy){
            case 'sortCheap':
                $sort = 'price';
                $order = 'asc';
                break;
            case 'sortExpires':
                $sort = 'source.expiresAt';
                $order = 'asc';
                break;
            case 'sortDiscount':
                $sort = 'discount';
                $order = 'desc';
                break;
            case 'sortNew':
            default :
                $sort = 'createdAt';
                $order = 'desc';
                break;
        }
        $dealRepo = $this->mongo("ReurbanoDealBundle:Deal");
        $dealQuery = $dealRepo->createQueryBuilder();
        if($this->get('session')->get('reurbano.user.city') == 'oferta-nacional'){
            $dealQuery->field('source.city.$id')->equals(new \MongoId($this->get('session')->get('reurbano.user.cityId')));
        }else{
            $cityNacional = $this->mongo("ReurbanoCoreBundle:City")->findBySlug('oferta-nacional');
            $dealQuery->field('source.city.$id')->in(array(new \MongoId($this->get('session')->get('reurbano.user.cityId')), new \MongoId($cityNacional->getId())));
        }
        $dealQuery->field('active')->equals(true);
        if($cat){
            $dealQuery->field('source.category.$id')->equals(new \MongoId($cat));
        }
        $total = 0;
        $dealQuery->sort('source.city.$id', 'desc')->sort($sort, $order)->sort('special', 'desc')->limit($limit);
        if($pg > 1){
            $pag = $pg - 1;
            $dealQuery->skip($limit * $pag);
        }
        $dealsFound = $dealQuery->getQuery()->execute();
        $deals = array();
        $found = 0;
        $total = 0;
        if($dealsFound){
            foreach ($dealsFound as $k => $d){
                $deals[$k] = $d;
            }
            $found = $dealsFound->count(true);
            $total = $dealsFound->count();
        }
        
        // Pagination
        if($total > $found){
            $restPages = $total % $limit;
            $restPages > 0 ? $totalPages = intval($total / $limit) + 1 : $totalPages = intval($total / $limit);
        }else{
            $totalPages = 1;
        }
        
        
        

        //$ofertas = $this->mongo('ReurbanoDealBundle:Deal')->findByCategory($categoria);
        return $this->render(
            'ReurbanoDealBundle:Widget/Deal:'.$template.'.html.twig',
            array(
                'orderBy'=> $orderBy,
                'found'  => $found,
                'total'  => $total,
                'cat'    => $cat,
                'limit'  => $limit,
                'pg'     => $pg,
                'totalPages'     => $totalPages,
                'deals'  => $deals,
                'pagination'  => $pagination,
                'showSort'  => $showSort,
            )
        );
    }
    
    
}