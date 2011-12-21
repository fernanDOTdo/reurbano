<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Reurbano\UserBundle\Form\Frontend\UserForm;
use Reurbano\UserBundle\Form\Frontend\UserFormTwitter;
use Reurbano\UserBundle\Form\Frontend\UserFormEdit;
use Reurbano\UserBundle\Form\ForgetForm;
use Reurbano\UserBundle\Form\Frontend\ReenviarForm;
use Reurbano\UserBundle\Form\Frontend\ChangePassForm;
use Reurbano\UserBundle\Form\Frontend\BankDataType;
use Reurbano\UserBundle\Document\User;
use Reurbano\UserBundle\Document\BankData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Reurbano\CoreBundle\Util\IPtoCity;


class UserController extends BaseController {

    /**
     * @Route("/usuario/scriptjs", name="user_user_script")
     */
    public function scriptAction() {

        $script = '
            var ajaxPath = "' . $this->generateUrl('user_user_check', array(), true) . '";
            var ajaxPath2 = "' . $this->generateUrl('user_user_check2', array(), true) . '";
            var ajaxCPF = "' . $this->generateUrl('user_user_checkCPF', array(), true) . '";
            var emailExiste = "' . $this->get('translator')->trans('O email digitado já existe, favor inserir outro.') . '";
            ';
        return new Response($script);
    }

