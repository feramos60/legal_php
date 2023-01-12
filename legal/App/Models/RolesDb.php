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
class RolesDb extends \Core\Model
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
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectRoles()
    {
        $sql = 'SELECT * FROM roles';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los usuarios excepto el SuperAdmin
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectUsers()
    {
        $sql = 'SELECT * FROM users WHERE role_id NOT IN (1)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los clubes
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectClubes()
    {
        $sql = 'SELECT * FROM clubes ORDER BY nombre_club ASC'  ;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Metodo que crea el club
     * @return void
     */
    public function crearClub()
    {

        $sql = 'INSERT INTO clubes (nombre_club, abreviatura, pais, email, id_liga, created_at, updated_at, status, time)
                    VALUES (:nombre_club, :abreviatura, :pais, :email, :id_liga, :created_at, :updated_at, :status, :time)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':nombre_club', $this->nombre_club, PDO::PARAM_STR);
        $stmt->bindValue(':abreviatura', $this->abreviatura, PDO::PARAM_STR);
        $stmt->bindValue(':pais', $this->pais, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':id_liga', $this->id_liga, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);        
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':status', 1, PDO::PARAM_STR);
        $stmt->bindValue(':time', 0, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (Exception $th) {
            return false;
        }
       
    }

    /**
     * Metodo que crea el Nadador
     * @return void
     */
    public function crearNada()
    {

        $sql = 'INSERT INTO nadadores (tipo, nuip, apellido, nombre, genero, fecha_nac, carreras, clavados, artistica, polo, aguas, master, id_club, exterior, sel_col, inactivo, act_liga, act_fede, antig, created_at, updated_at, status, created_by_id, updated_by_id)
                    VALUES (:tipo, :nuip, :apellido, :nombre, :genero, :fecha_nac, :carreras, :clavados, :artistica, :polo, :aguas, :master, :id_club, :exterior, :sel_col, :inactivo, :act_liga, :act_fede, :antig, :created_at, :updated_at, :status, :created_by_id, :updated_by_id)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $stmt->bindValue(':nuip', $this->nuip, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $stmt->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $stmt->bindValue(':genero', $this->genero, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_nac', $this->fecha_nac, PDO::PARAM_STR);
        $stmt->bindValue(':carreras', $this->carreras, PDO::PARAM_INT);
        $stmt->bindValue(':clavados', $this->clavados, PDO::PARAM_INT);
        $stmt->bindValue(':artistica', $this->artistica, PDO::PARAM_INT);
        $stmt->bindValue(':polo', $this->polo, PDO::PARAM_INT);
        $stmt->bindValue(':aguas', $this->aguas, PDO::PARAM_INT);
        $stmt->bindValue(':master', $this->master, PDO::PARAM_INT);   
        $stmt->bindValue(':id_club', $this->id_club, PDO::PARAM_INT);
        $stmt->bindValue(':exterior', $this->exterior, PDO::PARAM_STR);
        $stmt->bindValue(':sel_col', $this->seleccion, PDO::PARAM_STR);
        $stmt->bindValue(':inactivo', $this->inactivo, PDO::PARAM_STR);
        $stmt->bindValue(':act_liga', 0, PDO::PARAM_STR);
        $stmt->bindValue(':act_fede', 0, PDO::PARAM_STR);
        $stmt->bindValue(':antig', 0, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':status', 1, PDO::PARAM_STR);
        $stmt->bindValue(':created_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * Find a club model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findClubByID($id)
    {
        $sql = 'SELECT * FROM clubes
        INNER JOIN ligas ON clubes.id_liga = ligas.id
        WHERE clubes.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update the entrenador's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateClub($data)
    {

        if ($data['nombre_club'] != '') {

            $updated_at = time();

            //$this->updated_at = $updated_at;

            //Pendiente poner a funcionar el status del club

            $sql = 'UPDATE clubes
                    SET nombre_club = :nombre_club,
                        abreviatura = :abreviatura,
                        pais = :pais,
                        email = :email,
                        id_liga = :id_liga,                        
                        updated_at = :updated_at,                        
                        time = :time
                    ';                 
            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':nombre_club', $data['nombre_club'], PDO::PARAM_STR);
            $stmt->bindValue(':abreviatura', $data['abreviatura'], PDO::PARAM_STR);
            $stmt->bindValue(':pais', $data['pais'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':id_liga', $data['id_liga'], PDO::PARAM_INT);                      
            $stmt->bindValue(':id', $_SESSION['club'], PDO::PARAM_INT);           
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);
            $stmt->bindValue(':time', $data['time'], PDO::PARAM_INT);
            
            //$stmt->debugDumpParams(); Muestra lo que se ejecuto en MySql
           
           
            try {
                return $stmt->execute();
            } catch (Exception $th) {
                return false;
            }
        }
        return false;
    }

    /**
     * Encontrar todos los nadadores
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectNadadores()
    {
        $sql = 'SELECT * FROM nadadores';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Find a club model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findNadaByID($id)
    {
        $sql = 'SELECT * FROM nadadores WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update the entrenador's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateNada()
    {

        if ($this->nuip != '') {

            $updated_at = time();

            //$this->updated_at = $updated_at;

            $sql = 'UPDATE nadadores
                    SET nuip = :nuip,
                        apellido = :apellido,
                        nombre = :nombre,
                        genero = :genero,
                        fecha_nac = :fecha_nac,
                        carreras = :carreras,
                        clavados = :clavados,
                        artistica = :artistica,
                        polo = :polo,
                        aguas = :aguas,
                        master = :master,
                        id_club = :id_club,
                        exterior = :exterior,
                        sel_col = :sel_col,
                        inactivo = :inactivo,
                        act_liga = :act_liga,
                        act_fede = :act_fede,
                        antig = :antig,                      
                        updated_at = :updated_at,
                        status = :status,
                        updated_by_id = :updated_by_id
                    ';                 
            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':nuip', $this->nuip, PDO::PARAM_STR);
            $stmt->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $stmt->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindValue(':genero', $this->genero, PDO::PARAM_STR);
            $stmt->bindValue(':fecha_nac', $this->fecha_nac, PDO::PARAM_STR);
            $stmt->bindValue(':carreras', $this->carreras, PDO::PARAM_INT);
            $stmt->bindValue(':clavados', $this->clavados, PDO::PARAM_INT);
            $stmt->bindValue(':artistica', $this->artistica, PDO::PARAM_INT);
            $stmt->bindValue(':polo', $this->polo, PDO::PARAM_INT);
            $stmt->bindValue(':aguas', $this->aguas, PDO::PARAM_INT);
            $stmt->bindValue(':master', $this->master, PDO::PARAM_INT);
            $stmt->bindValue(':id_club', $this->id_club, PDO::PARAM_INT);
            $stmt->bindValue(':exterior', $this->exterior, PDO::PARAM_INT);
            $stmt->bindValue(':sel_col', $this->sel_col, PDO::PARAM_INT);
            $stmt->bindValue(':inactivo', $this->inactivo, PDO::PARAM_INT);
            $stmt->bindValue(':act_liga', $this->act_liga, PDO::PARAM_INT);
            $stmt->bindValue(':act_fede', $this->act_fede, PDO::PARAM_INT);
            $stmt->bindValue(':antig', $this->antig, PDO::PARAM_INT);            
            $stmt->bindValue(':id', $_SESSION['nadador'], PDO::PARAM_INT);
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);
            $stmt->bindValue(':status', $this->status, PDO::PARAM_INT);
            $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
            
            //$stmt->debugDumpParams();
           
           
            try {
                return $stmt->execute();
            } catch (Exception $th) {
                return false;
            }
        }
        return false;
    }    

    /**
     * Selecciona todos los tiempos del sistema
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectTiempos()
    {
        $sql = 'SELECT resultados.id, resultados.identificacion, resultados.prueba_id, resultados.tiempo, resultados.torneo, resultados.fecha_torneo, nadadores.apellido, nadadores.nombre AS nada, pruebas.nombre
        FROM resultados
        INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip
        INNER JOIN pruebas ON resultados.prueba_id = pruebas.id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Elimina el tiempo seleccinado
     *
     * @return void
     */
    public static function deleteTime($id)
    {
        $sql = 'DELETE FROM resultados
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        
        return $stmt->execute();
    }

    /**
     * Actualizar el tiempo del
     * NAdador para el Ranking
     * 
     * 
     */
    public static function updateTimeGeneral($data)
    {
        $updated_at = time();

        $id = $data['id_inscripcion'];

        $tiempo = $data['hora'] . ':' . $data['minutos'] . ':' . $data['segundos'] . '.' . $data['centesimas'];

        $sql = 'UPDATE resultados
                    SET tiempo = :tiempo
                    ';
        $sql .= "\nWHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        //$stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);

        //$stmt->debugDumpParams();

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Encontrar todas las módulos que existen en el sistema
     *
     *
     * @return mixed User object if found, false otherwise
     */
    public static function selectModulos()
    {
        $sql = 'SELECT * FROM modules';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encuentra los permisos del rol seleccionado
     *
     *
     * @return mixed User object if found, false otherwise
     */
    public static function permisosRol($rol)
    {
        $sql = 'SELECT * FROM permissions
        INNER JOIN modules  ON permissions.module_id = modules.id
         WHERE role_id  = :role_id ';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':role_id', $rol, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $th) {
            return array();
        }        
    }

    /**
     * Asignar Modulo a un rol
     *
     * 
     * 
     */
    public static function asignarRol($id_modulo, $id_rol)
    {
        $sql = 'INSERT INTO permissions (role_id, module_id, r, w, u, d)
            VALUES (:role_id, :module_id, :r, :w, :u, :d)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':role_id', $id_rol, PDO::PARAM_INT);
        $stmt->bindValue(':module_id', $id_modulo, PDO::PARAM_INT);
        $stmt->bindValue(':r', 1, PDO::PARAM_INT);
        $stmt->bindValue(':w', 1, PDO::PARAM_INT);
        $stmt->bindValue(':u', 1, PDO::PARAM_STR);
        $stmt->bindValue('d', 1, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina el modulo asignado
     *
     * @return void
     */
    public static function quitarRol($id_modulo, $id_rol)
    {
        $sql = 'DELETE FROM permissions
                WHERE role_id = :role_id AND module_id = :module_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':role_id', $id_rol, PDO::PARAM_INT);
        $stmt->bindValue(':module_id', $id_modulo, PDO::PARAM_INT);

        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }





}
