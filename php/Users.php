<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author Daniel
 */
require_once 'DataBase.php';
require_once 'XML.php';
class Users {
    public function __construct() {
        $this->Ajax();
    }
    
    private function Ajax()
    {
        $option = filter_input(INPUT_POST, "option");
        switch ($option)
        {
            case 'NewUser': $this->NewUser(); break;
            case 'UserList': $this->UserList(); break;
            case 'GetUserInfo': $this->GetUserInfo(); break;
            case 'ModifyUser': $this->ModifyUser(); break;
            case 'DeleteUser': $this->DeleteUser(); break;
        }
    }
    
    private function DeleteUser()
    {
        $DB = new DataBase();
        $IdUser = filter_input(INPUT_POST, "IdUser");
        
        $Delete = "DELETE FROM Usuarios WHERE IdUsuario = $IdUser";
        if(($ResultDelete = $DB->ConsultaQuery("CFDI", $Delete))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al elminar el usuario</p><br>Detalles:<br><br>$ResultDelete");
            return 0;
        }
        
        XML::XmlResponse("DeletedUser", 1, "Usuario eliminado");
    }
    
    private function ModifyUser()
    {
        $DB = new DataBase();
        
        $nombre_usuario=$_POST['nombre_usuario'];
        $password=$_POST['password'];
        $nombre=$_POST['nombre'];
        $apellido_paterno=$_POST['apellido_paterno'];
        $apellido_materno=$_POST['apellido_materno'];
        $curp=$_POST['curp'];
        $fecha_nac=$_POST['fecha_nac'];
        $id_login=$_POST['id_login'];
        
        $Update="UPDATE Usuarios SET nombre_usuario='$nombre_usuario', nombre='$nombre', apellido_paterno='$apellido_paterno'
            , apellido_materno='$apellido_materno', curp='$curp', fecha_nac='$fecha_nac', password='$password'
            WHERE IdUsuario=$id_login";
        
        if(($ResultUpdate = $DB->ConsultaQuery("CFDI", $Update))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al actualizar la informacion del usuario</p><br>Detalles:<br><br>$ResultUpdate");
            return 0;
        }
        
        XML::XmlResponse("ModifyUser", 1, "Informacion Actualizada");
        
    }
    
