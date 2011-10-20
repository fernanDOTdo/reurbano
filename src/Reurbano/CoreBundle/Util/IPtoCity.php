<?php
namespace Reurbano\CoreBundle\Util;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Classe para obter dados através do IP
 *
 * @author Fernando Santos <o@fernan.do>
 */
class IPtoCity {
    protected $ip;
    protected $city;
    protected $country;
    protected $coordinates = array();
    function __construct(ContainerInterface $container, $ip) {
        if(strpos($ip, '192.168.0') !== false || strpos($ip, '127.0.0') !== false){
            //$ip = '201.26.109.172'; // IP de teste para o caso de o acesso ser via localhost
            $ip = '201.43.205.208'; // IP de teste para o caso de o acesso ser via localhost
        }
        if($container->hasParameter('reurbano.quova.apikey') && $container->hasParameter('reurbano.quova.secret')){
            $apikey = $container->getParameter('reurbano.quova.apikey');
            $secret = $container->getParameter('reurbano.quova.secret');
            $ch = curl_init();
            $ver = 'v1/';
            $method = 'ipinfo/';
            // O 3600 representa uma hora a menos por causa da porra do horário de verão
            $timestamp = gmdate('U'); // 1200603038
            // echo $timestamp;   
            $sig = md5($apikey . $secret . $timestamp);
            //$service = 'http://api.quova.com/'; // Chave em desenvolvimento
            $service = 'http://api.quova.com/geodirectory/'; // Chave em produção
            $url = $service . $ver. $method. $ip . '?apikey=' . $apikey . '&sig='.$sig . '&format=xml';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $headers = curl_getinfo($ch);
            // close curl
            curl_close($ch);
            // return XML data
            if ($headers['http_code'] != '200') {
                $container->get('logger')->err('Erro ao obter dados do QUOVA para o IP '.$ip.' - '.$url);
            } else {
                $xml = simplexml_load_string($data);
                $this->setIP($xml->ip_address);
                $this->setCity($xml->Location->CityData->city);
                $this->setCountry($xml->Location->CountryData->country);
                $this->setCoordinates(array($xml->Location->latitude, $xml->Location->longitude));
                //exit($xml->Location->latitude);
                return $this;
                //exit(print_r($xml));
            }
        }
        // Este serviço meia-boca só rola se não conseguir a cidade via QUOVA
        $url = "http://api.hostip.info/?ip=".$ip;
        $xml = simplexml_load_file($url);
        $this->setIP($xml
            ->children('gml', TRUE)->featureMember
            ->children('', TRUE)->Hostip->ip);

        $city = $xml->children('gml', TRUE)->featureMember
                    ->children('', TRUE)->Hostip
                    ->children('gml', TRUE)->name;
        $city = explode(',', $city);
        $country = $xml->children('gml', TRUE)->featureMember
                    ->children('', TRUE)->Hostip->countryName;
        $coordinates = @$xml
                        ->children('gml', TRUE)->featureMember
                        ->children('', TRUE)->Hostip->ipLocation
                        ->children('gml', TRUE)->pointProperty->Point->coordinates;

        $this->setCity($city[0]);
        $this->setCountry($country);
        if(!empty ($coordinates)){
            $this->setCoordinates(explode(',', $coordinates));
        }
        //echo $city.' - '.$country.' - '.$coordinates;

        //echo $xml->{'gml:description'};
        //exit();
        //exit(var_dump($xml));
        return $this;
    }
    private function setIP($ip){
        $this->ip = $ip;
    }
    private function setCity($city){
        $this->city = $city;
    }
    private function setCountry($country){
        $this->country = $country;
    }
    private function setCoordinates(array $coordinates){
        $this->coordinates['lati'] = $coordinates[0];
        $this->coordinates['long'] = $coordinates[1];
    }
    public function getIP(){
        return $this->ip;
    }
    public function getCity(){
        return $this->city;
    }
    public function getCountry(){
        return $this->country;
    }
    public function getCoordinates(){
        return $this->coordinates;
    }
    
}