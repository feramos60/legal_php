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
class ImpugnaDb extends \Core\Model
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
     * Metodo que crea el club
     * @return void
     */
    public function saveComparendo()
    {

        $sql = 'INSERT INTO comparendos (id_usuario, transito, numCompa, fecha_comp, lugar, vehiculo, servicio, marca, placa, created_at, updated_at, created_by_id, updated_by_id)
                    VALUES (:id_usuario, :transito, :numCompa, :fecha_comp, :lugar, :vehiculo, :servicio, :marca, :placa, :created_at, :updated_at, :created_by_id, :updated_by_id)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_usuario', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':transito', $this->transito, PDO::PARAM_STR);
        $stmt->bindValue(':numCompa', $this->numCompa, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_comp', $this->fecha_comp, PDO::PARAM_STR);
        $stmt->bindValue(':lugar', $this->lugar, PDO::PARAM_STR);
        $stmt->bindValue(':vehiculo', $this->vehiculo, PDO::PARAM_STR);
        $stmt->bindValue(':servicio', $this->servicio, PDO::PARAM_STR);
        $stmt->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $stmt->bindValue(':placa', $this->placa, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);        
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':created_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (Exception $th) {
            return false;
        }
       
    }
}