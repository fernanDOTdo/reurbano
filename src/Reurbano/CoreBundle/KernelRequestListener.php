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
                    $session->set('reurbano.user.ip', (string)$ip2city->getIP());
                    $session->set('reurbano.user.city', (string)$this->container->get('mastop')->slugify($ip2city->getCity()));
                    $session->set('reurbano.user.country', (string)$ip2city->getCountry());
                    $coords = $ip2city->getCoordinates();
                    $session->set('reurbano.user.lati', (string)$coords['lati']);
                    $session->set('reurbano.user.long', (string)$coords['long']);
                }
            } else {
                // sessionless request, use explicit requested locale
                $locale = $this->container->get('request')->request->get('locale', 'ptBR');
                $this->container->get('translator')->setLocale($locale);
            }
        }
    }
}
