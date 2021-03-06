<?php

namespace Reurbano\DealBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mastop\SystemBundle\Controller\BaseController;

/*
 * Controller para os Widgets do Deal
 */

class DealController extends BaseController {

    /**
     * Widget que renderiza as ofertas
     * 
     */
    public function renderAction($cat = null, $limit = 4, $pg = 1, $orderBy = 'createdAt', $template = 'default', $showSort = true, $pagination = true, $search = null) {
        switch ($orderBy) {
            case 'sortRanking':
                $sort = 'source.billing.totalsellNormalized';
                $order = 'desc';
                break;
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
           case 'sortAggregator':
	           	$sort = 'priceOffer';
                $order = 'asc';
	           	$aggregator = $this->getDealAggregator($cat, $limit, $pg, $orderBy, $template, $showSort, $pagination, $search);
	           	return $this->render('ReurbanoDealBundle:Widget/Deal:aggregator.html.twig',  $aggregator);
           	break;
            case 'sortNew':
            default :
                $sort = 'createdAt';
                $order = 'desc';
                break;
        }
        
        $dealRepo = $this->mongo("ReurbanoDealBundle:Deal");
        $dealQuery = $dealRepo->createQueryBuilder();
        $dealQuery->field('checked')->equals(true);
        if ($this->get('session')->get('reurbano.user.city') == 'oferta-nacional') {
            $dealQuery->field('source.city.$id')->equals(new \MongoId($this->get('session')->get('reurbano.user.cityId')));
        } else {
            $cityNacionalId = $this->get('session')->get('reurbano.user.nacional');
            $dealQuery->field('source.city.$id')->in(array(new \MongoId($this->get('session')->get('reurbano.user.cityId')), new \MongoId($cityNacionalId)));
        }
        $dealQuery->field('active')->equals(true)->field('quantity')->gt(0); // Ofertas ativas e com quantidade maior que zero
        if ($cat) {
            $dealQuery->field('source.category.$id')->equals(new \MongoId($cat));
        }
        if($search){
            $regexp = new \MongoRegex('/' . $search . '/i');
            $tags = explode(' ', $search);
            $dealQuery->addOr($dealQuery->expr()->field('label')->equals($regexp))->addOr($dealQuery->expr()->field('tags')->all($tags));
        }
        $total = 0;
        

        if($sort != 'price'){ // Se a ordena��o escolhida n�o for por pre�o, ordena por cidade -> ordena��o escolhida -> destaques -> pre�o
            $dealQuery->sort('source.city.$id', 'desc')->sort($sort, $order)->sort('special', 'desc')->sort('price', 'asc')->limit($limit);
        }else{  // Se a ordena��o escolhida for por pre�o, ordena por cidade -> pre�o -> destaques
            $dealQuery->sort('source.city.$id', 'desc')->sort($sort, $order)->sort('special', 'desc')->limit($limit);
        }
        
        if ($pg > 1) {
            $pag = $pg - 1;
            $dealQuery->skip($limit * $pag);
        }
        $dealsFound = $dealQuery->getQuery()->execute();
        $deals = array();
        $found = 0;
        $total = 0;
        if ($dealsFound) {
            foreach ($dealsFound as $k => $d) {
                $deals[$k] = $d;
            }
            $found = $dealsFound->count(true);
            $total = $dealsFound->count();
        }

        // Pagination
        if ($total > $found) {
            $restPages = $total % $limit;
            $restPages > 0 ? $totalPages = intval($total / $limit) + 1 : $totalPages = intval($total / $limit);
        } else {
            $totalPages = 1;
        }
        return $this->render(
                        'ReurbanoDealBundle:Widget/Deal:' . $template . '.html.twig', array(
                    'orderBy' => $orderBy,
                    'found' => $found,
                    'total' => $total,
                    'cat' => $cat,
                    'limit' => $limit,
                    'pg' => $pg,
                    'totalPages' => $totalPages,
                    'deals' => $deals,
                    'pagination' => $pagination,
                    'showSort' => $showSort,
                    'search' => $search,
                        )
        );
    }

