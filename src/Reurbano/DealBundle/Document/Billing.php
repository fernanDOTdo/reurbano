<?php

namespace Reurbano\DealBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representa o faturamento normalizado das ofertoas do Source
 *
 * @author   Saulo Lima <saulo@startupsp.com.br>
 *
 * @ODM\EmbeddedDocument
 */
class Billing
{
    /**
     * Faturamento Normalizado
     *
     * @var float
     * @ODM\Float
     */
    protected $totalsellNormalized=0;
    
    /**
     * Parcela 1
     *
     * @var float
     * @ODM\Float
     */
    protected $parcelOne=0;
    
    /**
     * Parcela 2
     *
     * @var float
     * @ODM\Float
     */
    protected $parcelTwo=0;
    
    /**
     * Parcela 3
     *
     * @var float
     * @ODM\Float
     */
    protected $parcelThree=0;
    
    /**
     * Faturamento Cidade
     *
     * @var float
     * @ODM\Float
     */
    protected $faturamentoCity=0;
    
    /**
     * Total de cupons vendidos da cidade
     *
     * @var float
     * @ODM\Float
     */
    protected $couponsCity=0;
    
    /**
     * N�mero de coupons da cidade
     *
     * @var float
     * @ODM\Float
     */
    protected $nrCouponsCity=0;
    
    /**
     * Get totalsellNormalized
     *
     * @return float $totalsellNormalized
     */
    public function getTotalsellNormalized()
    {
        return $this->totalsellNormalized;
    }

    /**
     * Set totalsellNormalized
     *
     * @param float $totalsellNormalized
     */
    public function setTotalsellNormalized($totalsellNormalized)
    {
        $this->totalsellNormalized = $totalsellNormalized;
    }
    
    /**
     * Get parcelOne
     *
     * @return float $parcelOne
     */
    public function getParcelOne()
    {
        return $this->parcelOne;
    }

    /**
     * Set parcelOne
     *
     * @param float $parcel1
     */
    public function setParcelOne($parcelOne)
    {
        $this->parcelOne = $parcelOne;
    }
    
    /**
     * Get parcelTwo
     *
     * @return float $parcelTwo
     */
    public function getParcelTwo()
    {
        return $this->parcelTwo;
    }

    /**
     * Set parcelTwo
     *
     * @param float $parcelTwo
     */
    public function setParcelTwo($parcelTwo)
    {
        $this->parcelTwo = $parcelTwo;
    }
    
    /**
     * Get parcelThree
     *
     * @return float $parcelThree
     */
    public function getParcelThree()
    {
        return $this->parcelThree;
    }

    /**
     * Set parcelThree
     *
     * @param float $parcelThree
     */
    public function setParcelThree($parcelThree)
    {
        $this->parcelThree = $parcelThree;
    }
    
    /**
     * Get faturamentoCity
     *
     * @return float $faturamentoCity
     */
    public function getFaturamentoCity()
    {
        return $this->faturamentoCity;
    }

    /**
     * Set faturamentoCity
     *
     * @param float $faturamentoCity
     */
    public function setFaturamentoCity($faturamentoCity)
    {
        $this->faturamentoCity = $faturamentoCity;
    }
    
    /**
     * Get couponsCity
     *
     * @return float $couponsCity
     */
    public function getCouponsCity()
    {
        return $this->couponsCity;
    }
    
    /**
     * Set couponsCity
     *
     * @param float $couponsCity
     */
    public function setCouponsCity($couponsCity)
    {
        $this->couponsCity = $couponsCity;
    }
    
    /**
     * Get nrCouponsCity
     *
     * @return float $nrCouponsCity
     */
    public function getNrCouponsCity()
    {
        return $this->nrCouponsCity;
    }

    /**
     * Set nrCouponsCity
     *
     * @param float $nrCouponsCity
     */
    public function setNrCouponsCity($nrCouponsCity)
    {
        $this->nrCouponsCity = $nrCouponsCity;
    }
}
