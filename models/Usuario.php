<?php 

namespace Model;
use Model\ActiveRecord;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB=['id', 'nombre', 'email', 'password',
    'token', 'confirmado'];

    public function __construct($args = [])
    { 
        $this->id =$args['id'] ?? null;
        $this->nombre =$args['nombre'] ?? '';
        $this->email =$args['email'] ?? '';
        $this->password =$args['password'] ?? '';
        $this->password2 =$args['password2'] ?? null;
        $this->password_actual =$args['password_actual'] ?? null;
        $this->password_nuevo =$args['password_nuevo'] ?? null;
        $this->token =$args['token'] ?? '';
        $this->confirmado =$args['confirmado'] ?? null;
    }

    // Validar las cuentas
    public function validarNuevaCuenta(){
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }else if (!$this->email) {
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }else if (!$this->password) {
            self::$alertas['error'][] = 'El password del usuario es obligatorio';
        }else if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password del usuario debe tener al menos 6 caracteres';
        }else if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords no coinciden';
        }

        return self::$alertas;
    }

    //validar email
    public function validarEmail(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
        }
        return self::$alertas;
    }

    //hashear el password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //validar password
    public function validarPassword(){
        if (!$this->password) {
            self::$alertas['error'][] = 'El password del usuario es obligatorio';
        }else if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password del usuario debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }else if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password del usuario es obligatorio';
        }
        return self::$alertas;
    }

    public function validarPerfil(){
        if (!$this->nombre) {
            self::$alertas['error'][]='El nombre es oblogatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][]='El email es oblogatorio';
        }
        return self::$alertas;
    }

    public function nuevo_password(){
        if (!$this->password_actual) {
            self::$alertas['error'][]='El password actual es oblogatorio';
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][]='El password nuevo es oblogatorio';
        }
        if (strlen($this->password_nuevo)<6) {
            self::$alertas['error'][]='El password nuevo debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function comprobar_password(){
        return password_verify($this->password_actual, $this->password);
    }

    public function crearToken(){
        $this->token = uniqid(); // o con md5(uniqid()) pero regresa un string de 32
    }


}


?>