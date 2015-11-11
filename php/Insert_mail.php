<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '/volume1/web/DAO/Querys.php';

class Insert_mail {
    public function __construct() {        
        $this->get_ajax();
    }
    private function get_ajax()
    {
       //        echo var_dump($_POST);
        $id_correo=0;
        $server=$_POST['correo_comun'];
        $titulo_mostrar=$_POST['nombre'];       
        $id=$_POST['id'];
        //Cuando los usuarios seleccionan un correo empresarial
        if($server=="otro")
        {
            $server_empresa=$_POST['correo_empresa'];
            $seguridad=$_POST['seguridad'];
            $puerto=$_POST['puerto'];
            $smtp=$_POST['smtp'];
            $correo_empresa=$_POST['correo_empresa'];
            $password=$_POST['password_empresa'];    
            $id_correo=$this->insert_mail_empresa($id, $puerto, $seguridad, "true", $server_empresa, $smtp, $correo_empresa, $password,$titulo_mostrar);
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
            
            $password=$_POST['password_comun'];
            $correo=$_POST['usuario'];
            $correo.="@".$arrova;      
        
            echo "<p>password=$password</p>";
            echo "<p>$correo</p>";
            echo var_dump($array);
            echo "<p>$titulo_mostrar</p>";
            $id_correo=$this->insert_mail_comun($id, $array,$correo, $password,$titulo_mostrar);
        }
        $this->insert_correo_into_login($id, $id_correo);
    }
    
     private function insert_mail_comun($id_empleado,$array,$correo,$password,$titulo_mostrar)
    {
        $smtp=$array['smtp'];
        $puerto=$array['puerto'];
        $seg=$array['seg'];
        $auth=$array['auth'];
        $server=$array['server'];
        $Querys=new Querys();
        $conexion=$Querys->Conexion();
        $BD="CFDI";
        mysql_select_db($BD,$conexion);
        $q="INSERT INTO correo (id_empleado, servidor, smtp, puerto, seguridad, auth,correo, password,titulo_mostrar)
            VALUES ($id_empleado, '$server', '$smtp',$puerto, '$seg', '$auth','$correo','$password','$titulo_mostrar')";
        $resultado = mysql_query($q);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                echo($mensaje);
            }
        $ultimo_id = mysql_insert_id($conexion); 
        mysql_close($conexion);
        return $ultimo_id;
    }
    
     private function insert_mail_empresa($id_empleado,$puerto,$seg,$auth,$server,$smtp,$correo,$password,$titulo_mostrar)
    {       
        $Querys=new Querys();
        $conexion=$Querys->Conexion();
        $BD="CFDI";
        mysql_select_db($BD,$conexion);
        $q="INSERT INTO correo (id_empleado, servidor, smtp, puerto, seguridad, auth, correo, password, titulo_mostrar)
            VALUES ($id_empleado, '$server', '$smtp',$puerto, '$seg', '$auth','$correo','$password','$titulo_mostrar')";
        $resultado = mysql_query($q);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                echo($mensaje);
            }            
        $ultimo_id = mysql_insert_id($conexion); 
        mysql_close($conexion);
        return $ultimo_id;
    }
    
    /*
     * Se inserta el id del correo en la tabla login correspondiente al usuario
     */
     private function insert_correo_into_login($id_login,$id_correo)
    {       
        $Querys=new Querys();
        $conexion=$Querys->Conexion();
        $BD="CFDI";
        mysql_select_db($BD,$conexion);
        $q="UPDATE login SET id_correo=$id_correo WHERE id_login=$id_login";
        $resultado = mysql_query($q);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                echo($mensaje);
            }           
        mysql_close($conexion);
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
$insert_mail=new Insert_mail();
?>