    /**
     * @Route("/usuario/checkCPF", name="user_user_checkCPF")
     */
    public function checkCPFAction() {
        if ($this->get('request')->isXmlHttpRequest()) {
            if ($this->get('request')->getMethod() == 'POST') {
                $cpf = $this->get('request')->request->get('cpf');
                if (!empty($cpf)) {
                    $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                            ->field('cpf')->equals($cpf)
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
        return new Response($this->get('translator')->trans('Operação não permitida.'));
    }

    /**
     * @Route("/usuario/check", name="user_user_check")
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
        return new Response($this->get('translator')->trans('Operação não permitida.'));
    }

    /**
     * @Route("/usuario/check2", name="user_user_check2")
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
        return new Response($this->get('translator')->trans('Operação não permitida.'));
    }

    /**
     * @Route("/usuario/confirmacao/{username}", name="user_user_confirmation")
     * @Template()
     */
    function confirmationOkAction($username) {
        $rep = $this->mongo('ReurbanoUserBundle:User');
        $usuario = $rep->findOneBy(array('username' => $username));
        $modoCadastro = $this->get('mastop')->param('user.all.autoactive');
        if ($modoCadastro == 'email') {
            $msg = $this->trans('Olá %name%, seu cadastro foi efetuado, favor conferir seu email para habilitar sua conta. Foi enviado um email para %email%', array("%name%" => $usuario->getName(), "%email%" => $usuario->getEmail()));
        } elseif ($modoCadastro == 'auto') {
            $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado com sucesso. Seja bem vindo.', array("%name%" => $usuario->getName()));
        } elseif ($modoCadastro == 'admin') {
            $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado, aguarde a aprovação por um de nossos administradores. Assim que for aprovado você receberá um email de confirmação através do email %email%', array("%name%" => $usuario->getName(), "%email%" => $usuario->getEmail()));
        }
        return $this->redirectFlash($this->generateUrl('_home'), $msg);
    }

    /**
     * @Route("/usuario/novo", name="user_user_new")
     * @Template()
     */
    public function newAction() {
        $userLogado = $this->get('security.context')->getToken()->getUser();
        if(is_object($userLogado)){
            return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Você já está cadastrado e logado como <strong>'.$userLogado->getName().' ('.$userLogado->getEmail().')</strong>.', 'error');
        }
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        if($error){
            $this->get('session')->setFlash('error', $error->getMessage());
        }

        $factory = $this->get('form.factory');
        $titulo = $this->trans("Cadastre-se");
        $form = $factory->create(new UserForm());
        return $this->render('ReurbanoUserBundle:Frontend/User:novo.html.twig', array(
                    'form' => $form->createView(), 'titulo' => $titulo,
                    'usuario' => null,
                    'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
                ));
    }
    /**
     * @Route("/usuario/editar", name="user_user_edit")
     * @Template()
     */
    public function editAction() {
        $userLogado = $this->get('security.context')->getToken()->getUser();
        if(!$this->hasRole('ROLE_USER')){
            return $this->redirectFlash($this->generateUrl('_login'), 'É preciso estar logado para acessar esta página.', 'error');
        }
        $titulo = $this->trans("Edição do usuário %name%", array("%name%" => $userLogado->getName()));
        $form = $this->createForm(new UserFormEdit(), $userLogado);
        return $this->render('ReurbanoUserBundle:Frontend/User:editar.html.twig', array(
                    'form' => $form->createView(), 'titulo' => $titulo,
                    'usuario' => $userLogado,
                ));
    }

    /**
     * @Route("/usuario/ativar/{actkey}", name="user_user_active")
     * @Template()
     */
    public function activeAction($actkey) {
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
            $this->authenticateUser($usuario);
            // /autologin
            return $this->redirect($this->generateUrl('_home'));
        } else {
            $msg = $this->trans('Nenhum usuário encontrado com a chave de ativação fornecida.');
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/usuario/reenviar", name="user_user_resend")
     * @Template()
     */
    public function resendAction() {
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
        $mail = $this->get('mastop.mailer');
        $mail->to($email)
             ->subject('Confirmação de cadastro')
             ->template('usuario_confirmacao', array('title'=>'Ative seu cadastro', 'name' => $nome, 'linkAct' => $this->generateUrl('user_user_active', array('actkey' => $actkey), true)))
             ->send();
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
        $mail = $this->get('mastop.mailer');
        $mail->to($user)
             ->subject('Solicitação de troca de senha')
             ->template('usuario_novasenha', array('user' => $user, 'title' => 'Troca de Senha'))
             ->send();
        $mail->notify('Aviso de solicitação de troca de senha', 'O usuário '.$user->getName().' ('.$user->getEmail().') solicitou troca de senha no site.');
    }

    /**
     * Envia um email para o $newEmail com o link de confirmação para troca de email)
     *  @param objeto $user
     *  @param string $newEmail
     */
    private function emailTrocaEmail($user, $newEmail) {
        $mail = $this->get('mastop.mailer');
        $mail->to($user)
             ->subject('Confirmação de troca de email')
             ->template('usuario_novoemail', array('title'=>'Solicitação de Troca de E-mail', 'user' => $user, 'newEmail' => $newEmail, 'ip' => $_SERVER['REMOTE_ADDR']))
             ->send();
        $mail->notify('Aviso de solicitação de troca de e-mail', 'O usuário '.$user->getName().' ('.$user->getEmail().') solicitou troca de e-mail para '.$newEmail.'.');
    }

    /**
     * Envia um email para o email do $user com o o aviso que a senha foi trocada)
     *  @param objeto $user
     */
    private function emailNewPass($user) {
        $mail = $this->get('mastop.mailer');
        $mail->to($user)
             ->subject('Sua senha foi alterada')
             ->template('usuario_novasenha_ok', array('user' => $user, 'ip' => $_SERVER['REMOTE_ADDR'], 'title' => 'Confirmação de Troca de Senha'))
             ->send();
        $mail->notify('Aviso de confirmação de troca de senha', 'O usuário '.$user->getName().' ('.$user->getEmail().') confirmou a troca de senha no site.');
    }

    /**
     * Envia um email para o $email avisando de novo usuario no site
     * @param string $email
     * @param objeto $user
     */
    private function newUserEmail($email, $user) {
        $userStatus = $user->getStatus();
        $mail = $this->get('mastop.mailer');
        if ($email) { // Envia notificação administrativa de novo usuário
            $mail->to($email)
             ->subject($userStatus == 4 ? "Novo usuário aguardando aprovação" : 'Cadastro de novo usuário')
             ->template('usuario_novo', array('user' => $user, 'title' => 'Novo usuário: '.$user->getName()))
             ->send();
        } elseif($user) { // Envia e-mail de boas vindas para o usuário
            $mail->to($user)
             ->subject('Seja bem vindo')
             ->template('usuario_bemvindo', array('user' => $user, 'title' => 'Bem-vindo, '.$user->getName().'!'))
             ->send();
        }
    }

    /**
     * @Route("/usuario/reenviarOk", name="user_user_resendOk")
     * @Template()
     */
    public function resendOkAction() {
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

    /**
     * Verifica se o usuário esta confirmado ou não
     * @param object $user
     * @return boolean 
     */
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
     * @Route("/usuario/detalhes/{username}", name="user_user_details")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function detailsAction($username) {
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
     * @Route("/usuario/salvar/{id}", name="user_user_save", defaults={"id" = null})
     * @Template()
     */
    public function saveAction($id = null) {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        $cityId = $this->get('session')->get('reurbano.user.cityId');
        if ($id) {
            $form = $factory->create(new UserFormEdit());
        } else {
            $form = $factory->create(new UserForm());
        }
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->get($form->getName());
        $erro = array();
        $form->bindRequest($request);
        $mail = $this->get('mastop.mailer');
        if ($form->isValid()) {
            if ($id) {
                $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
                //validando se o email já não existe
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('email')->equals($dadosPost['email'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('O endereço de email <b>%email%</b> já é utilizado. Escolha outro email.', array('%email%' => $dadosPost['email']));
                }
                // /validando se o email já não existe
                if (count($erro) == 0) {
                    $user->setName($dadosPost['name']);
                    if(isset($dadosPost['cpf'])){
                        $user->setCpf($dadosPost['cpf']);
                    }
                    $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($dadosPost['city']);
                    $user->setCity($city);
                    $user->setGender($dadosPost['gender']);
                    $birth = new \DateTime();
                    if($dadosPost['birth']['year'] != "" && $dadosPost['birth']['month'] != "" && $dadosPost['birth']['day']){
                        $birth->setDate($dadosPost['birth']['year'], $dadosPost['birth']['month'], $dadosPost['birth']['day']);
                        $user->setBirth($birth);
                    }
                    $user->setEdited(new \DateTime());
                    $this->dm()->persist($user);
                    $this->dm()->flush();
                    $msgAux = "";
                    if ($dadosPost['email'] != $user->getEmail()) {
                        $msgAux = $this->trans('<br />A alteração do email de <b>%emailOld%</b> para <b>%email%</b> somente será realizada após a confirmação do email enviado para seu novo email', array("%emailOld%" => $user->getEmail(), "%email%" => $dadosPost['email']));
                        $this->emailTrocaEmail($user, $dadosPost['email']);
                    }
                    $msg = $this->trans('Usuário <b>%name%</b> alterado com sucesso.', array("%name%" => $dadosPost['name']));
                    $this->get('session')->setFlash('ok', $msg . $msgAux);
                    return $this->redirect($this->generateUrl('user_dashboard_index'));
                } else {
                    $msg = "";
                    foreach ($erro as $eItem) {
                        $msg.=$eItem . " <br />";
                    }
                    $this->get('session')->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('user_user_edit'));
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
                    return $this->redirect($this->generateUrl('user_user_new'));
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
                $user->setGender($dadosPost['gender']);
                $birth = new \DateTime();
                if($dadosPost['birth']['year'] != "" && $dadosPost['birth']['month'] != "" && $dadosPost['birth']['day']){
                    $birth->setDate($dadosPost['birth']['year'], $dadosPost['birth']['month'], $dadosPost['birth']['day']);
                    $user->setBirth($birth);
                }
                $user->setAvatar('');
                $user->setLang('pt_BR');
                $user->setTheme('');
                $user->setCreated(new \DateTime());
                $user->setRoles('ROLE_USER');
                $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
                $user->setCity($city);
                //$user->setCpf($dadosPost['cpf']);
                $user->setEmail($dadosPost['email']);
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($dadosPost['password'], $user->getSalt()));
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setNewsletters($dadosPost['email'] == 1 ? true : false);
                $this->dm()->persist($user);
                $this->dm()->flush();
                
                if ($modoCadastro == 'email') {
                    //envio de email para confirmar user
                    $this->emailActKey($dadosPost['email'], $dadosPost['name'], $actkey);
                    // /envio de email para confirmar user
                    $msg = $this->trans('Cadastro realizado, favor verificar seu email.');
                } elseif ($modoCadastro == 'auto') {
                    $mail->to($user)
                         ->subject('Seja bem vindo')
                         ->template('usuario_bemvindo', array('user' => $user, 'title' => 'Bem-vindo, '.$user->getName().'!'))
                         ->send();
                    $msg = $this->trans('Cadastro realizado, bem vindo.');
                    $this->authenticateUser($user);
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
                    $msg = $this->trans('Cadastro realizado, aguardar aprovação');
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
                $mail->notify('Novo usuário', 'O usuário '.$user->getName().' ('.$user->getEmail().') Foi cadastrado com sucesso no sistema.');
                $this->get('session')->setFlash('ok', $msg);
                return $this->redirect($this->generateUrl('user_user_confirmation', array('username' => $user->getUsername())));
            }
        } else {
            $this->get('session')->setFlash('error', $this->trans('Erro de validação no cadastro, tente novamente.'));
            $user = $this->dm()->getReference('ReurbanoUserBundle:User', $dadosPost['id']);
            return $this->redirect($this->generateUrl('user_user_edit', array('username' => $user->getUsername())));
        }
    }
    /**
     * @Route("/usuario/salvarPromo", name="user_user_savePromo")
     * @Template()
     */
    public function savePromoAction() {
        $request = $this->get('request');
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->all();
        $erro = array();
        $mail = $this->get('mastop.mailer');
        $modoCadastro = $this->get('mastop')->param('user.all.autoactive');
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
            return $this->redirect($this->generateUrl('user_user_new'));
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
        $user->setGender($dadosPost['gender']);
        $user->setAvatar('');
        $user->setLang('pt_BR');
        $user->setTheme('');
        $user->setCreated(new \DateTime());
        $user->setRoles('ROLE_USER');
        $cityId = $this->get('session')->get('reurbano.user.cityId');
        $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
        $user->setCity($city);
        $user->setEmail($dadosPost['email']);
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($dadosPost['password'], $user->getSalt()));
        $user->setMoneyFree(0);
        $user->setMoneyBlock(0);
        $user->setNewsletters(true);
        $this->dm()->persist($user);
        $this->dm()->flush();

        if ($modoCadastro == 'email') {
            //envio de email para confirmar user
            $this->emailActKey($dadosPost['email'], $dadosPost['name'], $actkey);
            // /envio de email para confirmar user
            $msg = $this->trans('Cadastro realizado, favor verificar seu email.');
        } elseif ($modoCadastro == 'auto') {
            $mail->to($user)
                    ->subject('Seja bem vindo')
                    ->template('usuario_bemvindo', array('user' => $user, 'title' => 'Bem-vindo, '.$user->getName().'!'))
                    ->send();
            $msg = $this->trans('Cadastro realizado, você já está participando da promoção!');
            $this->authenticateUser($user);
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
            $msg = $this->trans('Cadastro realizado, aguardar aprovação');
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
        $mail->notify('Novo usuário - Promoção iPad', 'O usuário '.$user->getName().' ('.$user->getEmail().') Foi cadastrado com sucesso no sistema.');
        $this->get('session')->setFlash('ok', $msg);
        $this->setCookie('hideiPad', 1, (time()+604800));
        return $this->redirect($this->generateUrl('_home', array('username' => $user->getUsername())));
        
    }

    /**
     * @Route("/usuario/senha/recupera/{username}", name="user_user_recovery", defaults={"username" = null})
     * @Template()
     */
    public function recoveryAction($username=null) {
        $factory = $this->get('form.factory');
        $form = $factory->create(new ForgetForm());
        return $this->render('ReurbanoUserBundle:Frontend/Security:forget.html.twig', array(
                    'form' => $form->createView(),
                ));
    }

    /**
     * @Route("/usuario/recuperaok", name="user_user_recoverypost")
     * @Template()
     */
    public function recuveryPostAction() {
        $form = $this->get('form.factory')->create(new ForgetForm());
        $dadosPost = $this->get('request')->request->get($form->getName());
        $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
        $usuario = $repository->findByField('email', $dadosPost['email']);
        if (count($usuario) != 1) {
            $this->get('session')->setFlash('error', $this->trans('Email não encontrado no sistema.'));
            return $this->redirect($this->generateUrl('user_user_recovery'));
        } else {
            $this->emailTrocaSenha($usuario);
            $this->get('session')->setFlash('ok', $this->trans('Favor seguir as instruções enviadas para o email %email%.', array('%email%' => $dadosPost['email'])));
            return $this->redirect($this->generateUrl('_home'));
        }
        //ver se o post eh username ou email, ver se tem um email/username com este dado alterar campo recoverid para um uniqkey,
        //enviar email para este usuário com uma url com o uniqkey que ao acessar entra em um action que permite o usuário editar a senha
    }

    /**
     * @Route("/usuario/senha/recuperacao/{actkey}", name="user_user_recovering")
     * @Template()
     */
    public function recoveringAction($actkey) {
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
     * @Route("/usuario/senha/recuperacaook/{actkey}", name="user_user_recoveringok")
     * @Template()
     */
    public function recoveringOkAction($actkey) {
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
                $this->get('session')->setFlash('ok', $this->trans('Senha alterada com sucesso, seja bem vindo %name$.',array('%name%'=>$usuario->getName())));
                $this->authenticateUser($usuario);
                return $this->redirect($this->generateUrl('_home'));
            }
        } else {
            $this->get('session')->setFlash('error', $this->trans('Código de recuperação inválido. Solicite novamente a troca de sua senha.'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/usuario/novoemail/{edited}/{email}", name="user_user_newemail")
     * @Template()
     */
    public function newEmailAction($edited, $email) {
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
            return $this->redirect($this->generateUrl('user_user_details', array('username' => $usuario->getUsername())));
        } else {
            $this->get('session')->setFlash('error', $this->trans('Não foi possível trocar seu email, repita o processo.'));
            return $this->redirect($this->generateUrl('_home'));
        }
    }

    /**
     * @Route("/usuario/facebook", name="user_user_facebook")
     * @Template()
     */
    public function facebookAction() {
        if ($this->get('request')->isXmlHttpRequest()) {
            //precisa ver se o usuario facebook ja nao esta cadastrado, se estiver apenas logar
            //se nao tiver logado entao pegar os dados dele e criar o user automaticamente
            //em ambos os casos tem que logar automaticamente depois disto
            $repository = $this->dm()->getRepository('ReurbanoUserBundle:user');
            $request = $this->getRequest();
            $result['success'] = 'false';
            $cityId = $this->get('session')->get('reurbano.user.cityId');
            
            $gets = $request->query;
            $usuario = $repository->findOneBy(array('facebookid' => $gets->get('facebookId')));
            $usuario2 = $repository->findOneBy(array('email' => $gets->get('email')));
            if ($usuario || $usuario2) {
                //é existe o user
                //verificar se os dados cadastrais estao atualizados
                if ($usuario) {
                    // Verifica se ele não tá inativo
                    if($usuario->getStatus() == 2){
                        return $this->redirectFlash($this->generateUrl('_home'), 'Seu usuário ('.$usuario->getEmail().') está bloqueado. Entre em contato para maiores informações.', 'error');
                    }
                    //o userfacebook é ele
                    $user = $this->dm()->getReference('ReurbanoUserBundle:User', $usuario->getId());
                    $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                    $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
                    $user->setCity($city);
                    $this->dm()->persist($user);
                    $this->dm()->flush();
                    $this->get('session')->setFlash('ok', $this->trans('Olá %name%.', array('%name%' => $usuario->getName())));
                    $result['success'] = 'true';
                } elseif ($usuario2) {
                    $fbID = $usuario2->getFacebookid();
                    if (!isset($fbID) || $fbID == $gets->get('facebookId')) {
                        $user = $this->dm()->getReference('ReurbanoUserBundle:User', $usuario2->getId());
                        $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                        $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
                        $user->setCity($city);
                        if (!isset($fbID)) {
                            $user->setFacebookid($gets->get('facebookId'));
                            $this->get('session')->setFlash('ok', $this->trans('Olá %name%, seu facebook foi vinculado a sua conta.', array('%name%' => $usuario2->getName())));
                        } else {
                            $this->get('session')->setFlash('ok', $this->trans('Olá %name%.', array('%name%' => $usuario2->getName())));
                        }

                        $result['success'] = 'true';
                    } else {
                        $this->get('session')->setFlash('error', $this->trans('Já existe uma conta com estes dados em nosso sistema e ela não pertence ao seu facebook'));
                        $result['success'] = 'false';
                    }
                }
                //efetuar o login
                $this->authenticateUser($user);
            } else {
                //novo user, salvar ele
                $user = new User();
                $user->setName($gets->get('firstName') . " " . $gets->get('lastName'));
                $user->setUsername(str_replace(".", "", str_replace("@", "", $gets->get('email'))));
                $user->setEmail($gets->get('email'));
                $user->setActkey('');
                $user->setMailOk(true);
                $user->setStatus(1);
                $user->setLang('pt_BR');
                $user->setCreated(new \DateTime());
                $user->setRoles('ROLE_USER');
                $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
                $user->setCity($city);
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
                $user->setGender(strtoupper(substr($gets->get('gender'), 0, 1)));
                $user->setMoneyFree(0);
                $user->setMoneyBlock(0);
                $user->setFacebookid($gets->get('facebookId'));
                $user->setFacebookToken($gets->get('facebookToken'));
                $user->setNewsletters(true);
                $this->dm()->persist($user);
                $this->dm()->flush();
                $this->get('session')->setFlash('ok', $this->trans('Cadastro efetuado com seus dados do Facebook.'));
                $result['success'] = 'true';
                //notificação de novo usuario interno
                $emailsNotify = str_replace(",", ";", $this->get('mastop')->param('user.all.mailnotify'));
                if ($emailsNotify != "") {
                    $emailsNotify = explode(";", $emailsNotify);
                    foreach ($emailsNotify as $email) {
                        $this->newUserEmail(str_replace(" ", '', $email), $user);
                    }
                }
                // /notificação de novo usuario interno
                // notificação ao usuário que ele foi cadastrado
                $this->newUserEmail(str_replace(" ", '', false), $user);
                // /notificação ao usuário que ele foi cadastrado
                //efetuar o login
                $this->authenticateUser($user);
            }
            // exit(var_dump($result));

            return new Response(json_encode($result));
        }
        return new Response($this->get('translator')->trans('Operação não permitida.'));
    }

    /**
     * Logar o usuário depois de facebookear ou twittear
     *
     * @param Boolean $reAuthenticate
     */
    private function authenticateUser(UserInterface $user) {
        $providerKey = "main";
        $role = $user->getRoles();
        if (in_array("ROLE_USER", $role)) {
            $user->setLastLogin(new \DateTime());
            $this->dm()->persist($user);
            $this->dm()->flush();
            $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
            $this->container->get('security.context')->setToken($token);
            $this->setCookie('hideNL', 1, time() + 604800);
        }
    }

    /**
     * @Route("/usuario/twitter/conect", name="user_user_twitterconect")
     * @Template()
     */
    function twitterConectAction() {
        $connection = $this->get('mastop.twitter');
        return $this->redirect($connection->getLoginUrl($this->get('request')));
    }

    /**
     * @Route("/usuario/twitter/back", name="user_user_twitterback")
     * @Template()
     */
    function twitterBackAction() {
        $connection = $this->get('mastop.twitter');
        $token_credentials = $connection->getAccessToken($this->get('request'));
        $session = $this->get('session');
        if (!empty($token_credentials)) {
            $session->set('twitter_credentials', serialize($token_credentials));
        } else {
            if ($session->has('twitter_credentials')) {
                $token_credentials = unserialize($session->get('twitter_credentials'));
            } else {
                $msg = $this->trans('Erro ao cadastrar o usuário, não foi possível comunicar-se com o Twitter.');
                $session->setFlash('error', $msg);
                return $this->redirect($this->generateUrl('_login'));
            }
        }
        $dados = $connection->getUserData($this->get('request'), array('user_id' => $token_credentials['user_id']));
        $request = $this->getRequest();

        if (is_object($dados)) {
            $repository = $this->dm()->getRepository('ReurbanoUserBundle:User');
            $usuario = $repository->findOneBy(array('twitterid' => $token_credentials['user_id']));
            if ($usuario) {
                // Verifica se ele não tá inativo
                if($usuario->getStatus() == 2){
                    return $this->redirectFlash($this->generateUrl('_home'), 'Seu usuário ('.$usuario->getEmail().') está bloqueado. Entre em contato para maiores informações.', 'error');
                }
                //ja existe um usuario com este twitter, portanto apenas logar ele
                $this->authenticateUser($usuario);
                $msg = $this->trans('Olá %name%.', array("%name%" => $usuario->getName()));
                $session->setFlash('ok', $msg);
                !$session->has('twitter_credentials') ? : $session->remove('twitter_credentials', null);
                return $this->redirect($this->generateUrl('_home'));
            } else {
                //novo usuario baseado no twitter, precisa do email dele.
                $factory = $this->get('form.factory');
                $titulo = $this->trans("Cadastro via Twitter - Informe seu email");
                $txtextra = $this->trans("Agora falta pouco para seu cadastro ser concluído, basta informar seu email:");
                $form = $factory->create(new UserFormTwitter());
                return $this->render('ReurbanoUserBundle:Frontend/User:novoTwitter.html.twig', array(
                            'form' => $form->createView(), 'titulo' => $titulo, 'txtextra' => $txtextra
                        ));
            }
        } else {
            $msg = $this->trans('Erro ao cadastrar o usuário, não foi possível comunicar-se com o Twitter.');
            $session->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_login'));
        }
    }

    /**
     * @Route("/usuario/twitter/salvar", name="user_user_twittersave")
     * @Template()
     */
    function twitterNewAction() {
        $connection = $this->get('mastop.twitter');
        $request = $this->get('request');
        $session = $request->getSession();
        $cityId = $this->get('session')->get('reurbano.user.cityId');
        $dados = $connection->getUserData($this->get('request'), array('user_id' => $session->get('tw_user_id')));
        $factory = $this->get('form.factory');
        $form = $factory->create(new UserFormTwitter());
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->get($form->getName());
        if (!isset($dadosPost['agree'])) {
            //precisa aceitar os termos
            $msg = $this->trans('Você precisa aceitar nossos termos e condições de uso.');
            $session->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('user_user_twitterback'));
        }
        if (!empty($dadosPost['email'])) {
            $usuario = $repository->findOneBy(array('email' => $dadosPost['email']));
            if ($usuario) {
                //ja existe usuário com este email
                $msg = $this->trans('Erro ao criar seu usuário, o email informado já é utilizado por outro usuário.');
                $session->setFlash('error', $msg);
                return $this->redirect($this->generateUrl('user_user_twitterback'));
            } else {
                //email não existe no bd mas será que é realmente email
                if (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $dadosPost['email'])) {
                    if (is_object($dados)) {

                        $user = new user();
                        $user->setName($dados->name);
                        $user->setUsername(str_replace(".", "", str_replace("@", "", $dadosPost['email'])));
                        $user->setActkey('');
                        $user->setMailOk(true);
                        $user->setStatus(1);
                        $user->setAvatar('');
                        $user->setLang('pt_BR');
                        $user->setTheme('');
                        $user->setCreated(new \DateTime());
                        $user->setRoles('ROLE_USER');
                        $city = $this->mongo('ReurbanoCoreBundle:City')->findOneById($cityId);
                        $user->setCity($city);
                        $user->setCpf('');
                        $user->setEmail($dadosPost['email']);
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
                        $user->setMoneyFree(0);
                        $user->setMoneyBlock(0);
                        $user->setNewsletters(isset($dadosPost['newsletters']) ? true : false);
                        $user->setTwitterid($dados->id);
                        $user->setTwitter($dados->screen_name);

                        $user->setTwToken($session->get('tw_access_token'));
                        $user->setTwSecret($session->get('tw_access_token_secret'));
                        $user->setNewsletters(true);
                        $this->dm()->persist($user);
                        $this->dm()->flush();
                        $session->setFlash('ok', $this->trans('Cadastro efetuado com seus dados do Twitter.'));
                        $result['success'] = true;
                        //notificação de novo usuario interno
                        $emailsNotify = str_replace(",", ";", $this->get('mastop')->param('user.all.mailnotify'));
                        if ($emailsNotify != "") {
                            $emailsNotify = explode(";", $emailsNotify);
                            foreach ($emailsNotify as $email) {
                                $this->newUserEmail(str_replace(" ", '', $email), $user);
                            }
                        }
                        // /notificação de novo usuario interno
                        // notificação ao usuário que ele foi cadastrado
                        $this->newUserEmail(str_replace(" ", '', false), $user);
                        // /notificação ao usuário que ele foi cadastrado
                        //efetuar o login
                        $this->authenticateUser($user);
                        $result['success'] = true;
                        $result['msg'] = $this->get('translator')->trans('Usuário cadastrado.');
                        !$session->has('twitter_credentials') ? : $session->remove('twitter_credentials', null);
                        return $this->redirect($this->generateUrl('_home'));
                    } else {
                        $msg = $this->trans('Erro ao cadastrar o usuário, não foi possível comunicar-se com o Twitter.');
                        $session->setFlash('error', $msg);
                        !$session->has('twitter_credentials') ? : $session->remove('twitter_credentials', null);
                        return $this->redirect($this->generateUrl('_home'));
                    }
                } else {
                    $msg = $this->trans('Erro ao criar seu usuário, favor fornecer um email válido.');
                    $session->setFlash('error', $msg);
                    return $this->redirect($this->generateUrl('user_user_twitterback'));
                }
            }
        } else {
            //devolve para o cadastro de email
            $msg = $this->trans('Erro ao criar seu usuário, favor fornecer um email.');
            $session->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('user_user_twitterback'));
        }
    }
    
    /**
     * @Route("/usuario/banco", name="user_user_bank")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function bankAction() {
        $userLogado = $this->get('security.context')->getToken()->getUser();
        $bankData = $userLogado->getBankData();
        $form = $this->createForm(new BankDataType(), $bankData);
        $ret['title'] = 'Informações Bancárias';
        $ret['form'] = $form->createView();
        return $ret;
    }
    
    /**
     * @Route("/usuario/banco/salvar", name="user_user_banksave")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function bankSaveAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $bankData = $user->getBankData();
        $form = $this->createForm(new BankDataType(), $bankData);
        $request = $this->get('request');
        $dm = $this->dm();
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $user->setBankData($form->getData());
                $dm->persist($user);
                $dm->flush();
                return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Informações bancárias atualizadas');
            }
        }
        return $this->redirectFlash($this->generateUrl('user_dashboard_index'), 'Ocorreu um erro ao processar suas informações bancárias.', 'error');
    }

}