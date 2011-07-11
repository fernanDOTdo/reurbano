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

class UserController extends BaseController {

    /**
     * @Route("/", name="admin_user_user_index")
     * @Template()
     */
    public function indexAction() {
        $rep = $this->get('doctrine.odm.mongodb.document_manager');
        $dados = $rep->getRepository("ReurbanoUserBundle:User")->findAll();
        $itens = array();
        foreach ($dados as $dt) {
            $itens[] = array("id" => $dt->getId(), "nome" => $dt->getName(), "email" => $dt->getEmail(), "createdAt" => $dt->getCreated(), 'username' => $dt->getUsername(), 'status' => ($dt->getStatus() ? $this->trans('_ATIVO') : $this->trans('_DESATIVADO')));
        }
        return $this->render('ReurbanoUserBundle:Backend/User:index.html.twig', array(
            'usuarios' => $dados));
    }

    /**
     * @Route("/novo", name="admin_user_user_novo")
     * @Route("/editar/{username}", name="admin_user_user_editar")
     * @Route("/senha/{username}", name="admin_user_user_senha")
     * @Template()
     */
    public function novoAction($username = false) {
        if ($username) {

            $rep = $this->mongo('ReurbanoUserBundle:User');
            $query = $rep->findByField('username', $username);
            $titulo = $this->trans("Edição do usuário %name%", array("%name%" => $query->getName()));
            $form = $this->createForm(new UserFormEdit(), $query);
            return $this->render('ReurbanoUserBundle:Backend/User:novo.html.twig', array(
                'form' => $form->createView(), 'titulo' => $titulo,
                'usuario' => $query
            ));
        } else {
            $factory = $this->get('form.factory');
            $titulo = $this->trans("Novo usuário");
            $form = $factory->create(new UserForm());
            return $this->render('ReurbanoUserBundle:Backend/User:novo.html.twig', array(
                'form' => $form->createView(), 'titulo' => $titulo,
                'usuario' => null
            ));
        }
    }

    /**
     * @Route("/senha/{username}", name="admin_user_user_senha")
     * @Template()
     */
    public function senhaAction($username) {

        $rep = $this->mongo('ReurbanoUserBundle:User');
        $query = $rep->findByField('username', $username);
        $titulo = $this->trans("Edição do usuário %name%", array("%name%" => $query->getName()));
        $form = $this->createForm(new ChangePass(), $query);
        return $this->render('ReurbanoUserBundle:Backend/User:senha.html.twig', array(
            'form' => $form->createView(), 'usuario' => $query
        ));
    }

    /**
     * @Route("/salvarsenha/", name="admin_user_user_salvar_senha")
     * @Template()
     */
    public function salvarSenhaAction($id=null) {
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
     * @Route("/salvar/{id}", name="admin_user_user_salvar", defaults={"id" = null})
     * @Template()
     */
    public function salvarAction($id=null) {
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
                    return $this->redirect($this->generateUrl('admin_user_user_salvar', array('username' => $usuario->getUsername())));
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
                    return $this->redirect($this->generateUrl('admin_user_user_novo'));
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

                $msg = $this->trans('O usuário <b>%name%</b>foi cadastrado. Dados do usuário: <b>%email%</b> e senha: <b>%pass%</b>', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email'], "%pass%" => $dadosPost['password']));

                $this->get('session')->setFlash('ok', $msg);
                return $this->redirect($this->generateUrl('admin_user_user_index'));
            }
        } else {
            $this->get('session')->setFlash('error', $this->trans('Erro de validação no cadastro, tente novamente.'));
            return $this->redirect($this->generateUrl('admin_user_user_index'));
        }
    }

    /**
     * @Route("/deletar/{username}", name="admin_user_user_deletar")
     * @Template()
     */
    public function deletarAction($username) {
        $usuario = $this->mongo('ReurbanoUserBundle:User')->findByField('username', $username);

        return array('usuario' => $usuario);
    }

    /**
     * @Route("/deletarOk/{username}", name="admin_user_user_deletarok")
     * @Template()
     */
    public function deletarOkAction($username) {
        $usuario = $this->mongo('ReurbanoUserBundle:User')->findByField('username', $username);
        $nome = $usuario->getName();
        $uname = $usuario->getUsername();
        $this->dm()->remove($usuario);
        $this->dm()->flush();
        $msg = $this->trans('O usuário %name% foi removido com sucesso.', array("%name%" => $nome . " ($uname)"));
        $this->get('session')->setFlash('ok', $msg);
        return $this->redirect($this->generateUrl('admin_user_user_index'));
    }

}