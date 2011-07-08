<?php

namespace Reurbano\UserBundle\Controller\Frontend;

use Mastop\SystemBundle\Controller\BaseController;
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

class UserController extends BaseController {

    /**
     * @Route("/script.js", name="user_user_script")
     */
    public function scriptAction() {

        $script = '
            var ajaxPath = "' . $this->generateUrl('user_user_check') . '";
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
     * @Route("/novo", name="user_user_novo")
     * @Template()
     */
    public function novoAction() {
        $pode = $this->get('mastop')->param('user.all.allownew');
        if ($pode) {
            $factory = $this->get('form.factory');
            $form = $factory->create(new UserForm());
            return $this->render('ReurbanoUserBundle:Frontend/User:novo.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            $msg = $this->trans('O cadastro de novos usuários não é permitido.');
            $this->get('session')->setFlash('error', $msg);
            return $this->redirect($this->generateUrl('_home'));
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
            return $this->redirect($this->generateUrl('_login'));
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
     * @Route("/salvar", name="user_user_salvar")
     * @Template()
     */
    public function salvarAction() {
        $request = $this->get('request');
        $factory = $this->get('form.factory');
        $form = $factory->create(new UserForm());
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $dadosPost = $request->request->get($form->getName());
        $erro = array();
        $form->bindRequest($request);
        if ($form->isValid()) {
            if (isset($dadosPost['id'])) {
                //validar se o username inserido não existe ou se é o dele mesmo
                $result = $this->dm()->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('username')->equals($dadosPost['username'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('Já existe o usuário <b>%name%</b>. Utilize outro', array("%name%" => $dadosPost['username']));
                }
                // /validar se o username inserido não existe ou se é o dele mesmo
                //validando se a senha confere com a repetição
                if ($dadosPost['password']['password'] != $dadosPost['password']['password2']) {
                    $erro[] = $this->trans('A senha digitada não confere com a confirmação');
                }
                // /validando se a senha confere com a repetição
                //validando se o email já não existe
                $result = $dm->createQueryBuilder('ReurbanoUserBundle:user')
                        ->field('email')->equals($dadosPost['email'])
                        ->field('id')->notEqual($dadosPost['id'])
                        ->getQuery()
                        ->execute();
                if (($result->count() > 0)) {
                    $erro[] = $this->trans('O endereço de email <b>%email%</b> já foi utilizado. Utilize outro', array('%email%' => $dadosPost['email']));
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
                    $msg = $this->trans('Usuário <b>%name%</b> alterado com sucesso.', array("%name%" => $dadosPost['name'] . " (" . $dadosPost['username'] . ")"));
                    $this->get('session')->setFlash('ok', $msg);
                } else {
                    //return array("erro" => $erro, 'sucesso' => false);
                }
            } else {
                $modoCadastro = $this->get('mastop')->param('user.all.autoactive');
                //validando captcha
                if (!empty($dadosPost['emailVerify'])) {
                    $erro[] = $this->trans('Não foi possível cadastrar seu usuário, sistema automatizado de inserção detectado.');
                }
                // /validando captcha
                //validando se a senha confere com a repetição
                if ($dadosPost['password']['Password'] != $dadosPost['password']['Password2']) {
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
                $user->setPassword($encoder->encodePassword($dadosPost['password']['Password'], $user->getSalt()));
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
                } elseif ($modoCadastro == 'admin') {
                    $msg = $this->trans('Olá <b>%name%</b>, seu cadastro foi efetuado, aguarde a aprovação por um de nossos administradores. Assim que for aprovado você receberá um email de confirmação através do email %email%', array("%name%" => $dadosPost['name'], "%email%" => $dadosPost['email']));
                }
                $this->get('session')->setFlash('ok', $msg);
                if ($modoCadastro == 'auto') {
                    return $this->redirect($this->generateUrl('_login'));
                } else {
                    return $this->redirect($this->generateUrl('_home'));
                }
            }
        } else {

            $this->get('session')->setFlash('error', $this->trans('Erro de validação no cadastro, tente novamente.'));
            return $this->redirect($this->generateUrl('user_user_salvar'));
        }
    }

    /**
     * @Route("/editar/{username}", name="user_user_editar")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function editarAction($username) {
        $factory = $this->get('form.factory');
        $repository = $this->mongo('ReurbanoUserBundle:User');
        $query = $repository->findByUsername($username);
        $form = $factory->create(new UserForm(), $query);
        if (count($query) > 0) {
            if ($this->verificaStatus($query)) {
                return $this->render('ReurbanoUserBundle:Frontend/User:editar.html.twig', array(
                    'form' => $form->createView(),
                    'id' => $username, 'nome' => $query->getName()
                ));
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