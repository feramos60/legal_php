<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use App\Flash;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{
  /**
   * Show the signup page
   *
   * @return void
   */
  public function newAction()
  {
      View::renderTemplate('Signup/new.html');
  }

  /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);
        if ($user->save()) {
            if ($user->sendActivationEmail()){
                $this->redirect('/legaltech/compa/signup/success');  //cuando llegue a la raiz se debe quitar la primera carpeta
            }else{
                View::renderTemplate('Login/index.html', [
                    'user' => $user
                ]);
            }                      
        } else {
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/alta.html');
    }

    /**
     * Activate a new account
     *
     * @return void
     */
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect(dirname($_SERVER["PHP_SELF"]).'/signup/activated');        
    }

    /**
     * Show the activation success page
     *
     * @return void
     */
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
