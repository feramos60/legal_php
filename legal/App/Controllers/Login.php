<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use App\Flash;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Muestra la página Index
     *
     * @return void
     */
    public function indexAction()
    {        
        View::renderTemplate('Login/index.html');
    }

    /**
     * Este metodo permite recoger los datos
     * enviados en el formulario y validar si el usuario 
     * exite, esta activo y la contraseña es correcta
     * redirecciona a la pagina correspondiente
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['user_name'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);
        
        if ($user) {
            Auth::login($user, $remember_me);
            Flash::addMessage('Usuario Logeado', Flash::INFO);
            $this->redirect(Auth::getReturnToPage());            
        } else {
            Flash::addMessage('Ingreso Erroneo, Intente de nuevo', Flash::WARNING);
            View::renderTemplate('Login/index.html', [
                'user_name' => $_POST['user_name'],
                'remember_me' => $remember_me
            ]);
        }
    }

    public function newAction()
    {
        View::renderTemplate('login/new.html');
        //Dashboard::indexAction();
    }

    /**
     * Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();
        $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
      Flash::addMessage('Ha salido del sistema con éxito!');

      $this->redirect(dirname($_SERVER["PHP_SELF"]));
    }
}