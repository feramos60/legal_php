<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\ImpugnaDb;
use Mpdf\Mpdf;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Impugna extends Authenticated
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
        $this->authorizationRequired(permission: 2);
    }

    /**
     * Items index muestra el formulario
     * de datos para generar PDF
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/Impugna/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Items index muestra el formulario
     * de datos para generar PDF
     *
     * @return void
     */
    public function createAction()
    {
        $comparendo = new ImpugnaDb($_POST);

        if ($comparendo->saveComparendo()) {
            $this->pdfAction($_POST);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/dashboard/index');
                        
        } else {
            echo 'NO Se subio la info';
        }
        
        // View::renderTemplate('/Impugna/index.html', [
        //     'user' => $this->user                   //llama al resultado del metodo before()
        // ]);
    }

    /**
     * Items index muestra el formulario
     * de datos para generar PDF
     *
     * @return void
     */
    public function pdfAction($datos)
    {        
        $html = View::getTemplate('Impugna/docComparendo.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'datos' => $datos
        ]);
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output('filename.pdf', 'D');
        

    }
}
