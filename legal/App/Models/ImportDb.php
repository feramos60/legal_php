<?php

namespace App\Models;

use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;


ini_set('date.timezone', 'America/Bogota');

/**
 * Clase para Cargar en la base de datos
 * los datos del torneo
 *
 * PHP version 7.0
 */
class ImportDb extends \Core\Model
{

    /**
     * Class constructor para la consulta de los torneos
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }


    /**
     * Lee el archivo Excel con los
     * Nadadores y lo sube a la base
     * de datos en la tabla nadadores
     *
     * @return void
     */
    public static function subirNadadores($file)
    {

        if (file_exists($file)) {
            $documento = IOFactory::load($file);
            // Clases necesarias
            # Se espera que en la primera hoja estén los productos
            $hojaDeDatos = $documento->getSheet(0);

            # Calcular el máximo valor de la fila como entero, es decir, el
            # límite de nuestro ciclo
            $numeroMayorDeFila = $hojaDeDatos->getHighestRow(); // Numérico: se optiene el mayor número de la filas

            //contador de errores a cero
            $errores = 0;

            $i = 1;

            $mensaje= array();

            for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
                # Las columnas están en este orden:
                # Lee el titulo d ela fila y sube la info
                $nuip = $hojaDeDatos->getCellByColumnAndRow(1, $indiceFila);
                $tipo = $hojaDeDatos->getCellByColumnAndRow(2, $indiceFila);
                $apellido = $hojaDeDatos->getCellByColumnAndRow(3, $indiceFila);
                $nombre = $hojaDeDatos->getCellByColumnAndRow(4, $indiceFila);
                $genero = $hojaDeDatos->getCellByColumnAndRow(5, $indiceFila);
                $fecha_nac = $hojaDeDatos->getCellByColumnAndRow(6, $indiceFila)->getFormattedValue();                
                $tfecha = date('Y-m-d', strtotime($fecha_nac));
                $carreras = $hojaDeDatos->getCellByColumnAndRow(7, $indiceFila)->getFormattedValue();                
                $clavados = $hojaDeDatos->getCellByColumnAndRow(8, $indiceFila)->getFormattedValue();
                $artistica = $hojaDeDatos->getCellByColumnAndRow(9, $indiceFila)->getFormattedValue();
                $polo = $hojaDeDatos->getCellByColumnAndRow(10, $indiceFila)->getFormattedValue();
                $aguas = $hojaDeDatos->getCellByColumnAndRow(11, $indiceFila)->getFormattedValue();
                $id_club = $hojaDeDatos->getCellByColumnAndRow(12, $indiceFila)->getFormattedValue();

                $sql = 'INSERT INTO nadadores (tipo, nuip, apellido, nombre, genero, fecha_nac, carreras, clavados, artistica, polo, aguas, master, id_club, exterior, sel_col, inactivo, act_liga, act_fede, antig, created_at, updated_at, status, created_by_id, updated_by_id)
                    VALUES (:tipo, :nuip, :apellido, :nombre, :genero, :fecha_nac, :carreras, :clavados, :artistica, :polo, :aguas, :master, :id_club, :exterior, :sel_col, :inactivo, :act_liga, :act_fede, :antig, :created_at, :updated_at, :status, :created_by_id, :updated_by_id)';

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
                $stmt->bindValue(':nuip', $nuip, PDO::PARAM_STR);                
                $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
                $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindValue(':genero', $genero, PDO::PARAM_STR);
                $stmt->bindValue(':fecha_nac', $tfecha, PDO::PARAM_STR);
                $stmt->bindValue(':carreras', $carreras, PDO::PARAM_INT);
                $stmt->bindValue(':clavados', $clavados, PDO::PARAM_INT);
                $stmt->bindValue(':artistica', $artistica, PDO::PARAM_INT);
                $stmt->bindValue(':polo', $polo, PDO::PARAM_INT);
                $stmt->bindValue(':aguas', $aguas, PDO::PARAM_INT);
                $stmt->bindValue(':master', 0, PDO::PARAM_INT);
                $stmt->bindValue(':id_club', $id_club, PDO::PARAM_INT);
                $stmt->bindValue(':exterior', 0, PDO::PARAM_INT);
                $stmt->bindValue(':sel_col', 0, PDO::PARAM_INT);
                $stmt->bindValue(':inactivo', 0, PDO::PARAM_INT);
                $stmt->bindValue(':act_liga', 0, PDO::PARAM_INT);
                $stmt->bindValue(':act_fede', 0, PDO::PARAM_INT);
                $stmt->bindValue(':antig', 0, PDO::PARAM_INT);
                $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':status', 1, PDO::PARAM_INT);
                $stmt->bindValue(':created_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);

                try {
                    $guardar =$stmt->execute();
                } catch (Exception $th) {
                    
                    $mensaje[$i]= $i;
                        
                }
                $i++;
            }
        } else {
            trigger_error("No se pudo cargar", E_USER_ERROR);
        }
        return $mensaje;
    }

    /**
     * Lee el archivo Excel con los
     * reusltados y lo sube a la base
     * de datos en la tabla resultados
     *
     * @return void
     */
    public static function subirResultados($file)
    {

        if (file_exists($file)) {
            $documento = IOFactory::load($file);
            // Clases necesarias
            # Se espera que en la primera hoja estén los productos
            $hojaDeDatos = $documento->getSheet(0);

            # Calcular el máximo valor de la fila como entero, es decir, el
            # límite de nuestro ciclo
            $numeroMayorDeFila = $hojaDeDatos->getHighestRow(); // Numérico: se optiene el mayor número de la filas

            //contador de errores a cero
            $errores = 0;

            $i = 2;

            $mensaje= array();

            for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
                # Las columnas están en este orden:
                # Lee el titulo d ela fila y sube la info
                $identificacion = $hojaDeDatos->getCellByColumnAndRow(1, $indiceFila)->getFormattedValue();
                $fecha_nac = $hojaDeDatos->getCellByColumnAndRow(2, $indiceFila)->getFormattedValue();
                $nfecha = date('Y-m-d', strtotime($fecha_nac));
                $codigo = $hojaDeDatos->getCellByColumnAndRow(3, $indiceFila)->getFormattedValue();;
                $tiempo = $hojaDeDatos->getCellByColumnAndRow(4, $indiceFila)->getFormattedValue();;
                $torneo = $hojaDeDatos->getCellByColumnAndRow(5, $indiceFila);
                $fecha_tor = $hojaDeDatos->getCellByColumnAndRow(6, $indiceFila)->getFormattedValue();                
                $tfecha = date('Y-m-d', strtotime($fecha_tor));
                $piscina = $hojaDeDatos->getCellByColumnAndRow(7, $indiceFila); 
                
                $sql = 'INSERT INTO resultados (identificacion, fecha, prueba_id, tiempo, torneo, fecha_torneo, piscina)
                    VALUES (:identificacion, :fecha, :prueba_id, :tiempo, :torneo, :fecha_torneo, :piscina)';

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue('identificacion', $identificacion, PDO::PARAM_INT);
                $stmt->bindValue(':fecha', $nfecha, PDO::PARAM_STR);
                $stmt->bindValue(':prueba_id', $codigo, PDO::PARAM_INT);
                $stmt->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
                $stmt->bindValue(':torneo', $torneo, PDO::PARAM_STR);
                $stmt->bindValue(':fecha_torneo', $tfecha, PDO::PARAM_STR);
                $stmt->bindValue(':piscina', $piscina, PDO::PARAM_STR);

                try {
                    $guardar =$stmt->execute();
                } catch (Exception $th) {
                    
                    $mensaje[$i]= $i;
                        
                }

       

                $i++;

                
            }
        } else {
            trigger_error("No se pudo cargar", E_USER_ERROR);
        }

        return $mensaje;
    }

    
}
