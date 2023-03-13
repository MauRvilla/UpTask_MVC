<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;
    

    public function __construct($email,$nombre,$token){
        $this->email=$email;
        $this->nombre=$nombre;
        $this->token=$token;
    }

    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        //$mail->Host = 'smtp.mailtrap.io';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        //$mail->Port = 2525;
        $mail->Port = 587;
        $mail->SMTPSecure='tls';
        //$mail->Username = '2ea48d3467179d';
        //$mail->Password = 'e2926022cfcd66';
        $mail->Username = 'mrvam390@gmail.com';
        $mail->Password = 'escorpion0899*';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta';
        
        $mail->isHTML(TRUE);
        $mail->Charset = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola ".$this->nombre."</trong> Has creado
        tu cuenta en UpTask, solo debes confirmarla en el 
        siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: 
        <a href='127.0.0.1:3000/confirmar?token=".$this->token."'>
        Confirma cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function reestablecerPass(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        //$mail->Host = 'smtp.mailtrap.io';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        //$mail->Port = 2525;
        $mail->Port = 587;
        $mail->SMTPSecure='tls';
        //$mail->Username = '2ea48d3467179d';
        //$mail->Password = 'e2926022cfcd66';
        $mail->Username = 'mrvam390@gmail.com';
        $mail->Password = 'escorpion0899*';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu Password';
        
        $mail->isHTML(TRUE);
        $mail->Charset = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola ".$this->nombre."</strong> Para recuperar tu acceso a UpTask, 
        tienes que acceder al siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: 
        <a href='127.0.0.1:3000/reestablecer?token=".$this->token."'>
        Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no solicitaste este correo, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }


}


?>