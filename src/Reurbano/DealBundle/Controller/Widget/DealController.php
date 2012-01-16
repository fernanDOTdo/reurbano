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
        if($sort != 'price'){ // Se a ordenação escolhida não for por preço, ordena por cidade -> ordenação escolhida -> destaques -> preço
            $dealQuery->sort('source.city.$id', 'desc')->sort($sort, $order)->sort('special', 'desc')->sort('price', 'asc')->limit($limit);
        }else{  // Se a ordenação escolhida for por preço, ordena por cidade -> preço -> destaques
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

}