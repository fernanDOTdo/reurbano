<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\UserBundle\Form\Frontend\UserForm;
use Reurbano\UserBundle\Form\Frontend\UserFormEdit;
use Reurbano\UserBundle\Form\ForgetForm;
use Reurbano\UserBundle\Form\Frontend\ReenviarForm;
use Reurbano\UserBundle\Form\Frontend\ChangePassForm;
use Reurbano\UserBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends BaseController {

    /**
     * @Route("/script.js", name="user_user_script")
     */
    public function scriptAction() {

        $script = '
            var ajaxPath = "' . $this->generateUrl('user_user_check', array(), true) . '";
            var ajaxPath2 = "' . $this->generateUrl('user_user_check2', array(), true) . '";
            var emailExiste = "' . $this->get('translator')->trans('O email digitado já existe, favor inserir outro.') . '";
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
                    $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
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
     * @Route("/check2", name="user_user_check2")
     */
    public function check2Action() {
        if ($this->get('request')->isXmlHttpRequest()) {
            if ($this->get('request')->getMethod() == 'POST') {
                $email = $this->get('request')->request->get('email');
                $id = $this->get('request')->request->get('id');
                if (!empty($email)) {
                    $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                            ->field('email')->equals($email)
                            ->field('id')->notEqual($id)
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
     * @Route("/editar/{username}", name="user_user_editar")
     * @Template()
     */
    public function novoAction($username = false) {
        $userLogado = $this->get('security.context')->getToken()->getUser();
        if ($username) {
            $rep = $this->mongo('ReurbanoUserBundle:User');
            $query = $rep->findByField('username', $username);
            if (count($query) == 1) {
                if ($this->get('security.context')->isGranted('ROLE_ADMIN') || ($query->getId() == $userLogado->getId())) {
                    $titulo = $this->trans("Edição do usuário %name%", array("%name%" => $query->getName()));
                    $form = $this->createForm(new UserFormEdit(), $query);
                    return $this->render('ReurbanoUserBundle:Frontend/User:novo.html.twig', array(
                        'form' => $form->createView(), 'titulo' => $titulo,
                        'usuario' => $query
                    ));
                } else {
                    $msg = $this->trans('Você não tem permissão para editar o usuário.');
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('_home'));
                }
            } else {
                $msg = $this->trans('Não existe o usuário %username%', array("%username%" => $username));
                $this->get('session')->setFlash('error', $msg);
                return $this->redirect($this->generateUrl('_home'));
            }
        } else {
            $factory = $this->get('form.factory');
            $titulo = $this->trans("Novo usuário");
            $form = $factory->create(new UserForm());
            return $this->render('ReurbanoUserBundle:Frontend/User:novo.html.twig', array(
                'form' => $form->createView(), 'titulo' => $titulo,
                'usuario' => null
            ));
        }
    }

    /**
     * @Route("/ativar/{actkey}", name="user_user_ativar")
     * @Template()
     */
    public function ativarAction($actkey) {
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $usuario = $repository->findByActkey($actkey);
        if (!empty($usuario)) {
            $repository->activeUser($usuario->getId());
            $msg = $this->trans('O <b>%email%</b> foi confirmado como um usuário. Agora você pode fazer login em nosso site.', array("%email%" => $usuario->getEmail()));
            $this->get('session')->setFlash('ok', $msg);
            //notificação de novo usuario
            $emailsNotify = str_replace(",", ";", $this->get('mastop')->param('user.all.mailnotify'));
            if ($emailsNotify != "") {
                $emailsNotify = explode(";", $emailsNotify);
                foreach ($emailsNotify as $email) {
                    $this->newUserEmail(str_replace(" ", '', $email), $usuario);
                }
            }
            // /notificação de novo usuario
            //autologin
            $usuario->setLastLogin(new \DateTime());
            $this->dm()->persist($usuario);
            $this->dm()->flush();
            $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
            $this->container->get('security.context')->setToken($token);
            // /autologin
            return $this->redirect($this->generateUrl('_home'));
        } else {
            $msg = $this->trans('Nenhum usuário encontrado com a chave de ativação fornecida.');
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
        $message = \Swift_Message::newInstance()
                ->setSubject($this->trans('Confirmação de cadastro no site'))
                ->setFrom($this->get('mastop')->param('system.site.adminmail'))
                ->setTo($email)
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailUserConfirmation.html.twig', array('name' => $nome, 'linkAct' => $this->generateUrl('user_user_ativar', array('actkey' => $actkey), true))), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * Envia um email para o email do $user com o link de troca de senha)
     *  @param objeto $user
     */
    private function emailTrocaSenha($user) {
        if ($user->getActkey() == '') {
            $user->setActkey(uniqid(null, true));
            $this->dm()->persist($user);
            $this->dm()->flush();
        }
        $message = \Swift_Message::newInstance()
                ->setSubject($this->trans('Solicitação de troca de senha'))
                ->setFrom($this->get('mastop')->param('system.site.adminmail'))
                ->setTo($user->getEmail())
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailUserNewPass.html.twig', array('usuario' => $user)), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * Envia um email para o $newEmail com o link de confirmação para troca de email)
     *  @param objeto $user
     *  @param string $newEmail
     */
    private function emailTrocaEmail($user, $newEmail) {
        $message = \Swift_Message::newInstance()
                ->setSubject($this->trans('Confirmação de troca de email'))
                ->setFrom($this->get('mastop')->param('system.site.adminmail'))
                ->setTo($newEmail)
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailUserNewEmail.html.twig', array('usuario' => $user, 'newEmail' => $newEmail, 'ip' => $_SERVER['REMOTE_ADDR'])), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * Envia um email para o email do $user com o o aviso que a senha foi trocada)
     *  @param objeto $user
     */
    private function emailNewPass($user) {
        $message = \Swift_Message::newInstance()
                ->setSubject($this->trans('Sua senha foi trocada em nosso site'))
                ->setFrom($this->get('mastop')->param('system.site.adminmail'))
                ->setTo($user->getEmail())
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailUserNewPassOk.html.twig', array('usuario' => $user, 'ip' => $_SERVER['REMOTE_ADDR'])), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * Envia um email para o $email avisando de novo usuario no site
     * @param string $email
     * @param objeto $user
     */
    private function newUserEmail($email, $user) {
        $userStatus = $user->getStatus();
        $message = \Swift_Message::newInstance()
                ->setSubject($this->trans($userStatus == 4 ? "Novo usuário no site aguardando aprovação" : 'Novo usuário no site'))
                ->setFrom($this->get('mastop')->param('system.site.adminmail'))
                ->setTo($email)
                ->setBody($this->renderView('ReurbanoUserBundle:Frontend/User:emailNewUserNotify.html.twig', array('usuario' => $user)), 'text/html');
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * @Route("/reenviarOk", name="user_user_reenviarOk")
     * @Template()
     */
    public function reenviarOkAction() {
        $form = $this->get('form.factory')->create(new ReenviarForm());
        $dadosPost = $this->get('request')->request->get($form->getName());
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('email', $dadosPost['email']);
        if (!empty($usuario)) {
            if ($usuario->getActkey() != '' && $usuario->getStatus() != 1 && $usuario->getMailOk() == false) {
                $this->emailActKey($usuario->getEmail(), $usuario->getName(), $usuario->getActkey());
                $msg = $this->trans('Foi enviado um email para <b>%email%</b> com o código de ativação de sua conta.', array("%email%" => $dadosPost['email']));
                $this->get('session')->setFlash('ok', $msg);
            } else {
                $msg = $this->trans('O usuário <b>%email%</b> já está ativo em nosso site, se não conseguir fazer login tente recuperar a senha.', array("%email%" => $dadosPost['email']));
                $this->get('session')->setFlash('ok', $msg);
            }
            return $this->redirect($this->generateUrl('_home'));
        } else {
            $msg = $this->trans('E email <b>%email%</b> não está cadastrado em nosso sistema', array("%email%" => $dadosPost['email']));
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    public function verificaStatus($user) {
        $status = $user->getStatus();
        if ($status == 1) {
            return true;
        } elseif ($status == 0) {
            $msg = $this->trans('O usuário <b>%username%</b> não foi confirmado. Você precisa primeiro confirmar seu usuário através do email cadastrado.', array("%username%" => $user->getUsername()));
            $this->get('session')->setFlash('error', $msg);
        } else {
            $msg = $this->trans('O usuário <b>%username%</b> está bloqueado.', array("%username%" => $user->getUsername()));
            $this->get('session')->setFlash('error', $msg);
        }
        return false;
    }

    /**
     * @Route("/detalhes/{username}", name="user_user_detalhes")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function detalhesAction($username) {
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $itens = $repository->findByUsername($username);
        if (count($itens) > 0) {
            if ($this->verificaStatus($itens)) {
                return $this->render('ReurbanoUserBundle:Frontend/User:detalhes.html.twig', array(
                    'usuario' => $itens));
            } else {
                return $this->redirect($this->generateUrl('_home'));
            }
        } else {
            $msg = $this->trans('O usuário <b>%username%</b> não existe', array("%username%" => $username));
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/salvar/{id}", name="user_user_salvar", defaults={"id" = null})
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
                $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
                //validar se o username inserido não existe ou se é o dele mesmo
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('username')->equals(str_replace(".", "", str_replace("@", "", $dadosPost['email'])))
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('Já existe o usuário <b>%name%</b>. Utilize outro', array("%name%" => $dadosPost['username']));
                }
                // /validar se o username inserido não existe ou se é o dele mesmo
                //validando se o email já não existe
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('email')->equals($dadosPost['email'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('O endereço de email <b>%email%</b> já foi utilizado. Escolha outro email.', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) == 0) {
                    $user->setName($dadosPost['name']);
                    $user->setCpf($dadosPost['cpf']);
                    $user->setEdited(new \DateTime());
                    $this->dm()->persist($user);
                    $this->dm()->flush();
                    $msgAux = "";
                    if ($dadosPost['email'] != $user->getEmail()) {
                        $msgAux = $this->trans('<br />A alteração do email de <b>%emailOld%</b> para <b>%email%</b> somente será realizada após a confirmação do email enviado para seu novo email', array("%emailOld%" => $user->getEmail(), "%email%" => $dadosPost['email']));
                        $this->emailTrocaEmail($user, $dadosPost['email']);
                    }
                    // $user->setEmail($dadosPost['email']);
                    //$user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));

                    $msg = $this->trans('Usuário <b>%name%</b> alterado com sucesso.', array("%name%" => $dadosPost['name']));
                    $this->get('session')->setFlash('ok', $msg . $msgAux);
                    return $this->redirect($this->generateUrl('user_user_detalhes', array('username' => $user->getUsername())));
                } else {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('user_user_detalhes', array('username' => $user->getUsername())));
                }
            } else {
                $modoCadastro = $this->get('mastop')->param('user.all.autoactive');
                //validando captcha
                if (!empty($dadosPost['emailVerify'])) {
                    $erro[] = $this->trans('Não foi possível cadastrar seu usuário, sistema automatizado de inserção detectado.');
                }
                // /validando captcha
                //validando se a senha confere com a repetição
                if ($dadosPost['password'] != $dadosPost['password2']) {
                    $erro[] = $this->trans('A senha digitada não confere com a confirmação da senha.');
                }
                // /validando se a senha confere com a repetição
                //validando se o email já não existe
                $query = $repository->findBy(array('email' => $dadosPost['email']));
                if ($query->count() > 0) {
                    $erro[] = $this->trans('O email <b>%email%</b> já foi utilizado por outro usuário.', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) > 0) {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('user_user_novo'));
                }
                $user = new user();
                $user->setName($dadosPost['name']);
                $user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));

                if ($modoCadastro == 'email') {
                    $user->setActkey($actkey = uniqid());
                    $user->setMailOk(false);
                    $user->setStatus(0);
                } elseif ($modoCadastro == 'auto') {
                    $user->setActkey('');
                    $user->setMailOk(true);
                    $user->setStatus(1);
                } elseif ($modoCadastro == 'admin') {
                    $user->setActkey('');
                    $user->setMailOk(true);
                    $user->setStatus(4);
                }

                $user->setAvatar('');
                $user->setLang('pt_BR');
                $user->setTheme('');
                //$user->setLastLogin(0);
                $user->setCreated(new \DateTime());
                //$user->setEdited(0);
                $user->setRoles('ROLE_USER');
                $user->setCity(0);
                $user->setCpf($dadosPost['cpf']);
                $user->setEmail($dadosPost['email']);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($dadosPost['password'], $user->getSalt()));
                //$user->setBirth(0);
                $user->setGender('');
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setNewsletters($dadosPost['email'] == 1 ? true : false);
                $this->dm()->persist($user);
                $this->dm()->flush();
                if ($modoCadastro == 'email') {
                    //envio de email para confirmar user
                    $this->emailActKey($dadosPost['email'], $dadosPost['name'], $actkey);
                    // /envio de email para confirmar user
                    $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado, favor conferir seu email para habilitar sua conta. Foi enviado um email para <b>%email%</b>', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email']));
                } elseif ($modoCadastro == 'auto') {
                    $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado com sucesso. Seja bem vindo, agora você já pode fazer o seu login.', array("%name%" => $dadosPost['name']));
                    //notificação de novo usuario
                    $emailsNotify = str_replace(",", ";", $this->get('mastop')->param('user.all.mailnotify'));
                    if ($emailsNotify != "") {
                        $emailsNotify = explode(";", $emailsNotify);
                        foreach ($emailsNotify as $email) {
                            $this->newUserEmail(str_replace(" ", '', $email), $user);
                        }
                    }
                    // /notificação de novo usuario
                } elseif ($modoCadastro == 'admin') {
                    $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado, aguarde a aprovação por um de nossos administradores. Assim que for aprovado você receberá um email de confirmação através do email %email%', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email']));
                    //notificação de novo usuario
                    $emailsNotify = str_replace(",", ";", $this->get('mastop')->param('user.all.mailnotify'));
                    if ($emailsNotify != "") {
                        $emailsNotify = explode(";", $emailsNotify);
                        foreach ($emailsNotify as $email) {
                            $this->newUserEmail(str_replace(" ", '', $email), $user);
                        }
                    }
                    // /notificação de novo usuario
                }

                $this->get('session')->setFlash('ok', $msg);
                if ($modoCadastro == 'auto') {
                    return $this->redirect($this->generateUrl('_login'));
                } else {
                    return $this->redirect($this->generateUrl('_home'));
                }
            }
        } else {
            /* print_r($form->getErrors());
              exit(); */
            $this->get('session')->setFlash('error', $this->trans('Erro de validação no cadastro, tente novamente.'));
            $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
            return $this->redirect($this->generateUrl('user_user_editar', array('username' => $user->getUsername())));
        }
    }

    /**
     * @Route("/senha/recupera/{username}", name="user_user_recupera", defaults={"username" = null})
     * @Template()
     */
    public function recuperaAction($username=null) {
        $factory = $this->get('form.factory');
        $form = $factory->create(new ForgetForm());
        return $this->render('ReurbanoUserBundle:Frontend/Security:forget.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/recuperaok", name="user_user_recupera_post")
     * @Template()
     */
    public function recuperaokAction() {
        $form = $this->get('form.factory')->create(new ForgetForm());
        $dadosPost = $this->get('request')->request->get($form->getName());
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('email', $dadosPost['email']);
        if (count($usuario) != 1) {
            $this->get('session')->setFlash('error', $this->trans('Email não encontrado no sistema.'));
            return $this->redirect($this->generateUrl('user_user_recupera'));
        } else {
            $this->emailTrocaSenha($usuario);
            $this->get('session')->setFlash('ok', $this->trans('Favor seguir as instruções enviadas para o email %email%.', array('%email%' => $dadosPost['email'])));
            return $this->redirect($this->generateUrl('_home'));
        }
        //ver se o post eh username ou email, ver se tem um email/username com este dado alterar campo recoverid para um uniqkey,
        //enviar email para este usuário com uma url com o uniqkey que ao acessar entra em um action que permite o usuário editar a senha
    }

    /**
     * @Route("/senha/recuperacao/{actkey}", name="user_user_recuperacao")
     * @Template()
     */
    public function recuperacaoAction($actkey) {
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('actkey', $actkey);
        if (count($usuario) == 1) {
            $factory = $this->get('form.factory');
            $form = $factory->create(new ChangePassForm());
            return $this->render('ReurbanoUserBundle:Frontend/User:changepass.html.twig', array(
                'form' => $form->createView(), 'actkey' => $usuario->getActkey(), 'email' => $usuario->getEmail()
            ));
        } else {
            $this->get('session')->setFlash('error', $this->trans('Código de recuperação inválido. Solicite novamente a troca de sua senha.'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/senha/recuperacaook/{actkey}", name="user_user_recuperacaook")
     * @Template()
     */
    public function recuperacaookAction($actkey) {
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('actkey', $actkey);
        if (count($usuario) == 1) {
            $factory = $this->get('form.factory');
            $form = $factory->create(new ChangePassForm());
            $dadosPost = $this->get('request')->request->get($form->getName());
            if ($dadosPost['password'] != $dadosPost['password2']) {
                $this->get('session')->setFlash('error', $this->trans('A senha digitada não confere com a confirmação, tente novamente.'));
                return $this->redirect($this->generateUrl('user_user_recuperacao', array('actkey' => $actkey)));
            } else {
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($usuario);
                $usuario->setPassword($encoder->encodePassword($dadosPost['password'], $usuario->getSalt()));
                $usuario->setActkey('');
                $this->dm()->persist($usuario);
                $this->dm()->flush();
                $this->emailNewPass($usuario);
                $this->get('session')->setFlash('ok', $this->trans('Senha alterada com sucesso.'));
                return $this->redirect($this->generateUrl('_home'));
            }
        } else {
            $this->get('session')->setFlash('error', $this->trans('Código de recuperação inválido. Solicite novamente a troca de sua senha.'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/novoemail/{edited}/{email}", name="user_user_novo_email")
     * @Template()
     */
    public function trocaEmailAction($edited, $email) {
        $email = html_entity_decode($email);
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $data = new \DateTime();
        $data->setTimestamp($edited);

        $usuario = $repository->findByField('edited', $data);
        if (count($usuario) == 1) {
            $verificaEmail = $repository->findByField('email', $email);
            if (count($verificaEmail) > 0) {
                $this->get('session')->setFlash('error', $this->trans('Não foi possível trocar seu email. O email %email% já está em uso.', array("%email%" => $email)));
                return $this->redirect($this->generateUrl('_home'));
            }
            $usuario->setEdited(new \DateTime());
            $usuario->setEmail($email);
            $usuario->setUsername(str_replace(".", "", str_replace("@", "", $email)));
            $this->dm()->persist($usuario);
            $this->dm()->flush();
            $this->get('session')->setFlash('ok', $this->trans('Seu email foi trocado para %email%.', array("%email%" => $email)));
            return $this->redirect($this->generateUrl('user_user_detalhes', array('username' => $usuario->getUsername())));
        } else {
            $this->get('session')->setFlash('error', $this->trans('Não foi possível trocar seu email, repita o processo.'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/facebook", name="user_user_facebook")
     * @Template()
     */
    public function facebookAction() {
        if ($this->get('request')->isXmlHttpRequest()) {
            //precisa ver se o usuario facebook ja nao esta cadastrado, se estiver apenas logar
            //se nao tiver logado entao pegar os dados dele e criar o user automaticamente
            //em ambos os casos tem que logar automaticamente depois disto
            $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
            $request = $this->getRequest();
            $gets = $request->query;

            $usuario = $repository->findByField('facebookid', $gets->get('facebookId'));
            $usuario2 = $repository->findByField('email', $gets->get('email'));
            if (count($usuario) > 0 || count($usuario2) > 0) {
                //então é existe o user
                //verificar se os dados cadastrais estao atualizados
                if (count($usuario) == 1) {
                    //so tem ele
                    $user = $this->dm()->getReference('ReurbanoUserBundle:User', $usuario->getId());
                    $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                    $user->setCity($gets->get('cidade'));
                    $this->dm()->persist($user);
                    $this->dm()->flush();
                    $this->get('session')->setFlash('ok', $this->trans('Olá %name%, login efetuado.', array('%name%' => $usuario->getName())));
                    $result['success'] = true;
                } elseif (count($usuario) > 0) {
                    //eita isto nao deveria acontecer (ter mais de 1 user com mesmo facebookid
                    $msg = $this->trans('Erro ao sincronizar dados com o Facebook. Entre em contato conosco e informe o erro FACEDUPLICITY');
                    $this->get('session')->setFlash('error', $msg);
                    $result['success'] = false;
                }
                if (count($usuario2) == 1) {
                    $fbID = $usuario2->getFacebookid();
                    if (!isset($fbID) || $fbID == $gets->get('facebookId')) {
                        $user = $this->dm()->getReference('ReurbanoUserBundle:User', $usuario2->getId());
                        $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                        $user->setCity($gets->get('cidade'));
                        if (!isset($fbID)) {
                            $user->setFacebookid($gets->get('facebookId'));
                            $this->get('session')->setFlash('ok', $this->trans('Olá %name%, seu facebook foi vinculado a sua conta.', array('%name%' => $usuario2->getName())));
                        } else {
                            $this->get('session')->setFlash('ok', $this->trans('Olá %name%, login efetuado.', array('%name%' => $usuario2->getName())));
                        }

                        $result['success'] = true;
                    } else {
                        $this->get('session')->setFlash('error', $this->trans('Já existe uma conta com estes dados em nosso sistema e ela não pertence ao seu facebook'));
                        $result['success'] = false;
                    }
                } elseif (count($usuario2) > 0) {
                    //eita isto nao deveria acontecer (ter mais de 1 user com mesmo email
                    $msg = $this->trans('Erro ao sincronizar dados com o Facebook. Entre em contato conosco e informe o erro MAILDUPLICITY');
                    $this->get('session')->setFlash('error', $msg);
                    $result['success'] = false;
                }
                //efetuar o login
                $this->authenticateUser($user);
            } else {
                //novo user, salvar ele
                $user = new user();
                $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                $user->setUsername(str_replace(".", "", str_replace("@", "", $gets->get('email'))));
                $user->setActkey('');
                $user->setMailOk(true);
                $user->setStatus(1);
                $user->setAvatar('');
                $user->setLang('pt_BR');
                $user->setTheme('');
                $user->setCreated(new \DateTime());
                $user->setRoles('ROLE_USER');
                $user->setCity($gets->get('cidade'));
                $user->setCpf('');
                $user->setEmail($gets->get('email'));
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                srand((double) microtime() * 1000000);
                $i = 0;
                $pass = '';
                while ($i <= 7) {
                    $num = rand() % 33;
                    $tmp = substr($chars, $num, 1);
                    $pass = $pass . $tmp;
                    $i++;
                }
                $user->setPassword($encoder->encodePassword($pass, $user->getSalt()));
                //$user->setBirth(0);
                $user->setGender(strtoupper(substr($gets->get('gender'), 0, 1)));
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setFacebookid($gets->get('facebookId'));
                $user->setNewsletters(true);
                $this->dm()->persist($user);
                $this->dm()->flush();
                $this->get('session')->setFlash('ok', $this->trans('Cadastro efetuado com seus dados do Facebook.'));
                $result['success'] = true;
                //efetuar o login
                $this->authenticateUser($user);
            }


            return new Response(json_encode($result));
        }
        return new Response($this->get('translator')->trans('_NOTAJAX'));
    }

    /**
     * Logar o usuário depois de facebookear
     *
     * @param Boolean $reAuthenticate
     */
    protected function authenticateUser(UserInterface $user) {
        $providerKey = "mongo";
        $user->setLastLogin(new \DateTime());
        $this->dm()->persist($user);
        $this->dm()->flush();
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }

    /**
     * @Route("/twitter", name="user_user_twitter")
     * @Template()
     */
    function twitterAction() {
        //echo "<script>window.close();";
        //print_r($this->get('request'));
        //echo "</script>";
        $twitter = new \TwitterOAuth('JUTf0s1U3zU8x0yhAWvUYw', 'ID9gSVim3FWwwaGZRgdlOtbUGSTpGR496vAgOoHpE');
        echo "<body></body>";
        exit();
    }

}