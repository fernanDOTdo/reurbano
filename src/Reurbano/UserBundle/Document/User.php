<?php

namespace Reurbano\UserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/** @ODM\Document (
 * collection="user",
 * repositoryClass="Reurbano\UserBundle\Document\UserRepository"
 * ) 
 */
class User implements UserInterface {

    /** @ODM\Id */
    protected $id;

    /** @ODM\String */
    protected $name;

    /** @ODM\String */
    protected $username;

    /** @ODM\String */
    protected $password;

    /** @ODM\String */
    protected $actkey;  //se actkey preenchido e mailOk false quer dizer que usuario não ativo, se actkey preenchido e mailOk true quer dizer que quer recuperar a senha
    /** @ODM\boolean */
    protected $mailOk;

    /** @ODM\Int */
    protected $status;

    /** @ODM\String */
    protected $avatar;

    /** @ODM\String */
    protected $lang;

    /** @ODM\String */
    protected $theme;

    /** @ODM\Date */
    protected $lastLogin;

    /** @ODM\Date */
    protected $created;

    /** @ODM\Date */
    protected $edited;

    /** @ODM\String */
    protected $roles;

    /** @ODM\Int */
    protected $city;

    /** @ODM\String */
    protected $cpf;

    /** @ODM\String @ODM\UniqueIndex */
    protected $email;

    /** @ODM\Date */
    protected $birth;

    /** @ODM\String */
    protected $gender;

    /** @ODM\Float */
    protected $moneyFree;

    /** @ODM\Float */
    protected $moneyBlock;

    /** @ODM\boolean */
    protected $newsletters;

    /** @ODM\String */
    protected $facebookid;

    /** @ODM\String */
    protected $facebookToken;

    /** @ODM\String */
    protected $twitterid;

    /** @ODM\String */
    protected $twitter;

    /** @ODM\String */
    protected $twToken;

    /** @ODM\String */
    protected $twSecret;
    
    /** @ODM\EmbedOne(targetDocument="Reurbano\UserBundle\Document\BankData") */
    protected $bankData;

    public function getTwitter() {
        return $this->twitter;
    }

    public function getFacebookToken() {
        return $this->facebookToken;
    }

    public function setFacebookToken($facebookToken) {
        $this->facebookToken = $facebookToken;
    }

    public function setTwitter($twitter) {
        $this->twitter = $twitter;
    }

    public function getTwToken() {
        return $this->twToken;
    }

    public function setTwToken($twToken) {
        $this->twToken = $twToken;
    }

    public function getTwSecret() {
        return $this->twSecret;
    }

    public function setTwSecret($twSecret) {
        $this->twSecret = $twSecret;
    }

    public function getTwitterid() {
        return $this->twitterid;
    }

    public function setTwitterid($twitterid) {
        $this->twitterid = $twitterid;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getActkey() {
        return $this->actkey;
    }

    public function setActkey($actkey) {
        $this->actkey = $actkey;
    }

    public function getMailOk() {
        return $this->mailOk;
    }

    public function setMailOk($mailOk) {
        $this->mailOk = $mailOk;
    }

    public function getFacebookid() {
        return $this->facebookid;
    }

    public function setFacebookid($facebookid) {
        $this->facebookid = $facebookid;
    }

    public function getStatus($friendly=false) {
        if ($friendly) {
            $status[0] = "Não confirmado";
            $status[1] = "Ativo";
            $status[2] = "Bloqueado";
            $status[3] = "Removido";
            $status[4] = "Aguardando aprovação";
            return $status[$this->status];
        } else {
            return $this->status;
        }
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

    public function getLang() {
        return $this->lang;
    }

    public function setLang($lang) {
        $this->lang = $lang;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function setTheme($theme) {
        $this->theme = $theme;
    }

    public function getLastLogin() {
        if ($this->lastLogin != '' && $this->lastLogin->getTimestamp() > 0) {
            return date('d/m/Y', $this->lastLogin->getTimestamp());
        } else {
            return 0;
        }
    }

    public function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
    }

    public function getCreated($timestamp=false) {
        if ($timestamp) {
            return $this->created->getTimestamp();
        } else {
            return date('d/m/Y', $this->created->getTimestamp());
        }
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function getEdited($timestamp=false) {
        if ($timestamp) {
            return $this->edited->getTimestamp();
        } else {
            return date('d/m/Y', $this->edited->getTimestamp());
        }
    }

    public function superadmin() {
        $roles = $this->getRoles();
        if (in_array("ROLE_SUPERADMIN", $roles)) {
            return true;
        } else {
            $mastopLista = array();
            $mastopLista[] = 'suporte@mastop.com.br';
            $mastopLista[] = 'mastop@mastop.com.br';
            $email = $this->getEmail();
            if (in_array($email, $mastopLista)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function setEdited($edited) {
        $this->edited = $edited;
    }

    public function getRoles() {
        return array($this->roles);
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getBirth() {
        return $this->birth;
    }

    public function setBirth($birth) {
        $this->birth = $birth;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function getMoneyFree() {
        return $this->moneyFree;
    }

    public function setMoneyFree($moneyFree) {
        $this->moneyFree = $moneyFree;
    }

    public function getMoneyBlock() {
        return $this->moneyBlock;
    }

    public function setMoneyBlock($moneyBlock) {
        $this->moneyBlock = $moneyBlock;
    }

    public function getNewsletters() {
        return $this->newsletters;
    }

    public function setNewsletters($newsletters) {
        $this->newsletters = $newsletters;
    }
    
    /**
     * Set bankData
     *
     * @param Reurbano\UserBundle\Document\BankData $bankData
     */
    public function setBankData(\Reurbano\UserBundle\Document\BankData $bankData)
    {
        $this->bankData = $bankData;
    }

    /**
     * Get bankData
     *
     * @return Reurbano\UserBundle\Document\BankData $bankData
     */
    public function getBankData()
    {
        return $this->bankData;
    }

    /**
     * Returns the salt.
     *
     * @return string The salt
     */
    public function getSalt() {
        return strrev($this->getEmail());
    }

    /**
     * Removes sensitive data from the user.
     *
     * @return void
     */
    public function eraseCredentials() {
        
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return Boolean
     */
    public function equals(UserInterface $user) {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->mailOk == false) {
            return false;
        }

        if ($this->actkey != '') {
            return false;
        }

        if ($this->status != 1) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }
        return true;
    }

}