    /**
     * Widget que renderiza uma oferta no bloco
     * @param string $deal id do Deal
     */
    public function blockAction($deal) {
        $theDeal = $this->mongo('ReurbanoDealBundle:Deal')->findOneById($deal);
        return $this->render(
                        'ReurbanoDealBundle:Widget/Deal:block.html.twig', array(
                        'deal' => $theDeal,
                        )
        );
    }
    /**
     * Widget que renderiza uma oferta no bloco Topo
     * @param string $deal id do Deal
     */
    public function blockTopAction($deal) {
        $theDeal = $this->mongo('ReurbanoDealBundle:Deal')->findOneById($deal);
        return $this->render(
                        'ReurbanoDealBundle:Widget/Deal:blockTop.html.twig', array(
                        'deal' => $theDeal,
                        )
        );
    }
    
    /**
     * Widget que renderiza as ofertas do crawler para a parte do agregador
     *
     */
    public function getDealAggregator($cat = null, $limit = 4, $pg = 1, $orderBy = 'createdAt', $template = 'default', $showSort = true, $pagination = true, $search = null) {
    	$sourceQuery = $this->mongo("ReurbanoDealBundle:Source", 'crawler')->createQueryBuilder();
    	
    	if ($this->get('session')->get('reurbano.user.city') == 'oferta-nacional') {
    	    $sourceQuery->field('city.$id')->equals(new \MongoId($this->get('session')->get('reurbano.user.cityId')));
    	} else {
    	    $cityNacionalId = $this->get('session')->get('reurbano.user.nacional');
    	    $sourceQuery->field('city.$id')->in(array(new \MongoId($this->get('session')->get('reurbano.user.cityId')), new \MongoId($cityNacionalId)));
    	}
    	
    	if ($cat) {
    		$sourceQuery->field('category.$id')->equals(new \MongoId($cat));
    	}
    	
    	// Ser� dado o display apenas nas ofertas dos ultimos 5 dias
    	$days = 5;
    	$date = new \DateTime();
    	$date->setTimestamp(strtotime('-'.$days.' days'));
    	$date->setTime(0, 0, 0);
    	
    	$datexpiresDeal = new \DateTime();
    	$datexpiresDeal->setTime(0, 0, 0);
    	
    	$sourceQuery->field('dateRegister')->gte($date); // Data de registro maior ou igual ao "date"
    	$sourceQuery->field('price')->gt(0); // Pre�o normal maior que ZERO
    	$sourceQuery->field('priceOffer')->gt(0); // Pre�o com desconto maior que ZERO
    	// Data do fim das negocia��es n�o existe ou seja maior que hoje
    	$sourceQuery->addOr($sourceQuery->expr()->field('expiresDeal')->exists(false))->addOr($sourceQuery->expr()->field('expiresDeal')->gte($datexpiresDeal)); 
    	
    	if($search){
    		$regexp = new \MongoRegex('/' . $search . '/i');
    		$tags = explode(' ', $search);
    		$sourceQuery->addOr($sourceQuery->expr()->field('title')->equals($regexp));
    	}
    	$total = 0;
    	
    	// Se a ordena��o escolhida n�o for por pre�o, ordena por cidade -> ordena��o escolhida -> destaques -> pre�o
    	$sourceQuery->sort('priceOffer', 'asc')->limit($limit);
    	
    	if ($pg > 1) {
    		$pag = $pg - 1;
    		$sourceQuery->skip($limit * $pag);
    	}
    	
    	$sourcesFound = $sourceQuery->getQuery()->execute();
    	$sources = array();
    	$found = 0;
    	$total = 0;
    	if ($sourcesFound) {
    		foreach ($sourcesFound as $k => $d) {
    			$sources[$k] = $d;
    		}
    		$found = $sourcesFound->count(true);
    		$total = $sourcesFound->count();
    	}
    
    	// Pagination
    	if ($total > $found) {
    		$restPages = $total % $limit;
    		$restPages > 0 ? $totalPages = intval($total / $limit) + 1 : $totalPages = intval($total / $limit);
    	} else {
    		$totalPages = 1;
    	}
    	
    	return  array(
    					'orderBy' => $orderBy,
    					'found' => $found,
    					'total' => $total,
    					'cat' => $cat,
    					'limit' => $limit,
    					'pg' => $pg,
    					'totalPages' => $totalPages,
    					'deals' => $sources,
    					'pagination' => $pagination,
    					'showSort' => $showSort,
    					'search' => $search,
    	);
    }

}