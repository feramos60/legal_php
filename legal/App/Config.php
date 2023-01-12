<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'legaltech';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = TRUE;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'your-secret-key';

    /**
     * Host de envio dfe correo
     * @var string
     */
    const HOST_SMTP = 'mail.ecoapplet.co';

    /**
     * Usuario del correo
     * @var string
     */
    const USER_MAIL = 'contacto@ecoapplet.co';

    /**
     * CONTRASEÑA del correo
     * @var string
     */
    const PASS_MAIL = 'Juan99103034#';
}