    private function GetUserInfo()
    {
        $DB = new DataBase();
        $IdUser = filter_input(INPUT_POST, "IdUser");
        
        $Select = "SELECT *FROM Usuarios WHERE IdUsuario = $IdUser";
        
        $ResultSelect =$DB->ConsultaSelect("CFDI", $Select);
        if($ResultSelect['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al consultar la información del usuario</p><br>Detalles<br><br>".$ResultSelect['Estado']);
            return 0;
        }
        
        $User = $ResultSelect['ArrayDatos'];
        
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('UserInfo');
        $doc->appendChild($root);
        for ($cont = 0;$cont < count($User); $cont++)
        {
            $usuario=$doc->createElement('User');
            $id_login=$doc->createElement('IdUser',$User[$cont]['IdUsuario']);
            $usuario->appendChild($id_login);
            $nombre_usuario=$doc->createElement('UserName',$User[$cont]['nombre_usuario']);
            $usuario->appendChild($nombre_usuario);
            $nombre_=$doc->createElement('Name',$User[$cont]['nombre']);
            $usuario->appendChild($nombre_);
            $password=$doc->createElement('password',$User[$cont]['password']);
            $usuario->appendChild($password);
            $apellido_paterno=$doc->createElement('LastName',$User[$cont]['apellido_paterno']);
            $usuario->appendChild($apellido_paterno);
            $apellido_materno=$doc->createElement('MLastName',$User[$cont]['apellido_materno']);
            $usuario->appendChild($apellido_materno);
            $curp=$doc->createElement('curp',$User[$cont]['curp']);
            $usuario->appendChild($curp);
            $fecha_nac=$doc->createElement('DateBorn',$User[$cont]['fecha_nac']);
            $usuario->appendChild($fecha_nac);
            $fecha_alta=$doc->createElement('RegistrationDate',$User[$cont]['fecha_alta']);
            $usuario->appendChild($fecha_alta);
            $root->appendChild($usuario);
        }
        header ("Content-Type:text/xml");
    //        $doc->save('/volume1/public/login.xml');
        echo $doc->saveXML();
    }
    
    private function UserList()
    {
        $DB = new DataBase();
        $SelectUsers = "SELECT * FROM Usuarios";
        $ResultSelect = $DB->ConsultaSelect("CFDI", $SelectUsers);
        if($ResultSelect['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al consultar los usuarios</p><br>Detalles:<br><br>".$ResultSelect['Estado']);
            return 0;
        }
        
        $Users = $ResultSelect['ArrayDatos'];
        
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;   
        $root = $doc->createElement('UserList');
        $doc->appendChild($root);
        for ($cont = 0; $cont < count($Users); $cont++)
        {        
    //        echo $valor['id_login'];
    //        echo $valor['nombre_usuario'];
    //        echo $valor['nombre'];
    //        echo $valor['apellido_paterno'];
    //        echo $valor['apellido_materno'];
    //        echo $valor['curp'];
    //        echo $valor['fecha_nac'];
            $usuario=$doc->createElement('User');
            $id_login=$doc->createElement('IdUser',$Users[$cont]['IdUsuario']);
            $usuario->appendChild($id_login);
            $nombre_usuario=$doc->createElement('UserName',$Users[$cont]['nombre_usuario']);
            $usuario->appendChild($nombre_usuario);
            $nombre_=$doc->createElement('Name',$Users[$cont]['nombre']);
            $usuario->appendChild($nombre_);
            $apellido_paterno=$doc->createElement('LastName',$Users[$cont]['apellido_paterno']);
            $usuario->appendChild($apellido_paterno);
            $apellido_materno=$doc->createElement('MLastName',$Users[$cont]['apellido_materno']);
            $usuario->appendChild($apellido_materno);
            $curp=$doc->createElement('curp',$Users[$cont]['curp']);
            $usuario->appendChild($curp);
            $fecha_nac=$doc->createElement('DateBorn',$Users[$cont]['fecha_nac']);
            $usuario->appendChild($fecha_nac);
            $fecha_alta=$doc->createElement('RegistrationDate',$Users[$cont]['fecha_alta']);
            $usuario->appendChild($fecha_alta);
            $root->appendChild($usuario);
        }
            header ("Content-Type:text/xml");
            echo $doc->saveXML();
        
    }
    
    private function NewUser()
    {
        $DB = new DataBase();
        
        $nombre = filter_input(INPUT_POST, "nombre");
        $apellido_p = filter_input(INPUT_POST, "apellido_p");
        $apellido_m = filter_input(INPUT_POST, "apellido_m");
        $fecha_nac = filter_input(INPUT_POST, "fecha_nac");
        $curp = filter_input(INPUT_POST, "curp");
        $usuario = filter_input(INPUT_POST, "usuario");
        $password = filter_input(INPUT_POST, "password");
        
        $Repeated = "SELECT * FROM Usuarios WHERE nombre_usuario COLLATE utf8_bin = '$usuario'";
        $ResultRepetaed = $DB->ConsultaSelect("CFDI", $Repeated);
        if($ResultRepetaed['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al comprobar que el usuario no estuviera registrado previamente</p><br>Detalles:<br><br>".$ResultRepetaed['Estado']);
            return ;
        }               
        
        if(count($ResultRepetaed['ArrayDatos'])>0)
        {            
            XML::XmlResponse("RepeatedUser", 1, "El usuario '$nombre' ya existe");
            return 0;
        }
        
        $Insert = "INSERT INTO Usuarios (nombre_usuario,password,nombre,apellido_materno,apellido_paterno,curp,fecha_nac,tipo_usuario,fecha_alta)
        VALUES ('$usuario' ,'$password', '$nombre','$apellido_m','$apellido_p','$curp','$fecha_nac','usuario',now())";
        
        
        if(($ResultInsert = $DB->ConsultaQuery("CFDI", $Insert))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al registrar el nuevo usuario</p><br>Detalles:<br><br>$ResultInsert");
            return 0;
        }
        
        XML::XmlResponse("NewUser", 1, "El usuario '$usuario' se dio de alta con éxito");
                
    }
    
}

$Users = new Users();
