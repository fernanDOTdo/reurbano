<?php

namespace Reurbano\UserBundle\Controller\Backend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\UserBundle\Form\Backend\UserForm;
use Reurbano\UserBundle\Form\ForgetForm;
use Reurbano\UserBundle\Document\User;

class UserController extends BaseController {

    /**
     * @Route("/", name="admin_user_user_index")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $rep = $this->get('doctrine.odm.mongodb.document_manager');
        $dados = $rep->getRepository("ReurbanoUserBundle:User")->findAll();
        $trad = $this->get('translator');
        $itens = array();
        foreach ($dados as $dt) {
            $dateRet = $trad->trans('_NAO_DEFINIDO');
            if ($dt->getCreated()) {
                $dateRet = $dt->getCreated();
            }
            $itens[] = array("id" => $dt->getId(), "nome" => $dt->getName(), "email" => $dt->getEmail(), "createdAt" => $dateRet, 'uname' => $dt->getUsername(), 'status' => ($dt->getStatus() ? $trad->trans('_ATIVO') : $trad->trans('_DESATIVADO')));
        }
        return $this->render('ReurbanoUserBundle:Backend/User:index.html.twig', array(
            'usuarios' => $dados));
    }

    /**
     * @Route("/usuario/novo", name="admin_user_user_novo")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function novoAction() {
        $factory = $this->get('form.factory');
        $form = $factory->create(new UserForm());
        return $this->render('ReurbanoUserBundle:Backend/User:novo.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/usuario/salvar", name="_salvar_user")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function salvarAction() {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        $form = $factory->create(new UserForm());
        $dadosPost = $request->request->get($form->getName());

        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $trad = $this->get('translator');
        $erro = array();

        if (isset($dadosPost['id'])) {
            //validar se o username inserido não existe ou se é o dele mesmo
            $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                    ->field('username')->equals($dadosPost['username'])
                    ->field('id')->notEqual($dadosPost['id'])
                    ->getQuery()
                    ->execute();
            if (($result->count() > 0)) {
                $erro[] = $trad->trans('UserForm.UserExists%name%', array("%name%" => $dadosPost['username']));
            }
            // /validar se o username inserido não existe ou se é o dele mesmo
            //validando se a senha confere com a repetição
            if ($dadosPost['password']['password'] != $dadosPost['password']['password2']) {
                $erro[] = $trad->trans('UserForm.PassDiff');
            }
            // /validando se a senha confere com a repetição
            //validando se o email já não existe
            $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                    ->field('email')->equals($dadosPost['email'])
                    ->field('id')->notEqual($dadosPost['id'])
                    ->getQuery()
                    ->execute();
            if (($result->count() > 0)) {
                $erro[] = $trad->trans('UserForm.EmailExists%email%', array('%email%' => $dadosPost['email']));
            }
            // /validando se o email já não existe
            if (count($erro) == 0) {
                $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
                $user->setId($dadosPost['id']);
                $user->setName($dadosPost['name']);
                $user->setEmail($dadosPost['email']);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setUsername($dadosPost['username']);
                $user->setPassword($encoder->encodePassword($dadosPost['password']['password'], $user->getSalt()));
                //exit($user->getSalt().' => '.$dadosPost['password']['password'].' => '.$encoder->encodePassword($dadosPost['password']['password'], $user->getSalt()).' => '.$encoder->encodePassword($dadosPost['password']['password'], ''));
                $user->setGroup($dadosPost['group']);
                $user->setAvatar('');
                $user->setStatus($dadosPost['status']);
                $this->dm()->flush();
                $msg = $trad->trans('UserForm.UserEdited%name%', array("%name%" => $dadosPost['name'] . " (" . $dadosPost['username'] . ")"));
                $this->get('session')->setFlash('ok', $msg);
            } else {
                return array("erro" => $erro, 'sucesso' => false);
            }
        } else {

            // validando se já não existe username
            $query = $repository->findBy(array('username' => $dadosPost['username']));

            if ($query->count() > 0) {
                $erro[] = $trad->trans('UserForm.UserExists%name%', array("%name%" => $dadosPost['username']));
            }
            // /validando se já não existe username
            //validando se a senha confere com a repetição
            if ($dadosPost['password']['password'] != $dadosPost['password']['password2']) {
                $erro[] = $trad->trans('UserForm.PassDiff');
            }
            // /validando se a senha confere com a repetição
            //validando se o email já não existe
            $query = $repository->findBy(array('email' => $dadosPost['email']));
            if ($query->count() > 0) {
                $erro[] = $trad->trans('UserForm.EmailExists%email%', array('%email%' => $dadosPost['email']));
            }
            // /validando se o email já não existe
            if (count($erro) > 0) {
                return array("erro" => $erro, 'sucesso' => false);
            }
            $user = new user();
            $user->setName($dadosPost['name']);
            $user->setEmail($dadosPost['email']);
            $user->setUsername($dadosPost['username']);
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($dadosPost['password']['password'], $user->getSalt()));
            $user->setGroup($dadosPost['group']);
            $user->setAvatar('');
            $user->setStatus($dadosPost['status']);
            $user->setCreatedAt(new \DateTime());

            $this->dm()->persist($user);
            $this->dm()->flush();
            $msg = $trad->trans('UserForm.UserInserted%name%', array("%name%" => $dadosPost['name'] . " (" . $dadosPost['username'] . ")"));
            $this->get('session')->setFlash('ok', $msg);
        }

        return $this->redirect($this->generateUrl('_user'));
    }

    /**
     * @Route("/usuario/editar/{id}", name="_editar_user")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editarAction($id) {
        $factory = $this->get('form.factory');
        $rep = $this->get('doctrine.odm.mongodb.document_manager');
        //$query = $rep->getRepository('ReurbanoUserBundle:user')->findOneById($id);
        $query = $rep->find('ReurbanoUserBundle:User', $id);
        /* print_r($query);
          exit(); */
        $form = $factory->create(new UserForm(), $query);

//print_r($form->getData());
        return $this->render('ReurbanoUserBundle:Backend/User:editar.html.twig', array(
            'form' => $form->createView(),
            'id' => $id, 'nome' => $query->getName()
        ));
    }

    /**
     * @Route("/usuario/deletar/{id}", name="_deletar_user")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function deletarAction($id) {
        $query = $this->dm()->getRepository('ReurbanoUserBundle:user')->findOneById($id);

        return array('id' => $id, 'nome' => $query->getName());
    }

    /**
     * @Route("/usuario/deletarOk/{id}", name="_deletarOk_user")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function deletarOkAction($id) {
        $query = $this->dm()->getRepository('ReurbanoUserBundle:user')->findOneById($id);
        $nome = $query->getName();
        $uname = $query->getUsername();
        $this->dm()->remove($query);
        $this->dm()->flush();
        $trad = $this->get('translator');
        $msg = $trad->trans('UserForm.UserRemoved%name%', array("%name%" => $nome . " ($uname)"));
        $this->get('session')->setFlash('ok', $msg);
        return $this->redirect($this->generateUrl('_user'));
    }

}