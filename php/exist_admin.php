<?php

/*
 * Se registra por primera vez un administrador o varios posteriormente al igual que 
 * usuarios del sistema, esta clase devuelve un 1 si existen administradores en el sistema
 * y un 0 si no existen.
 * *******************************************************
 * LLamado a esta clase desde el archivo alta_sistema.js *
 * Método:comprobar_admins();                            *
 * *******************************************************
 */
include '/volume1/web/DAO/Querys.php';
class exist_admin {
    public function __construct() {    
        $this->alta_sistema();
    }
    
    private function alta_sistema()
    {
        //Se comprueba que existan administradores
        if($this->exist_admin())
        {
            echo '1';
        }
        else
        {
            echo '0';
        }
        
    }
    
    private function exist_admin()
    {
        $estado=FALSE;        
        $login=array();
        $Querys=new Querys();
        $conexion=$Querys->Conexion();
        $BD="CFDI";
        mysql_select_db($BD,$conexion);
        $q="SELECT nombre_usuario FROM login WHERE tipo_usuario='admin'";
        $resultado = mysql_query($q);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                echo($mensaje);
            }
         while($fila = mysql_fetch_assoc($resultado))
         {
              $estado=TRUE;
              echo $fila['nombre_usuario'];
         }
            
        mysql_close($conexion);
        return $estado;
    }    
}
$alta=new exist_admin();
?>
