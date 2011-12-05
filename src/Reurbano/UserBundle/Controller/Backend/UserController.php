<?php

namespace Reurbano\UserBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\UserBundle\Form\Backend\UserForm;
use Reurbano\UserBundle\Form\Backend\UserFormEdit;
use Reurbano\UserBundle\Form\Backend\ChangePass;
use Reurbano\UserBundle\Form\ForgetForm;
use Reurbano\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController {

    /**
     * @Route("/", name="admin_user_user_index")
     * @Template()
     */
    public function indexAction() {
        $rep = $this->get('doctrine.odm.mongodb.document_manager');
        $dados = $rep->getRepository("ReurbanoUserBundle:User")->findAllByCreated();
        $itens = array();
        $titulo = $this->trans("Listagem de usuários");

        return $this->render('ReurbanoUserBundle:Backend/User:index.html.twig', array(
                    'title' => $titulo,
                    'usuarios' => $dados));
    }

    /**
     * @Route("/novo", name="admin_user_user_new")
     * @Route("/editar/{username}", name="admin_user_user_edit")
     * @Template()
     */
    public function newAction($username = false) {
        if ($username) {

            $rep = $this->mongo('ReurbanoUserBundle:User');
            $query = $rep->findByField('username', $username);
            if ($query->superadmin()) {
                if (!$this->hasRole("ROLE_SUPERADMIN")) {
                    $msg = $this->trans('Você não tem permissão para editar este usuário.');
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('_home'));
                }
            }
            $titulo = $this->trans("Edição do usuário %name%", array("%name%" => $query->getName()));
            $form = $this->createForm(new UserFormEdit(), $query);
            return $this->render('ReurbanoUserBundle:Backend/User:novo.html.twig', array(
                        'form' => $form->createView(), 'title' => $titulo,
                        'usuario' => $query
                    ));
        } else {
            $factory = $this->get('form.factory');
            $titulo = $this->trans("Novo usuário");
            $form = $factory->create(new UserForm());
            return $this->render('ReurbanoUserBundle:Backend/User:novo.html.twig', array(
                        'form' => $form->createView(), 'title' => $titulo,
                        'usuario' => null,
                        'current' => 'admin_user_user_index'
                    ));
        }
    }

    /**
     * @Route("/senha/{username}", name="admin_user_user_pass")
     * @Template()
     */
    public function passAction($username) {

        $rep = $this->mongo('ReurbanoUserBundle:User');
        $query = $rep->findByField('username', $username);
        $titulo = $this->trans("Edição de senha do usuário %name%", array("%name%" => $query->getName()));
        $form = $this->createForm(new ChangePass(), $query);
        return $this->render('ReurbanoUserBundle:Backend/User:senha.html.twig', array(
                    'form' => $form->createView(), 'usuario' => $query,
                    'title' => $titulo,
                ));
    }

    /**
     * @Route("/salvarsenha/", name="admin_user_user_savepass")
     * @Template()
     */
    public function savePassAction($id=null) {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        $form = $factory->create(new ChangePass());
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->get($form->getName());
        $erro = array();
        //validando se a senha confere com a repetição
        if ($dadosPost['password'] != $dadosPost['password2']) {
            $erro[] = $this->trans('A senha digitada não confere com a confirmação da senha.');
        }
        // /validando se a senha confere com a repetição
        if (count($erro) == 0) {
            $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
            $user->setId($dadosPost['id']);
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($dadosPost['password'], $user->getSalt()));
            $this->dm()->flush();
            $msg = $this->trans('Senha do usuário <b>%name%</b> alterada com sucesso. Nova senha %pass%', array("%name%" => $user->getName() . " (" . $user->getUsername() . ")", "%pass%" => $dadosPost['password']));
            $this->get('session')->setFlash('ok', $msg);
            return $this->redirect($this->generateUrl('admin_user_user_index'));
        } else {
            $msg = "";
            foreach ($erro as $eItem) {
                $msg.=$eItem . " <br />";
            }
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('admin_user_user_index'));
        }
    }

    /**
     * @Route("/salvar/{id}", name="admin_user_user_save", defaults={"id" = null})
     * @Template()
     */
    public function saveAction($id=null) {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        if ($id) {
            $form = $factory->create(new UserFormEdit());
        } else {
            $form = $factory->create(new UserForm());
        }
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->get($form->getName());

        $erro = array();
        $form->bindRequest($request);
        if ($form->isValid()) {
            if ($id) {
                //edição
                //validar se o username inserido não existe ou se é o dele mesmo
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:User')
                        ->field('username')->equals(str_replace(".", "", str_replace("@", "", $dadosPost['email'])))
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('Já existe o usuário <b>%name%</b>. Utilize outro', array("%name%" => $dadosPost['username']));
                }
                // /validar se o username inserido não existe ou se é o dele mesmo
                //validando se o email já não existe
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:User')
                        ->field('email')->equals($dadosPost['email'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('O endereço de email <b>%email%</b> já foi utilizado. Utilize outro', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) == 0) {
                    $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
                    $user->setId($dadosPost['id']);
                    $user->setName($dadosPost['name']);
                    $user->setEmail($dadosPost['email']);
                    $user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));
                    $user->setRoles($dadosPost['roles']);
                    $user->setStatus($dadosPost['status']);
                    $this->dm()->flush();
                    $msg = $this->trans('Usuário <b>%name%</b> alterado com sucesso.', array("%name%" => $dadosPost['name'] . " (" . $user->getUsername() . ")"));
                    $this->get('session')->setFlash('ok', $msg);
                    return $this->redirect($this->generateUrl('admin_user_user_index'));
                } else {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('error', $msg);
                    $usuario = $repository->findByField('id', $dadosPost['id']);
                    return $this->redirect($this->generateUrl('admin_user_user_save', array('username' => $usuario->getUsername())));
                }
            } else {
                //inserção
                //validando se a senha confere com a repetição
                if ($dadosPost['password'] != $dadosPost['password2']) {
                    $erro[] = $this->trans('A senha digitada não confere com a confirmação da senha.');
                }
                // /validando se a senha confere com a repetição
                //validando se o email já não existe
                $usuario = $repository->findByField('email', $dadosPost['email']);
                if (!empty($usuario)) {
                    $erro[] = $this->trans('O email <b>%email%</b> já foi utilizado por outro usuário.', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) > 0) {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('admin_user_user_new'));
                }
                $user = new user();
                $user->setName($dadosPost['name']);
                $user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));
                $user->setActkey('');
                $user->setMailOk(true);
                $user->setStatus($dadosPost['status']);
                $user->setAvatar('');
                $user->setLang('pt_BR');
                $user->setTheme('');
                $user->setCreated(new \DateTime());
                $user->setRoles($dadosPost['roles']);
                $user->setCity(0);
                $user->setCpf('');
                $user->setEmail($dadosPost['email']);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($dadosPost['password'], $user->getSalt()));
                $user->setGender('');
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setNewsletters(true);
                $this->dm()->persist($user);
                $this->dm()->flush();

                $msg = $this->trans('O usuário <b>%name%</b> foi cadastrado. Dados do usuário: <b>%email%</b> e senha: <b>%pass%</b>', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email'], "%pass%" => $dadosPost['password']));

                $this->get('session')->setFlash('ok', $msg);
                return $this->redirect($this->generateUrl('admin_user_user_index'));
            }
        } else {
            $this->get('session')->setFlash('error', $this->trans('Erro de validação no cadastro, tente novamente.'));
            return $this->redirect($this->generateUrl('admin_user_user_index'));
        }
    }

    /**
     * @Route("/bloquear", name="admin_user_user_block")
     * @Template()
     */
    public function deleteAction() {
        $request = $this->getRequest();
        $username = $request->get('username');
        if ('POST' == $request->getMethod()) {
            $id = $request->get('id');
            $usuario = $this->mongo('ReurbanoUserBundle:User')->findOneById($id);
            if ($usuario->superadmin()) {
                if (!$this->hasRole("ROLE_SUPERADMIN")) {
                    $msg = $this->trans('Você não tem permissão para bloquear este usuário.');
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('_home'));
                }
            }
            $this->mongo('ReurbanoUserBundle:User')->block($usuario);
            $nome = $usuario->getName();
            $uname = $usuario->getUsername();
            $msg = $this->trans('O usuário %name% foi bloqueado com sucesso.', array("%name%" => $nome . " ($uname)"));
            $this->get('session')->setFlash('ok', $msg);
            return $this->redirect($this->generateUrl('admin_user_user_index'));
        } else {
            $usuario = $this->mongo('ReurbanoUserBundle:User')->findOneBy(array('username' => $username));
            return $this->confirm($this->trans('Tem certeza que deseja bloquear o usuário %name%?', array("%name%" => $usuario->getName())), array('id' => $usuario->getId()));
        }
    }

    /**
     * Action para exibir um password criptografado com as atuais configurações
     * 
     * @Route("/testpass", name="admin_user_user_testpass")
     */
    public function testPass() {
        $req = $this->getRequest()->query;
        $pass = $req->get('p');
        $salt = $req->get('s');
        $encoder = $this->get('security.encoder_factory')->getEncoder(new User());
        return new \Symfony\Component\HttpFoundation\Response($encoder->encodePassword($pass, $salt));
    }

    /**
     * Action de view das informações do usuário
     * 
     * @Route("/informacoes/{username}", name="admin_user_user_info")
     * @Template()
     */
    public function infoAction(User $user){
        $title = $this->trans("Informações de ".$user->getName());
        $ofertas = $this->mongo('ReurbanoDealBundle:Deal')->count('user.id', $user->getId());
        $compras = $this->mongo('ReurbanoOrderBundle:Order')->count('user.id', $user->getId());
        $vendas = $this->mongo('ReurbanoOrderBundle:Order')->count('seller.id', $user->getId());
        $saldo = $this->mongo('ReurbanoOrderBundle:Escrow')->totalCheckoutByUser($user->getId());
        return array(
            'title' => $title,
            'user' => $user,
            'ofertas' => $ofertas,
            'compras' => $compras,
            'vendas' => $vendas,
            'saldo' => $saldo,
        );
    }
    
    /**
     * @Route("/export", name="admin_user_user_export")
     */
    public function exportAction()
    {
        $user = $this->mongo('ReurbanoUserBundle:User')->findAllByCreated();
        $data = "Nome,E-mail,Cidade,Data\n";
        foreach($user as $user){
            $data .= $user->getName() .  
                    "," .$user->getEmail() . 
                    "," . $user->getCity()->getName() . 
                    "," . $user->getCreated() . "\n";
        }
        return new Response($data, 200, array(
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename= mailing_' . date('d_m_Y') . '.csv',
        ));
    }
}