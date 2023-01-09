<?php

namespace App\Models;

use PDO;
use \App\Token;
use Exception;

ini_set('date.timezone', 'America/Bogota');

/**
 * Example roles model
 *
 * PHP version 7.0
 */
class RenovaDb extends \Core\Model
{
    /**
     * Class constructor
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
     * Seleccionar todos loa años 
     * para edición.
     *
     * 
     */
    public static function selectReg()
    {
        $sql = 'SELECT * FROM registros';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute();
        } catch (Exception $th) {            
            return false;
        }      

        return $stmt->fetchAll();
    }

    /**
     * Seleccionar el club.
     *
     * 
     */
    public static function RegById($id)
    {
        $sql = 'SELECT * FROM registros WHERE id='. $id;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute();
        } catch (Exception $th) {            
            return false;
        }      

        return $stmt->fetch();
    }

    /**
     * Metodo que actualiza el año en
     * la base de datos tabla Registro
     * 
     * @return void
     */
    public function updateRegistros()
    {
        $sql = 'UPDATE registros
                    SET val_dep = :val_dep,
                        val_club = :val_club,
                        updated_at = :updated_at,
                        updated_by_id = :updated_by_id,
                        status = :status                        
                    ';                 
            $sql .= "\nWHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':val_dep', $this->val_dep, PDO::PARAM_INT);
        $stmt->bindValue(':val_club', $this->val_club, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':status', $this->status, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (Exception $th) {
            return false;
        }
       
    }

    /**
     * Busca si el financiero ha activado el proceso
     * para el año en curso.
     *
     * 
     */
    public static function registro()
    {
        $sql = 'SELECT * FROM registros WHERE year= :yearNow';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':yearNow', date('Y'), PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch (Exception $th) {            
            return false;
        }      

        return $stmt->fetch();
    }

    /**
     * Busca si nadador ya tiene hoja de vida
     * actualizada
     *
     * 
     */
    public static function hojaVida($id_nada)
    {
        $sql = 'SELECT * FROM resumes WHERE year = :yearNow AND id_nadador = :id_nadador';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':yearNow', date('Y'), PDO::PARAM_STR);
        $stmt->bindValue(':id_nadador', $id_nada, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (Exception $th) {            
            return false;
        }      

        return $stmt->rowCount();
    }

    /**
     * Busca los datos que el nadador tenga en la última
     * Hoja de Vida
     *
     * 
     */
    public static function hojaVidaDatos($id_nada)
    {
        $sql = 'SELECT * FROM resumes WHERE year = :yearNow AND id_nadador = :id_nadador';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':yearNow', date('Y'), PDO::PARAM_STR);
        $stmt->bindValue(':id_nadador', $id_nada, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (Exception $th) {            
            return false;
        }      

        return $stmt->rowCount();
    }

    /**
     * Encuentra todos los datos del nadador
     * seleccionado para ponerlos en el formulario
     *
     * @param int $id id del nadador
     *
     * @return mixed User object if found, false otherwise
     */
    public static function datosNadador($id)
    {

        $sql = 'SELECT * FROM nadadores
            INNER JOIN clubes ON nadadores.id_club = clubes.id
            INNER JOIN ligas ON clubes.id_liga = ligas.id
            INNER JOIN resumes ON nadadores.id = resumes.id_nadador
            WHERE nadadores.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $th) {            
            return false;
        }        
    }
}