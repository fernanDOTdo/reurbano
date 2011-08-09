<?php

namespace Reurbano\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Reurbano\CoreBundle\Util\IPtoCity;
use Symfony\Component\HttpFoundation\Request;

class KernelRequestListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if(HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($session = $event->getRequest()->getSession()) {
                if(!$session->has('reurbano.user.city')) {
                    $ip2city = new IPtoCity($this->container, $_SERVER['REMOTE_ADDR']);
                    $cidade = (string)$this->container->get('mastop')->slugify($ip2city->getCity());
                    $session->set('reurbano.user.ip', (string)$ip2city->getIP());
                    $cidadeDB = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoCoreBundle:City')->findBySlug($cidade);
                    if($cidadeDB){ // Cidade atual está no DB
                        $session->set('reurbano.user.city', $cidade);
                        $session->set('reurbano.user.cityName', $cidadeDB->getName());
                    }else{
                        $cidadeDB = $this->container->get('mastop')->getDocumentManager()->getRepository('ReurbanoCoreBundle:City')->findBySlug($this->container->getParameter('reurbano.default_city'));
                        if($cidadeDB){
                            $session->set('reurbano.user.city', $this->container->getParameter('reurbano.default_city')); // Cidade atual não está no DB, então seta a cidade padrão como atual
                            $session->set('reurbano.user.cityName', $cidadeDB->getName());
                        }else{ // Cidade padrão não foi encontrada no DB (isso é ruim)
                            $session->set('reurbano.user.city', $cidade);
                            $session->set('reurbano.user.cityName', $ip2city->getCity());
                        }
                    }
                    $session->set('reurbano.user.country', (string)$ip2city->getCountry());
                    $coords = $ip2city->getCoordinates();
                    if(isset ($coords['lati']) && isset ($coords['long'])){
                        $session->set('reurbano.user.lati', (string)$coords['lati']);
                        $session->set('reurbano.user.long', (string)$coords['long']);
                    }
                }
            } else {
                // sessionless request, use explicit requested locale
                $locale = $this->container->get('request')->request->get('locale', 'pt_BR');
                $this->container->get('translator')->setLocale($locale);
            }
        }
    }
}
