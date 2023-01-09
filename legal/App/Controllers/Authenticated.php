<?php

namespace App\Controllers;

/**
 * Authenticated base controller
 *
 * PHP version 7.0
 */
abstract class Authenticated extends \Core\Controller
{
    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }

    /**
     * Determine if a user has the correct user type to access a controller o pagina.
     * If a user is not authenticated for this controller, redirect to another page (Dashboard).     * 
     * @param $permission - The current user type must match this requirement to access.
     * @param string $redirect      
     * @return bool|void
     */
    protected function authorizationRequired($permission = null)
    {
        if ($permission) {
            $existPermission = array_search($permission, array_column($_SESSION['permissions'], 'module_id'));           
            
            if ($existPermission) {
                $this->Authorization = true;               
            } else {
                if ($existPermission === 0) {
                    $this->Authorization = true;
                } else {
                    $this->Authorization = false;
                }                
            }
        } else {
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Dashboard/index');
        }      
        
        // If a user is not Authorization for this controller, redirect to another page and die.
        if (!$this->Authorization) {				
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Dashboard/index');            
            die();
        }

        return true;
    }
    
}
