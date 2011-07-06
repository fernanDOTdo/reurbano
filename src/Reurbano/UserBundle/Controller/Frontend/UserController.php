<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\UserBundle\Form\Frontend\UserForm;
use Reurbano\UserBundle\Form\ForgetForm;
use Reurbano\UserBundle\Form\Frontend\ReenviarForm;
use Reurbano\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

    /**
     * @Route("/script.js", name="user_user_script")
     */
    public function scriptAction() {

        $script = '
            var ajaxPath = "' . $this->generateUrl('user_user_check') . '";
            var emailExiste = "' . $this->get('translator')->trans('user.user.novo.frontend.emailexists') . '";
            ';
        return new Response($script);
    }

    /**
     * @Route("/check", name="user_user_check")
     */
    public function checkAction() {
        if ($this->get('request')->isXmlHttpRequest()) {
            if ($this->get('request')->getMethod() == 'POST') {
                $email = $this->get('request')->request->get('email');
                if (!empty($email)) {
                    $dm = $this->get('doctrine.odm.mongodb.document_manager');
                    $result = $dm->createQueryBuilder('ReurbanoUserBundle:user')
                            ->field('email')->equals($email)
                            ->getQuery()
                            ->execute();
                    if (($result->count() == 0)) {
                        return new Response('1');
                    } else {
                        return new Response('0');
                    }
                }
            }
        }
        return new Response($this->get('translator')->trans('_NOTAJAX'));
    }

    /**
     * @Route("/novo", name="user_user_novo")
     * @Template()
     */
    public function novoAction() {

        $factory = $this->get('form.factory');
        $form = $factory->create(new UserForm());
        return $this->render('ReurbanoUserBundle:Frontend/User:novo.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/ativar/{actkey}", name="user_user_ativar")
     * @Template()
     */
    public function ativarAction($actkey) {
        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $trad = $this->get('translator');
        $usuario = $repository->findByActkey($actkey);
        if (!empty($usuario)) {
            $repository->activeUser($usuario->getId());
            $msg = $trad->trans('user.user.ativar.frontend.actkeyok%email%', array("%email%" => $usuario->getEmail()));
            $this->get('session')->setFlash('ok', $msg);
            return $this->redirect($this->generateUrl('_login'));
        } else {
            $msg = $trad->trans('user.user.ativar.frontend.actkey404');
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/reenviar", name="user_user_reenviar")
     * @Template()
     */
    public function reenviarAction() {
        $factory = $this->get('form.factory');
        $form = $factory->create(new ReenviarForm());
        return $this->render('ReurbanoUserBundle:Frontend/User:reenviar.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Envia um email para o $email com o codigo de ativação ($actkey)
     * @param string $email
     * @param string $nome
     * @param string $actkey 
     */
    private function emailActKey($email, $nome, $actkey) {
        $trad = $this->get('translator');
        $message = \Swift_Message::newInstance()
                ->setSubject($trad->trans('user.user.novo.frontend.emailConfirmTitle'))
                ->setFrom('fabio@mastop.com.br')
                ->setTo($email)
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailUserConfirmation.html.twig', array('name' => $nome, 'linkAct' => $this->generateUrl('user_user_ativar', array('actkey' => $actkey), true))), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * @Route("/reenviarOk", name="user_user_reenviarOk")
     * @Template()
     */
    public function reenviarOkAction() {
        $trad = $this->get('translator');
        $form = $this->get('form.factory')->create(new ReenviarForm());
        $dadosPost = $this->get('request')->request->get($form->getName());
        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('email', $dadosPost['email']);
        if (!empty($usuario)) {
            if ($usuario->getActkey() != '' && $usuario->getStatus() != 1 && $usuario->getMailOk() == false) {
                $this->emailActKey($usuario->getEmail(), $usuario->getName(), $usuario->getActkey());
                $msg = $trad->trans('user.user.reenviar.frontend.emailOk%email%', array("%email%" => $dadosPost['email']));
                $this->get('session')->setFlash('ok', $msg);
            } else {
                $msg = $trad->trans('user.user.reenviar.frontend.userAtived%email%', array("%email%" => $dadosPost['email']));
                $this->get('session')->setFlash('ok', $msg);
            }
            return $this->redirect($this->generateUrl('_home'));
        } else {
            $msg = $trad->trans('user.user.reenviar.frontend.email404%email%', array("%email%" => $dadosPost['email']));
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/detalhes/{username}", name="user_user_view")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function detalhesAction($username) {
        $trad = $this->get('translator');
        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $itens = $repository->findByUsername($username);
        if (count($itens) > 0) {
            return $this->render('ReurbanoUserBundle:Frontend/User:user.html.twig', array(
                'usuario' => $itens));
        } else {
            $msg = $trad->trans('user.user.view.frontend.erro404%username%', array("%username%" => $username));
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/salvar", name="user_user_salvar")
     * @Template()
     */
    public function salvarAction() {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        $form = $factory->create(new UserForm());
        $dadosPost = $request->request->get($form->getName());

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $trad = $this->get('translator');
        $erro = array();
        $form->bindRequest($request);
        if ($form->isValid()) {
            if (isset($dadosPost['id'])) {
                //validar se o username inserido não existe ou se é o dele mesmo
                $result = $dm->createQueryBuilder('ReurbanoUserBundle:user')
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
                $result = $dm->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('email')->equals($dadosPost['email'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $trad->trans('UserForm.EmailExists%email%', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) == 0) {
                    $user = $dm->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
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
                    //$dm->persist($user);
                    $dm->flush();
                    $msg = $trad->trans('UserForm.UserEdited%name%', array("%name%" => $dadosPost['name'] . " (" . $dadosPost['username'] . ")"));
                    $this->get('session')->setFlash('ok', $msg);
                } else {
                    //return array("erro" => $erro, 'sucesso' => false);
                }
            } else {
                //validando captcha
                if (!empty($dadosPost['emailVerify'])) {
                    $erro[] = $trad->trans('user.user.novo.frontend.valid.captcha');
                }
                // /validando captcha
                //validando se a senha confere com a repetição
                if ($dadosPost['password']['Password'] != $dadosPost['password']['Password2']) {
                    $erro[] = $trad->trans('user.user.novo.frontend.valid.pass');
                }
                // /validando se a senha confere com a repetição
                //validando se o email já não existe
                $query = $repository->findBy(array('email' => $dadosPost['email']));
                if ($query->count() > 0) {
                    $erro[] = $trad->trans('user.user.novo.frontend.valid.email%email%', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) > 0) {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('ok', $msg);
                    return $this->redirect($this->generateUrl('user_user_novo'));
                }
                $user = new user();
                $user->setName($dadosPost['name']);
                $user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));
                $user->setActkey($actkey = uniqid());
                $user->setMailOk(false);
                $user->setStatus(0);
                $user->setAvatar('');
                $user->setLang('pt_BR');
                $user->setTheme('');
                $user->setLastLogin(0);
                $user->setCreated(new \DateTime());
                $user->setEdited(0);
                $user->setRoles('ROLE_USER');
                $user->setCity(0);
                $user->setCpf($dadosPost['cpf']);
                $user->setEmail($dadosPost['email']);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($dadosPost['password']['Password'], $user->getSalt()));
                $user->setBirth(0);
                $user->setGender('');
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setNewsletters($dadosPost['email'] == 1 ? true : false);
                $dm = $this->get('doctrine.odm.mongodb.document_manager');
                $dm->persist($user);
                $dm->flush();
                //envio de email para confirmar user
                $this->emailActKey($dadosPost['email'], $dadosPost['name'], $actkey);
                // /envio de email para confirmar user
                $msg = $trad->trans('user.user.novo.frontend.new%name%confirm%email%', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email']));
                $this->get('session')->setFlash('ok', $msg);
                return $this->redirect($this->generateUrl('_home'));
            }
        } else {
            $erro[] = $trad->trans('erro');
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/editar/{username}", name="user_user_editar")
     * @Template()
     */
    public function editarAction($username) {
        $factory = $this->get('form.factory');
        $trad = $this->get('translator');
        $repository = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ReurbanoUserBundle:User');
        $query = $repository->findByUsername($username);
        $form = $factory->create(new UserForm(), $query);
        if (count($query) > 0) {
            return $this->render('ReurbanoUserBundle:Frontend/User:editar.html.twig', array(
                'form' => $form->createView(),
                'id' => $username, 'nome' => $query->getName()
            ));
        } else {
            $msg = $trad->trans('user.user.view.frontend.erro404%username%', array("%username%" => $username));
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/usuario/recupera", name="_user_forget")
     * @Template()
     */
    public function recuperaAction() {

        $factory = $this->get('form.factory');
        $form = $factory->create(new ForgetForm());
        return $this->render('ReurbanoUserBundle:Frontend/Security:forget.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/usuario/recuperaok", name="_user_forget_post")
     * @Template()
     */
    public function recuperaokAction() {

        //ver se o post eh username ou email, ver se tem um email/username com este dado alterar campo recoverid para um uniqkey,
        //enviar email para este usuário com uma url com o uniqkey que ao acessar entra em um action que permite o usuário editar a senha
    }

}