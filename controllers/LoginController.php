<?php

namespace Controllers;


use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){
        $alertas=[];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //verificar si el usuario existe
                $usuario = Usuario::where('email',$auth->email);
                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                }else{
                    //el usuario existe
                    if ( password_verify($_POST['password'], $usuario->password) ){
                        //Inicio session
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        #debuguear($_SESSION);
                        header('Location: /dashboard');
                    }else{
                        Usuario::setAlerta('error','Password incorrecto');
                    }
                    
                }
            }
        }

        $alertas=Usuario::getAlertas();
        //renderizar vista
        $router->render('auth/login',[
            'titulo' => 'Login',
            'alertas' => $alertas
        ]);

    }

    public static function logout(){
        session_start();
        $_SESSION=[];
        session_destroy();
        header('Location: /');
    }

    public static function crear(Router $router){
        $usuario = new Usuario;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            
            if (empty($alertas)) {
                $existeUsuario=Usuario::where('email', $usuario->email);
                //debuguear($existeUsuario);
                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas= Usuario::getAlertas();
                }else{
                    //hashear password
                    $usuario->hashPassword();

                    //eliminar password2
                    unset($usuario->password2);

                    //generar token
                    $usuario->crearToken();
                    $usuario->confirmado = 0;

                    //Crear un nuevo usuario
                    //debuguear($usuario);
                    $resultado=$usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    //debuguear($email);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        //renderizar vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas=[];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //buscamos al usuario con el email
                $usuario = Usuario::where('email', $usuario->email);
                //debuguear($usuario);
                if ($usuario && $usuario->confirmado) {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    //debuguear($email);

                    $email->reestablecerPass();

                    //imprimir alerta
                    Usuario::setAlerta('exito', 'Se enviaron las instrucciones a tu correo');
                
                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas=Usuario::getAlertas();
        //renderizar la vista
        $router->render('auth/olvide',[
            'titulo' => 'Recupera tu contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){
        $token=s($_GET['token']);
        $alertas=[];
        $mostrar=true;

        if(!$token){
            header('Location: /');
        }

        $usuario=Usuario::where('token', $token);
        //debuguear($usuario);
        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $mostrar=false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //añadir el nuevo password
            $usuario->sincronizar($_POST);

            //validar password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                //hashear el nuevo password
                $usuario->hashPassword();
                unset($usuario->password2);

                //borrar el token
                $usuario->token=null;
                
                //guardar el usuario
                $resultado=$usuario->guardar();

                //redireccionar
                if ($resultado) {
                    header('Location: /');
                }

            }
        }

        $alertas = Usuario::getAlertas();
        //renderizar la vista
        $router->render('auth/reestablecer',[
            'titulo' => 'Reestablece tu contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        
        //renderizar la vista
        $router->render('auth/mensaje',[
            'titulo' => 'Mensaje'
        ]);
    }

    public static function confirmar(Router $router){
        //se obtiene el token
        $token=s($_GET['token']);
        $alertas=[];

        //si no existe se redirecciona al login
        if (!$token) {
            header('Location: /');
        }

        //se busca al usuario con el token
        $usuario=Usuario::where('token', $token);
        //debuguear($usuario);

        //se valida el token
        if (empty($usuario)) {
            $alertas=Usuario::setAlerta('error','Token incorrecto');
        }else{
            $alertas=Usuario::setAlerta('exito','Cuenta Confirmada');
            unset($usuario->password2);
            $usuario->confirmado = 1;
            $usuario->token = null;
            //debuguear($usuario);
            $usuario->guardar();
        }

        $alertas=Usuario::getAlertas();
        //renderizar la vista
        $router->render('auth/confirmar',[
            'titulo' => 'Mensaje',
            'alertas' => $alertas
        ]);
    }

}


?>