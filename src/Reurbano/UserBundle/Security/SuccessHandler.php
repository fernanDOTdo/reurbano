<?php
 
namespace Reurbano\UserBundle\Security;
 
use Symfony\Component\Routing\RouterInterface,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Http\HttpUtils;
 
class SuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $router;
    
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = $request->getSession();
        $session->setFlash('ok', 'Login efetuado com sucesso.');
        $roles = $token->getUser()->getRoles();
        if ($targetUrl = $session->get('_security.target_path')) {
            $session->remove('_security.target_path');
            return new RedirectResponse($targetUrl);
        }elseif(in_array('ROLE_ADMIN', $roles)){
           return new RedirectResponse($this->router->generate('admin_system_home_index')); // Se o user for admin, vai para a Home da Administração
        }else{
           return new RedirectResponse($this->router->generate('_home'));
        }
        //$targetUrl = $request->headers->get('Referer')
    }
}