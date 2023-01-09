<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\RolesDb;
use App\Flash;
use Mpdf\Mpdf;

/**
 * Administración del Sistema (example)
 *
 * PHP version 7.0
 */
class Certificado extends Authenticated
{
    /**
     * Crear el torneo
     *
     * @return void
     */
    public function indexAction()
    {
        $html = View::getTemplate('Certificado/index.html');
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
