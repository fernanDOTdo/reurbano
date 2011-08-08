<?php
namespace Reurbano\CoreBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller que cuidará de setar a cidade escolhida na session do usuário.
 */

class CityController extends BaseController
{
    /**
     * @Route("/{name}", name="core_city_index")
     * @Template()
     */
    public function indexAction($name)
    {
        $cidade = $this->mongo('ReurbanoCoreBundle:City')->findBySlug($name);
        if(!$cidade){
            $this->get('session')->setFlash('error', 'Cidade não encontrada');
            return $this->redirect($this->generateUrl('_home'));
        }
        $this->get('session')->set('reurbano.user.city', $name);
        $this->get('session')->set('reurbano.user.cityName', $cidade->getName());
        return array('name' => $name);
    }
}
