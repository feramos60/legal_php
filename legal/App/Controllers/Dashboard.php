<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;


/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Dashboard extends Authenticated
{
    /**
     * Before filter - called before each action method
     * Hace que se requiera loguearse para poder acceder a estos metodos
     * con $this->user se llama este metodo before
     *
     * @return void
     */
    protected function before()
    {
        parent::before();           //Llama el metodo before() de la clase Authenticated

        $this->user = Auth::getUser();
    }
    
    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {        
        View::renderTemplate('Dashboard/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }    
}
