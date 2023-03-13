<?php 

namespace Controllers;
use Model\Proyecto;
use Model\Tarea;

class TareaController{
    public static function index(){
        $proyectoUrl=$_GET['url'];

        if(!$proyectoUrl) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoUrl);

        session_start();
        
        if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
            header('Location: /404');
        }

        $proyectoId=$proyecto->id;

        $tareas=Tarea::belongsTo('proyectoId', $proyectoId);

        //debuguear($tareas);
        echo json_encode(['tareas' => $tareas]);
        
    }

    public static function crear(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $proyectoUrl=$_POST['proyectoUrl'];
            $proyecto = Proyecto::where('url', $proyectoUrl);

            # Valida si existe un proyecto O si el usuario que crea la tarea sea el mismo dueño del proyecto
            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta=[
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al crear la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea=new Tarea($_POST);
            $tarea->proyectoId=$proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta =[
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'La tarea fue creada exitosamente',
                'proyectoId' => $proyecto->id
            ];
            echo json_encode($respuesta);
            
        }
    }

    public static function actualizar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            //validamos el proyecto
            $proyectoUrl=$_POST['url'];
            $proyecto = Proyecto::where('url', $proyectoUrl);

            # Valida si existe un proyecto O si el usuario que crea la tarea sea el mismo dueño del proyecto
            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta=[
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            if ($resultado) {
                $respuesta =[
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'mensaje' => 'Se actualizo correctamente',
                    'proyectoId' => $proyecto->id
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }
        }

    }

    public static function eliminar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $proyectoUrl = $_POST['url'];
            $proyecto=Proyecto::where('url', $proyectoUrl);

            # Valida si existe un proyecto O si el usuario que crea la tarea sea el mismo dueño del proyecto
            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta=[
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al elimiar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado=$tarea->eliminar();

            $resultado=[
                'resultado' => $resultado,
                'mensaje' => 'Eliminado correctamente',
                'tipo' => 'exito'
            ];

            echo json_encode($resultado);
        }

    }
}


?>