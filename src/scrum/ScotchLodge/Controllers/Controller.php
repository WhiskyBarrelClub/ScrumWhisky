<?php

namespace scrum\ScotchLodge\Controllers;

use Doctrine\ORM\EntityManager;
use Slim\Slim;

/**
 * Controller abstract controller
 *
 * @author jan van biervliet
 */
abstract class Controller {
  /* @var $em EntityManager */
  private $em;

  /* @var $app  Slim */
  private $app;

  function __construct($em, $app) {
    $this->em = $em;
    $this->app = $app;
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function getEntityManager() {
    return $this->em;
  }

  public function getApp() {
    return $this->app;
  }
  
  public function getGlobals() {
    $globals = array(
      'app' => $this->app,
      'user' => $this->getUser(),
      'session' => $this->getSession()
    );
    return $globals;
  }
  
  public function getSession() {
    return $_SESSION;
  }
  
  private function queryUserByUserName($username) {
    $em = $this->getEntityManager();
    $repo = $em->getRepository('scrum\ScotchLodge\Entities\User');
    $user = $repo->findBy(array('username' => $username));
    return $user;
  }

  public function getUser() {
    if (isset($_SESSION['user']) && $_SESSION['user'] != null ) {
      $user = $this->queryUserByUserName($_SESSION['user']);
      return $user[0];
    }
    return null;
  }
  
  public function isUserAnonymous() {
    return !$this->isLoggedIn();
  }
  
  public function isUserLoggedIn() {
    return $this->getUser() != null;
  }

  public function isUserAdmin() {
    if (isset($_SESSION['user'])) {
      $user = unserialize($_SESSION['user']);
      return $user->isAdmin() == 1 ? true : false;
    }
    return false;
  }

}
