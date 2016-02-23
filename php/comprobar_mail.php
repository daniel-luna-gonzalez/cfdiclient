<?php
/*
 * Esta clase es llamada de alta_sistema.js para probar la configuración del correo electrónico
 */
include '/volume1/web/DAO/Querys.php';

class comprobar_mail {
    //put your code here
    public function __construct() {
        $this->get_ajax();
    }
    private function get_ajax()
    {
//        echo var_dump($_POST);
        $server=$_POST['correo_comun'];
        $titulo_nombre=$_POST['nombre'];        
        //Cuando los usuarios seleccionan un correo empresarial
        if($server=="otro")
        {
            $seguridad=$_POST['seguridad'];
            $puerto=$_POST['puerto'];
            $smtp=$_POST['smtp'];
            $correo_empresa=$_POST['correo_empresa'];
            $password=$_POST['password_empresa'];
            $this->prueba_mail($titulo_nombre, $password, $smtp, $seguridad, $puerto, $correo_empresa);
        }
        else
        {
            $array=array();
            $arrova="";
            if($server=="hotmail")
            {
//                echo "<p>hotmail</p>";
                $arrova="hotmail.com";
                $array=  $this->hotmail();
            }
            if($server=="live")
            {
//                 echo "<p>live</p>";
                 $arrova="live.com";
                 $array= $this->live();                    
            }
            if($server=="yahoo")
            {
//                 echo "<p>yahoo</p>";
                 $arrova="yahoo.com.mx";
                 $array=  $this->yahoo();
            }
            if($server=="gmail")
            {
//                 echo "<p>gmail</p>";
                 $array=  $this->gmail();
                 $arrova="gmail.com";
            }
            $password=  utf8_decode($_POST['password_comun']);
            $correo=  utf8_decode($_POST['usuario']);
            $correo.="@".$arrova;
            $smtp=$array['smtp'];
            $puerto=$array['puerto'];
            $seg=$array['seg'];
            $auth=$array['auth'];      
                       
            $this->prueba_mail($titulo_nombre,$password, $smtp,$seg,$puerto, $correo);
        }
        //Cuando el usuario selecciona un correo común (Hotmail, Yahoo, Gmail y Live)        
    }          
        
    function prueba_mail($remitente_nombre,$password,$smtp,$seguridad,$puerto,$remitente_mail)
    {
        require '/volume1/web/usuario/php/PHPMailer/PHPMailerAutoload.php'; 
        
//         echo "<p>correo = $remitente_mail</p>";
//         echo "<p>password = $password</p>";
//            echo "<p>smtp = $smtp</p>";
//            echo "<p>seguridad = $seguridad</p>";
//            echo "<p>puerto = $puerto</p>";
//            echo "<p>auth = $auth</p>";
        $mail = new PHPMailer;

        $mail->isSMTP();           
//        $mail->SMTPDebug = 1;// Set mailer to use SMTP
        $mail->CharSet = 'UTF-8';
        $mail->Host = $smtp;  // Specify main and backup server
        $mail->SMTPAuth = true;                              // Enable SMTP authentication
        $mail->Username = $remitente_mail;                            // SMTP username
        $mail->Password = $password;                        // SMTP password
        if($seguridad!=null or $seguridad!="")
        {
            $mail->SMTPSecure = $seguridad;                            // Enable encryption, 'ssl' also accepted            
        }
        $mail->Port=$puerto;


        $mail->From = $remitente_mail;
        $mail->FromName = $remitente_nombre;
        $mail->addAddress($remitente_mail, $remitente_nombre);  // Add a recipient
        $mail->AddEmbeddedImage("/volume1/web/usuario/res/img/CSDocs.png","CS-DCOS", "CS-DOCS");
    //        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = "Alta de registro CFDI CS-DOCS";
        $mail->Body    ="<FONT SIZE=10px><b>$remitente_nombre</b> le damos la
            más cordial bienvenida, la configuración de su correo fué realizada con éxito, ahora ya puede hacer uso
            de <b>CS-DOCS CFDI</b>.</FONT>  
                    ";
//        $mail->AltBody = 'Texto enviado desde PHP maquina Daniel - PC';

        if(!$mail->send()) {          
//           echo 'Error: ' . $mail->ErrorInfo;  
           $this->respuesta_mail(0, 'Error: '  . $mail->ErrorInfo);
        }
        else
        {
            $this->respuesta_mail(1, '¡Configuración de correo realizada con éxito!');
        }
    }
        
        private function respuesta_mail($tipo,$mensaje)
    {
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Mail');
        $doc->appendChild($root);
        
        $detalle=$doc->createElement("tipo",$tipo);
        $root->appendChild($detalle);
        $mensaj=$doc->createElement('mensaje',$mensaje);
        $root->appendChild($mensaj);
        
        header ("Content-Type:text/xml");        
//        $doc->save('/volume1/public/mail.xml');        
        echo $doc->saveXML();                
    }
    private function hotmail()
    {
        $smtp="smtp-mail.outlook.com";
        $puerto=587;
        $seg="tls";
        $auth="true";
        $server="hotmail";
        $array= array("smtp"=>$smtp,"puerto"=>$puerto,"auth"=>$auth,"seg"=>$seg,"server"=>$server);
        return $array;
    }
    private function live()
    {   
        $array= array("smtp"=>"smtp-mail.outlook.com","puerto"=>587,"auth"=>"true","seg"=>"tls","server"=>"live");
        return $array;
    }
    private function gmail()
    {        
        $smtp="smtp.gmail.com";
        $puerto=465;
        $seg="ssl";
        $auth="true";
        $server="gmail";
        $array= array("smtp"=>$smtp,"puerto"=>$puerto,"auth"=>$auth,"seg"=>$seg,"server"=>$server);
        return $array;
    }
    private function yahoo()
    {
        $smtp="plus.smtp.mail.yahoo.com";
        $puerto=465;
        $seg="ssl";
        $auth="true";
        $server="yahoo";
        $array= array("smtp"=>$smtp,"puerto"=>$puerto,"auth"=>$auth,"seg"=>$seg,"server"=>$server);
        return $array;
    }
}
$comprobar_mail=new comprobar_mail();
?>
