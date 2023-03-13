<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController{

    public static function index(Router $router){
        session_start();
        isAuth();

        $id=$_SESSION['id'];
        $proyectos = Proyecto::belongsTo('usuarioId', $id);
        #debuguear($proyectos);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        session_start();
        $alertas=[];
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto=new Proyecto($_POST);
            
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                #generamos una url unica
                $proyecto->url = md5(uniqid());

                #almacenamos al creador del proyecto
                $proyecto->usuarioId = $_SESSION['id'];
                #debuguear($proyecto);

                #guardamos proyecto
                $proyecto->guardar();

                #redireccion
                header('Location: /proyecto?url='.$proyecto->url);
            }
        }

        $router->render('dashboard/crear_proyecto', [
            'titulo' => 'Crear proyecto',
            'alertas' => $alertas
        ]);
    }

/*  public static function editar_proyecto(Router $router){
        $router->render('', [
        ]);
    }*/
    public static function eliminar_proyecto(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id=$_POST["id"];
            $proyecto = Proyecto::find($id);

            if ($proyecto) {
                $resultado=$proyecto->eliminar();
                //debuguear($resultado);
                header("Location: dashboard");
            }
        }
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();

        $alertas=[];

        $usuario = Usuario::find($_SESSION['id']);

        //debuguear($usuario);

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $usuario->sincronizar($_POST);

            $alertas= $usuario->validarPerfil();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    //mensaje de error
                    Usuario::setAlerta('error','Este email ya existe');
                }else{
                    //Guardamos el usuario
                    $usuario->guardar();
                    //asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                    Usuario::setAlerta('exito','Se actualizo correctamente');
                }
            }
        }

        $alertas=$usuario->getAlertas();
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        $alertas=[];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if (empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if ($resultado) {
                    $usuario->password = $usuario->password_nuevo;
                    
                    //elimino propiedades innesesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    //hashear el nuevo password
                    $usuario->hashPassword();
                    //debuguear($usuario);
                    //asignar password
                    $resultado=$usuario->guardar();
                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Password actualizado correctamente');
                        $alertas=$usuario->getAlertas();
                    }
                }else{
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas=$usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar password',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        #Revisar que la persona que visita el proyecto sea quien la creo
        $token=$_GET['url'];
        if (!$token) {
            header('Location: /dashboard');
        }

        $proyecto=Proyecto::where('url', $token);
        #debuguear($proyecto);
        if ($proyecto->usuarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }


        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }
}