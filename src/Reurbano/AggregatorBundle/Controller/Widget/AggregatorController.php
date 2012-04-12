<?php

namespace Reurbano\AggregatorBundle\Controller\Widget;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mastop\SystemBundle\Controller\BaseController;

/*
 * Controller para os Widgets das ofertas do agregador
 */

class AggregatorController extends BaseController {

    /**
     * Widget que renderiza as ofertas para o agregador
     * 
     */
    public function renderAction($cat = null, $limit = 4, $pg = 1, $orderBy = 'createdAt', $template = 'default', $showSort = true, $pagination = true, $search = null) {
        switch ($orderBy) {
            case 'sortRanking':
                $sort = 'billing.totalsellNormalized';
                $order = 'desc';
                break;
            case 'sortCheap':
                $sort = 'price';
                $order = 'asc';
                break;
            case 'sortExpires':
                $sort = 'expiresAt';
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
    	
    	// Será dado o display apenas nas ofertas dos ultimos 5 dias
    	$days = 5;
    	$date = new \DateTime();
    	$date->setTimestamp(strtotime('-'.$days.' days'));
    	$date->setTime(0, 0, 0);
    	
    	$datexpiresDeal = new \DateTime();
    	$datexpiresDeal->setTime(0, 0, 0);
    	
    	$sourceQuery->field('dateRegister')->gte($date); // Data de registro maior ou igual ao "date"
    	$sourceQuery->field('price')->gt(0); // Preço normal maior que ZERO
    	$sourceQuery->field('priceOffer')->gt(0); // Preço com desconto maior que ZERO
    	// Data do fim das negociações não existe ou seja maior que hoje
    	$sourceQuery->addOr($sourceQuery->expr()->field('expiresDeal')->exists(false))->addOr($sourceQuery->expr()->field('expiresDeal')->gte($datexpiresDeal)); 
    	
    	
    	if($search){
    		$regexp = new \MongoRegex('/' . $search . '/i');
    		$tags = explode(' ', $search);
    		$sourceQuery->addOr($sourceQuery->expr()->field('title')->equals($regexp));
    	}
    	
    	$total = 0;
    	if($sort != 'price'){ // Se a ordenação escolhida não for por preço, ordena por cidade -> ordenação escolhida -> destaques -> preço
    		$sourceQuery->sort('city.$id', 'desc')->sort($sort, $order)->sort('price', 'asc')->limit($limit);
    	}else{  // Se a ordenação escolhida for por preço, ordena por cidade -> preço -> destaques
    		$sourceQuery->sort('city.$id', 'desc')->sort($sort, $order)->limit($limit);
    	}    	
    	
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
        return $this->render(
                        'ReurbanoAggregatorBundle:Widget/Aggregator:' . $template . '.html.twig', array(
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
                        'ReurbanoAggregatorBundle:Widget/Aggregator:block.html.twig', array(
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
                        'ReurbanoAggregatorBundle:Widget/Aggregator:blockTop.html.twig', array(
                        'deal' => $theDeal,
                        )
        );
    }

}