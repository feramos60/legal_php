<?php

namespace Core;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        ini_set('date.timezone', 'America/Bogota');
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Error Fatal</h1>";
            echo "<p>Excepción no detectada: '" . get_class($exception) . "'</p>";
            echo "<p>Mensaje: '" . $exception->getMessage() . "'</p>";
            echo "<p>Seguimiento de pila:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Lanzado en '" . $exception->getFile() . "' en la línea " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d H:i:s') . '.txt';
            ini_set('error_log', $log);

            $message = "Excepción no detectada: '" . get_class($exception) . "'";
            $message .= " Con mensaje '" . $exception->getMessage() . "'";
            $message .= "\nSeguimiento de pila: " . $exception->getTraceAsString();
            $message .= "\nLanzado en '" . $exception->getFile() . "' en la línea " . $exception->getLine();

            error_log($message);

            View::renderTemplate("$code.html");
        }
    }
}
