<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

ini_set('date.timezone', 'America/Bogota');

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        
        foreach($data as $key => $value){
            $this->$key = $value;            
        }       
    }
    
    /**
     * Metodo que guarda los datos recibidos del usuario
     * @return void
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (user_name, first_name, last_name, email, role_id, api_key, password_hash, created_at, updated_at, activation_hash)
                    VALUES (:user_name, :first_name, :last_name, :email, :role_id, :api_key, :password_hash, :created_at, :updated_at, :activation_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_name', $this->user_name, PDO::PARAM_STR);
            $stmt->bindValue(':first_name', $this->first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':role_id', 3, PDO::PARAM_STR);
            $stmt->bindValue(':api_key', $hashed_token, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            

            return $stmt->execute();
        }
        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // NOmbre de Usuario
        if (isset($this->user_name)) {
            if ($this->user_name == '') {
                $this->errors['user_name'] = 'Identificación de usurio requerida';
            }
        }        

        if (preg_match('/^[0-9]{3,16}$/', $this->user_name) == 0) {
            $this->errors['error_user'] = 'La identificación del usuario deben ser solo numeros';
        }

        // Nombres
        if ($this->first_name == '') {
            $this->errors['first_name'] = 'Debe contener al menos un nombre';
        }

        if (preg_match('/^[a-zñ A-ZÑ\']{3,100}$/', $this->first_name) == 0) {
            $this->errors['error_letran'] = 'El nombre debe contener solo letras';
        }

        // Apellidos
        if ($this->last_name == '') {
            $this->errors['last_name'] = 'Debe contener al menos un apellido';
        }

        if (preg_match('/^[a-zñ A-ZÑ\']{3,100}$/', $this->first_name) == 0) {
            $this->errors['error_letrap'] = 'El apellido debe contener solo letras';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['email'] = 'Correo Electrónico no valido';
        }

        if ($this->emailExists($this->email, $this->id ?? null)) {
            $this->errors['validacion'] = 'Correo electrónico o usuario ya existe';
        }

        if ($this->userExists($this->user_name, $this->id ?? null)) {
            $this->errors['validacion1'] = 'Correo electrónico o usuario ya existe';
        }

        if (isset($this->password)) {
        
            if (strlen($this->password) < 8) {
                $this->errors['longitud_pass'] = 'Por favor ingrese una contraseña de al menos 8 caracteres';
            }

            if (preg_match('/.*[a-zñ]+.*/i', $this->password) == 0) {
                $this->errors['letra_pass'] = 'La contraseña requiere al menos una letra';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors['numero_pass'] = 'Password requiere al menos un número';
            }
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
      $user = static::findByEmail($email);

        if ($user) {
          if ($user->id != $ignore_id) {
              return true;
          }
      }

      return false;
    }

    public static function userExists($user_name, $ignore_id = null)
    {
        
        $user = static::findByUser($user_name);

        if ($user) {
          if ($user->id != $ignore_id) {
              return true;
          }
      }

      return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Find a user model by User_name
     *
     * @param string $user name
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByUser($user_name)
    {
        $sql = 'SELECT * FROM users WHERE user_name = :user_name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();   
    }

    /**
     * Busca el usuario y si existe y esta activo
     * verifica que la clave sea la que esta en la BD
     * Si es así retorna todos los datos del usuario.
     *
     * @param string $user_name y $password The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function authenticate($user_name, $password)
    {
        $user = static::findByUser($user_name);

        if ($user && $user->is_active) {
            if (password_verify($password, $user->password_hash)) 
            {
                return $user;
            }
        }
        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 10;  // 10 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions to the user specified
     *
     * @param string $email The email address
     *
     * @return void
     */
    public static function sendPasswordReset($email)
    {
        $user = static::findByEmail($email);

        if ($user) 
        {
            if ($user->startPasswordReset()) {
                $user->sendPasswordResetEmail(); 
                return true;
            }
        }
        return false;
    }

    /**
     * Start the password reset process by generating a new token and expiry
     *
     * @return void
     */
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2;  // 2 hours from now

        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions in an email to the user
     *
     * @return void
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]) . '/password/reset/' . $this->password_reset_token;        

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url, 'nombre' => $this->first_name]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url, 'nombre' => $this->first_name]);

        Mail::send($this->email, $this->first_name, 'Recuperación de contraseña - Ecoapplet', $text, $html);
    }

    /**
     * Find a user model by password reset token and expiry
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users
                WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {

            // Check password reset token hasn't expired
            if (strtotime($user->password_reset_expires_at) > time()) {

                return $user;

            }
        }
    }

    /**
     * Reset the password
     *
     * @param string $password The new password
     *
     * @return boolean  True if the password was updated successfully, false otherwise
     */
    public function resetPassword($password)
    {
        $this->password = $password;

        $this->validate();

        if (empty($this->errors)) {

          $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

          $sql = 'UPDATE users
                  SET password_hash = :password_hash,
                      password_reset_hash = NULL,
                      password_reset_expires_at = NULL
                  WHERE id = :id';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
          $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

          return $stmt->execute();
      }

      return false;
    }

    /**
     * Send an email to the user containing the activation link
     *
     * @return void
     */
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]) . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url, 'nombre' => $this->first_name]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url, 'nombre' => $this->first_name]);

        if (Mail::send($this->email, $this->first_name, 'Activación de la cuenta - Ecoapplet', $text, $html)) {
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * Activate the user account with the specified activation token
     *
     * @param string $value Activation token from the URL
     *
     * @return void
     */
    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();                
    }

    /**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateProfile($data, $img)
    {
        if ($data['user_name'] != $_SESSION['user_name']) {
            $this->user_name = $data['user_name'];
        }
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->email = $data['email'];
        
        // Only validate and update the password if a value provided
        if ($data['password'] != '') {
            $this->password = $data['password'];
        }

        if ($data['role_id'] != '') {
            $this->role_id = $data['role_id'];
        }

        if ($data['club_id'] != '') {
            $this->club_id = $data['club_id'];
        }

        if (!isset($data['is_active']) ? false : true) {
            $this->is_active = $data['is_active'];
        }

        if ($img != '') {            
           
            $this->userimg = $img;
        }


        $this->validate();        

        if (empty($this->errors)) {
            $updated_at = time();

            //$this->updated_at = $updated_at;

            $sql = 'UPDATE users
                    SET user_name = :user_name,
                        first_name = :first_name,
                        last_name = :last_name,
                        email = :email,
                        updated_at = :updated_at
                    ';                        

            if (isset($this->password)) {
                $sql .= ', password_hash = :password_hash';
            }
            
            if (isset($this->role_id)) {
                $sql .= ', role_id = :role_id';
            }

            if (isset($this->club_id)) {
                $sql .= ', club_id = :club_id';
            }

            if (isset($this->is_active)) {
                $sql .= ', is_active = :is_active';
            }

            if (isset($this->userimg)) {
                $sql .= ', userimg = :userimg';
            }

            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_name', $this->user_name, PDO::PARAM_STR);
            $stmt->bindValue(':first_name', $this->first_name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':last_name', $this->last_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);
            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            if (isset($this->role_id)) {                
                $stmt->bindValue(':role_id', $this->role_id, PDO::PARAM_INT);
            }

            if (isset($this->club_id)) {                
                $stmt->bindValue(':club_id', $this->club_id, PDO::PARAM_INT);
            }

            if (isset($this->is_active)) {                
                $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_INT);
            }

            if (isset($this->userimg)) {                
                $stmt->bindValue(':userimg', $this->userimg, PDO::PARAM_STR);
            }

            //$stmt->debugDumpParams();
            $_SESSION['user_name'] = $this->user_name;

            return $stmt->execute();            
        }        
        return false;
        
    }

    /**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateNadador($data, $img)
    {
        $this->id = $data['id_nadador'];
        $updated_at = time();          

        $sql = 'UPDATE nadadores
                    SET nadaimg = :nadaimg,                        
                        updated_at = :updated_at
                    ';
            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':nadaimg', $img, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);            
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);
            

            return $stmt->execute();            
    }

    /**
     * Encontrar los permisos de un usuario
     * logueado según si rol ID
     *
     * @param string $role_id The user rol DI
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findPermissionsByUserID($role_id)
    {
        $sql = 'SELECT * FROM permissions WHERE role_id = :role_id ';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }   

}
