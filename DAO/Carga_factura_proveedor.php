<?php

$RoutFile = dirname(getcwd())."/web"; 

if(!file_exists($RoutFile))
    $RoutFile = dirname(getcwd());
else            /* Ejecutando en modo servicio */    
    print ("\n  Path principal $RoutFile");

include $RoutFile.'/Transaction/Read_factura_cliente.php';
include $RoutFile.'/DAO/Querys.php';
include $RoutFile.'/DAO/Insert_pdf.php';
include $RoutFile.'/Transaction/webservice_sat.php';

class Carga_factura_proveedor {
     public $pila=array();
    public $emisor=array();
    public $receptor=array();
    public $detalle=array();
    public $id_emisor=0,$id_receptor=0, $id_detalle=0;
    private $pila_pdf = array();
    private $pila_xml=array();
    private $registro_pdf=array();
//    private $stack_xml=array();
    public $array_xml=array();
    public function __construct() {        
    }
    public function Cachar_Datos($pila)
    {
        $this->pila=$pila;   
        //Se manda a procesamiento la pila de archivos
        $retorno=$this->process_stack();
   
        return $retorno;
    }    
    
    private function process_stack()
    {
        $arr_detalle=array();
        //Ruta en donde se almacenan los XML que se van a procesar
        $ruta_xml="/volume1/Inbox_Factura_proveedor/";    
//      Instancia a la clase que lee los XML
        $read_xml=new Read_factura_cliente();     
        //Recorremos la pila recibida
        for($valor=0;$valor<count($this->pila);$valor++)
        {
            $existe=0;
            $nombre_xml=  $this->pila[$valor];     
            
            print(PHP_EOL.PHP_EOL.' Comienza Procesamiento de ....'.$nombre_xml.PHP_EOL);
            
            //Se comprueba la extensión del archivo
            $extension=strtolower($this->type_extension($nombre_xml));            
            
            //Cuando se encuentren archivos XML son procesados para obtener su información             
            if($extension=="xml"){ 
                if(!strstr($nombre_xml, 'Invalido')==false or !strstr($nombre_xml, 'Existe')==false 
                        or !strstr($nombre_xml, 'ErrorEstructura')==false){
                    print('\n Ya se habia introducido invalido '.$nombre_xml);
                    continue;
                }
                
                $this->array_xml=$read_xml->detalle($ruta_xml, $nombre_xml);
                
                if(!is_array($this->array_xml)){
                    printf('\n xml invalido '.$nombre_xml);  
                    if(file_exists($ruta_xml.$nombre_xml))
                        rename($ruta_xml.$nombre_xml, "/volume1/Inbox_Factura_proveedor/"."ErrorEstructura_".date('m-d-y-H-m-s').'_'.$nombre_xml);            
                    continue;                
                }
                
                if(strlen($this->array_xml['emisor']['rfc'])==0 or strlen($this->array_xml['receptor']['rfc'])==0 or strlen($this->array_xml['timbreFiscalDigital']['UUID'])==0)
                {
                    printf('\n xml invalido '.$nombre_xml);  
                    if(file_exists($ruta_xml.$nombre_xml))
                        rename($ruta_xml.$nombre_xml, "/volume1/Inbox_Factura_proveedor/"."ErrorEstructura_".date('m-d-y-H-m-s').'_'.$nombre_xml);            
                    continue;                
                }
                
                /* Validación de XML */                
                $validador =  new webservice_sat();
                $validacion = $validador->valida_cfdi($this->array_xml['emisor']['rfc'], $this->array_xml['receptor']['rfc'], $this->array_xml['encabezado']['total'], $this->array_xml['timbreFiscalDigital']['UUID']);
                if(!is_object($validacion))
                {
                    printf("\n Fallo en validacion $nombre_xml");
                    rename($ruta_xml.$nombre_xml, "/volume1/Inbox_Factura_proveedor/"."Invalido_".date('mdyHms').'_'.$nombre_xml);
                    continue;
                }                                                               
               
            }
            else
            {//Se guardan en stack los pdf que van llegando
                if($extension=="pdf")
                {
                    array_push($this->pila_pdf, $nombre_xml);
                }
                continue;            
            }        
                                                      
            /* Devuelve el id del emisor si existe sino se aumenta el contador */
            if(($this->id_emisor = $this->exist_emisor())  ==0)
                $this->id_emisor=$this->Insert_emisor(); 
            else
                $existe++;  
            
            if(!($this->id_emisor>0)){
                printf("\n Posible error de lectura $nombre_xml"); 
                return;
                
            }
      
            /* Devuelve el id del receptor si existe sino se aumenta el contador */
            if(($this->id_receptor=$this->exist_receptor())   ==0)
                $this->id_receptor = $this->insert_receptor(); 
            else
                $existe++;

            /*Se obtienen los ids emisor y receptor después de la inserción de los mismo
            * esto pasa sino existian en caso de que existan la condición se ignora  */
            if($this->id_emisor==0)
                $this->id_emisor=$this->id_emisor();
            
            if($this->id_receptor==0)
                $this->id_receptor=  $this->id_receptor();
     
            if(($id_detalle=$this->exist_detalle())==0)
            {                           
                $extension = pathinfo($nombre_xml, PATHINFO_EXTENSION);
                $nombre_acuse = basename($nombre_xml, '.'.$extension);
                $ruta_cfdi=  $this->get_ruta_cfdi();
                $validacion->save($ruta_cfdi.$nombre_acuse."SAT.xml");
                $id_validacion=$this->insert_validacion($validacion,$ruta_cfdi.$nombre_acuse."SAT.xml");
                $ruta=$this->move_cfdi($ruta_xml,$nombre_xml);           
                $this->id_detalle=$this->Insert_detalle($id_validacion,$ruta.$nombre_xml,''); 
                $id_and_xml=array("id"=>$this->id_detalle,"nombre_xml"=>$nombre_xml);
                array_push($this->pila_xml,$id_and_xml);                
            }
            else
                $existe++;     

            if($existe==3)
            {
                $intentos=$this->obtener_intentos($this->id_emisor, $nombre_xml);              
                $this->renombrado_fallido($ruta_xml, $nombre_xml, $intentos);/* Se renombra con Existe_ */
            }                                                            
                                            
            //Al terminar de insertar, se vacia el contenido de cada array
            while(count($this->emisor )) array_pop($this->emisor );
            while(count($this->receptor )) array_pop($this->receptor );
            while(count($this->detalle )) array_pop($this->detalle);
            while(count($arr_detalle)) array_pop($arr_detalle);
            while(count($this->array_xml)) array_pop($this->array_xml);
            $this->id_emisor=0;
            $this->id_receptor=0;
            
        } // fin For 
        
        $this->stack_xml=  $this->pila_xml;
        
        // Al termino del Procesamiento de todos los XML se procesa la pila de PDF
        //para localizar su directorio correspondiente y se inserta en la BD su ruta
        
        if(count($this->stack_xml)==0)
        {
            $this->registro_pdf=  $this->pila_pdf;
        }
        else
        {
            foreach ($this->pila_pdf as $valor)
            {            
                //Se envia la pila donde estan los xml que se lograron insertar en la BD y el pdf a buscar
                $this->search_pdf_in_stack($valor);
            }
        }

        return TRUE;
        
        }#Fin de Método    
        

    function Insert_emisor()
    {      
        print("\n Insertando Emisor: ".$this->array_xml['emisor']['rfc']);
        $Querys= new Querys();
        $conexion=$Querys->Conexion();          
        mysql_select_db('CFDI',  $conexion);
    
        $query='INSERT INTO emisor_factura_proveedor (rfc,nombre)
        VALUES(\''.$this->array_xml['emisor']['rfc'].'\' , \''.$this->array_xml['emisor']['nombre'].'\')';
                
        $resultado=mysql_query($query,$conexion);
        
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $query;
                print("\n".$mensaje);
            }       
            
        $id = mysql_insert_id();
        mysql_close($conexion);
        
         return $id;
    }
    
    function insert_receptor()
    {  
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
                
        $query='INSERT INTO receptor_factura_proveedor
        (rfc,nombre)
        VALUES (\''.$this->array_xml['receptor']['rfc'].'\' , \''.$this->array_xml['receptor']['nombre'].'\')';
         
        $resultado=mysql_query($query,$conexion);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $query;
                printf("\n".$mensaje);
            }                                   
        $id=mysql_insert_id();
        mysql_close($conexion);
        return $id;
    }
    
                
    function Insert_detalle($id_validacion,$ruta_xml, $ruta_pdf)
    {           
        $full = '';
        $Full = $this->GetFullText($this->array_xml, $full);
        
        $rfc = $this->array_xml['receptor']['rfc'];
        $serie = $this->array_xml['encabezado']['serie'];
        $folio = $this->array_xml['encabezado']['folio'];
        $fecha = $this->array_xml['encabezado']['fecha'];
        $formaDePago = $this->array_xml['encabezado']['formaDePago'];
        $subTotal = $this->array_xml['encabezado']['subTotal'];
        $descuento = $this->array_xml['encabezado']['descuento'];
        $total = $this->array_xml['encabezado']['total'];
        $metodoDePago = $this->array_xml['encabezado']['metodoDePago'];
        $tipoDeComprobante = $this->array_xml['encabezado']['tipoDeComprobante'];
        $tipoCambio = $this->array_xml['encabezado']['TipoCambio'];
        $moneda = $this->array_xml['encabezado']['Moneda'];
           
        if(!(is_numeric("$descuento")))
            $descuento = 0;
        if(!(is_numeric("$total")))
            $total = 0;
        if(!(is_numeric("$subTotal")))
            $subTotal = 0;
        if(!(is_numeric("$tipoCambio")))
            $tipoCambio=0;
        
      
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
        
        $query="INSERT INTO detalle_factura_proveedor (id_emisor, id_receptor, id_validacion, rfc_cliente, serie, folio, fecha,
            formaDePago, subTotal, descuento, total, metodoDePago, tipoDeComprobante, TipoCambio, Moneda,ruta_xml, ruta_pdf, Full)
        VALUES ($this->id_emisor,$this->id_receptor, $id_validacion, '$rfc' , '$serie', '$folio', '$fecha',
        '$formaDePago', $subTotal, $descuento, $total, '$metodoDePago','$tipoDeComprobante', '$tipoCambio', '$moneda','$ruta_xml', '$ruta_pdf', '$Full')";        

        $resultado=mysql_query($query,$conexion);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $query;
                print("\n".$mensaje);
            } 
        
        $ultimo_id = mysql_insert_id($conexion); 
        mysql_close($conexion);
        return $ultimo_id;                            
    }
    
    function GetFullText($array, $Full)
    {
        foreach ($array as  $value)
        {
            if(is_array($value))
            {
                $Full = $this->GetFullText($value,$Full);
            }
            else
                $Full.=$value.", ";
        }    
    
        return $Full;
    }
    
    /* Recibe un objeto tipo DomDocument, el cual es el XML devuelto después de la validación */
    function insert_validacion($validacion,$ruta_acuse)
    {        
        $webService=$validacion->getElementsByTagName("WebService")->item(0)->nodeValue;
        $EmisorRfc=$validacion->getElementsByTagName("EmisorRfc")->item(0)->nodeValue;
        $ReceptorRfc=$validacion->getElementsByTagName("ReceptorRFC")->item(0)->nodeValue;
        $FechaHoraEnvio=$validacion->getElementsByTagName("FechaHoraEnvio")->item(0)->nodeValue;
        $FechaHoraRespuesta=$validacion->getElementsByTagName("FechaHoraRespuesta")->item(0)->nodeValue;
        $TotalFactura=$validacion->getElementsByTagName("TotalFactura")->item(0)->nodeValue;
        $uuid=$validacion->getElementsByTagName("UUID")->item(0)->nodeValue;
        $CodigoEstatus=$validacion->getElementsByTagName("CodigoEstatus")->item(0)->nodeValue;
        $Estado=$validacion->getElementsByTagName("Estado")->item(0)->nodeValue;
        $md5=$validacion->getElementsByTagName("AcuseRecibo")->item(0)->nodeValue;
        
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
        $q="INSERT INTO validacion_proveedor (FechaHora_envio, FechaHora_respuesta, emisor_rfc,"
                . "receptor_rfc, total_factura, uuid, codigo_estatus, estado, md5, web_service, ruta_acuse)"
                . " VALUES ('$FechaHoraEnvio', '$FechaHoraRespuesta', '$EmisorRfc', '$ReceptorRfc'"
                . ", $TotalFactura, '$uuid', '$CodigoEstatus', '$Estado', '$md5', '$webService', '$ruta_acuse')";
        $resultado=  mysql_query($q);      

        if(!$resultado)
        {
            printf("\n Error en la consulta $q \n Error:". mysql_error()); 
        }     
        
        $ultimo_id = mysql_insert_id($conexion); 
        mysql_close($conexion);
        return $ultimo_id;                                  
    }
    function id_emisor()
    {
        $id_emisor=0;
//        printf ("\n RFC EMISOR= ".  $this->array_xml['emisor']['rfc']);
        $rfc=$this->array_xml['emisor']['rfc'];
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
        $q="select idemisor from emisor_factura_proveedor where rfc='$rfc'";
        $resultado=  mysql_query($q);      

        if(!$resultado)
        {
            echo mysql_error();            
        }
        else 
        {
            $row= mysql_fetch_row($resultado);
            $id_emisor=$row[0];
        
        }
       // echo "ID EMISOR ".$id_emisor;
        
        mysql_close($conexion);
        
        return $id_emisor;
    }
    function id_receptor()
    {
        $id_emisor=0;
        $rfc=$this->array_xml['receptor']['rfc'];
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);        
                
        $q="select id_receptor from receptor_factura_proveedor where rfc='$rfc'";
        $resultado=  mysql_query($q);      

        if(!$resultado)
        {
            echo mysql_error();
        }
        else
        {
            $row=  mysql_fetch_row($resultado);
            $id_emisor=$row[0];
        }      
                
        mysql_close($conexion);
        return $id_emisor;
    }
    function id_detalle($id_emisor,$id_receptor)
    {
//        $id_emisor=  $this->id_emisor;
//        $id_receptor=  $this->id_receptor;
        $rfc=$this->array_xml['receptor']['rfc'];

        $folio=  $this->array_xml['encabezado']['folio'];
        $serie=  $this->array_xml['encabezado']['serie'];
        $formaDePago=  $this->array_xml['encabezado']['formaDePago'];
        $subTotal=  $this->array_xml['encabezado']['subTotal'];
        $descuento=  $this->array_xml['encabezado']['descuento'];
        $total=  $this->array_xml['encabezado']['total'];
        $tipoDeComprobante=  $this->array_xml['encabezado']['tipoDeComprobante'];
        $var=$this->array_xml['encabezado']['fecha'];
        $date=str_replace('/', '-', $var);
        $fecha=date("Y-m-d", strtotime($date) );       
       
        $id_detalle=0;
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        
        mysql_select_db('CFDI',  $conexion);                
        $q="SELECT id_detalle from detalle_factura_proveedor where rfc_cliente='$rfc' 
            and id_emisor=$id_emisor and id_receptor=$id_receptor and fecha='$fecha'
            and folio='$folio' and serie='$serie' 
            and formaDePago='$formaDePago' and subTotal=$subTotal and descuento=$descuento
            and total=$total and tipoDeComprobante='$tipoDeComprobante'"; 

        $resultado=  mysql_query($q);      

        if(!$resultado)
        {
            echo mysql_error();
        }
        else
        {
            $row=  mysql_fetch_row($resultado);
            $id_detalle=$row[0];
        }  
                
        mysql_close($conexion);
        return $id_detalle;
    }
    /* Comprueba la existencia del emisor y si existe nos devulve su id */
    function exist_emisor()
    {
        $rfc = $this->array_xml['emisor']['rfc'];   
        
        print("\n comprobando existencia de emisor $rfc");
        
        $id=0;
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);        
        $q = "select idemisor from emisor_factura_proveedor where rfc = '$rfc'";
        $resultado= mysql_query($q,$conexion); 
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                print("\n".$mensaje);
            } 
        while($fila = mysql_fetch_assoc($resultado))
         {
              $id=$fila['idemisor'];             
         }       
        
        mysql_close($conexion);
        return $id;
    }
     function exist_receptor()
    {
        $id=0;
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        $rfc=$this->array_xml['receptor']['rfc'];
        mysql_select_db('CFDI',  $conexion);                        
        $q="select id_receptor from receptor_factura_proveedor where rfc='$rfc'";
        $resultado= mysql_query($q,$conexion); 
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                printf("\n".$mensaje);
            } 
        while($fila = mysql_fetch_assoc($resultado))
         {           
               $id=$fila['id_receptor'];
         }         
        mysql_close($conexion);
        return $id;        
    }
     function exist_detalle()
    {
        $id_emisor=  $this->id_emisor;
        $id_receptor=  $this->id_receptor;
        $rfc=$this->array_xml['receptor']['rfc'];
//        $fecha=$this->array_xml['encabezado']['fecha'];        
        $folio=  $this->array_xml['encabezado']['folio'];
        $serie=  $this->array_xml['encabezado']['serie'];
        $formaDePago=  $this->array_xml['encabezado']['formaDePago'];
        $subTotal=  $this->array_xml['encabezado']['subTotal'];
        $descuento=  $this->array_xml['encabezado']['descuento'];
        $total=  $this->array_xml['encabezado']['total'];
        $tipoDeComprobante=  $this->array_xml['encabezado']['tipoDeComprobante'];
        
        $var=$this->array_xml['encabezado']['fecha'];
        $date=str_replace('/', '-', $var);
        $fecha=date("Y-m-d", strtotime($date) );              
        
        if($descuento=="" or $descuento==null)$descuento=0;
        if($total=="" or $total==null)$total=0;
        if($subTotal=="" or $subTotal==null)$subTotal=0;       
                        
        
        $id=0;
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);                        
        $q="select id_detalle from detalle_factura_proveedor where rfc_cliente='$rfc' 
            and id_emisor=$id_emisor and id_receptor=$id_receptor and fecha='$fecha'
            and folio='$folio' and serie='$serie' 
            and formaDePago='$formaDePago' and subTotal=$subTotal and descuento=$descuento
            and total=$total and tipoDeComprobante='$tipoDeComprobante'"; 

        $resultado= mysql_query($q,$conexion); 
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                printf("\n".$mensaje);
            }
            else
            {
                while($fila = mysql_fetch_assoc($resultado))
                {           
                      $id=$fila['id_detalle'];
                }    
            }
                                                 
        mysql_close($conexion);
        return $id;        
    } 
    
    //Busca la ruta del xml en tabla detalle para reemplazarla (update con R_ al inicio del nombre del archivo xml)
     private function ruta_xml($id_empleado)
    {
        $ruta="";
        $Querys=new Querys();
        $conexion=$Querys->Conexion();
        $BD="CFDI";
        mysql_select_db($BD,$conexion);
        $q="select ruta_xml from detalle_factura_proveedor where id_detalle=$id_empleado";
        $resultado = mysql_query($q);
        if (!$resultado)
            {
                $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
                $mensaje .= 'Consulta completa: ' . $q;
                echo($mensaje);
            }
        while ($fila = mysql_fetch_assoc($resultado))
            {
                $ruta=$fila['ruta_xml'];              
            }

        mysql_close($conexion);
     return $ruta;
    }
    //Nueva ruta del xml para hacer el update R_ al inicio
    private function obtener_nueva_ruta($ruta_antigua)
    {
        $trozos = explode("/", $ruta_antigua); 
        $ruta_nueva="$trozos[0]/$trozos[1]/$trozos[2]/$trozos[3]/$trozos[4]/$trozos[5]/$trozos[6]/"; 
        return $ruta_nueva;
    }
    
    //Método que comprueba el número de veces que a sido enviado el mismo XML
    function obtener_intentos($id_emisor,$nombre_archivo)
    {
//        echo "ID EMISOR METODO OBTENER INTENTOS =".$id_emisor."  ".$nombre_archivo;
        $intentos=0;
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
//        echo "<p>QUERY SELECT emisor=$id_emisor:  </p>";
        $q="select NoIntentos from existe where id_emisor=".$id_emisor." and nombre = '".$nombre_archivo."';";        
        $tamaño=  mysql_num_rows(mysql_query($q));
//        echo "TAMAÑO DEVUELTO ROW = $tamaño";
        if($tamaño==0)
        {
//            echo "OBTENER INTENTOS QUERY IF ==0  ";
            //Se registra por primera ves en la tabla
            $q="Insert into existe (id_emisor, nombre, NoIntentos) values (".$id_emisor.", '".$nombre_archivo."',1); ";
            $res=  mysql_query($q);
            if(!$res)
            {
                echo mysql_error()." al insertar el archivo $nombre_archivo a la tabla existe $q";
            }
            $intentos++;
            
        }
        else
        {
//            echo "QUERY UPDATE NOMBRE = $nombre_archivo";
            $resultado=  mysql_query($q); 
            $row=mysql_fetch_row($resultado);
            $intentos=$row[0];
            $intentos++;
//            echo "NO INTENTOS ANTES DE UPDATE = $intentos  ";
            $q="UPDATE existe set NoIntentos=$intentos WHERE id_emisor=$id_emisor and nombre = '$nombre_archivo'";
            $update= mysql_query($q);
            if(!$update)
            {
                echo "ERROR UPDATE ID EMISOR = $id_emisor";
                echo mysql_error();
            }
        }
        mysql_close($conexion);
//        echo "INTENTOS = $intentos";
        return $intentos;
    }    
    //Método que realiza el nombrado a Existe3_nombre.xml y lo regresa al Inbox
     function renombrado_fallido($ruta,$archivo,$intentos)
    {
        
        rename($ruta.$archivo, "/volume1/Inbox_Factura_proveedor/"."Existe".$intentos."_".$archivo);
    }
    
    //Se generan los directorios en _root/Nomina_XML/año/curp/xml
    function move_cfdi($ruta,$nombre)
    {
        $curp=$this->array_xml['receptor']['rfc'];
        //Formateado de fecha
        $var=$this->array_xml['encabezado']['fecha']; // dato de prueba
        $date=str_replace('/', '-', $var);
        $fecha=date("Y-m-d", strtotime($date) );
        $año=substr($fecha, 0,4); 
        
        $estructura = '/volume1/web/_root/Factura_Proveedor/'.$año.'/'.$curp.'/';
        if(!file_exists($estructura))
        {
             mkdir($estructura,0777, true);
            chmod($estructura,  0777);      
        }       
        rename($ruta.$nombre, "/volume1/web/_root/Factura_Proveedor/$año/$curp/".$nombre);
        
        return $estructura;
    }
    
    function get_ruta_cfdi()
    {
        $curp=$this->array_xml['receptor']['rfc'];
        //Formateado de fecha
        $var=$this->array_xml['encabezado']['fecha']; // dato de prueba
        $date=str_replace('/', '-', $var);
        $fecha=date("Y-m-d", strtotime($date) );
        $año=substr($fecha, 0,4); 
        
        $estructura = '/volume1/web/_root/Factura_Proveedor/'.$año.'/'.$curp.'/';
        if(!file_exists($estructura))
        {
             mkdir($estructura,0777, true);
            chmod($estructura,  0777);      
        }       
        return $estructura;
    }
    
    private function move_pdf($ruta_original,$ruta_destino,$pdf)
    {           
        if(file_exists($ruta_original.$pdf))
            if(!rename($ruta_original.$pdf,$ruta_destino.$pdf))
                printf("\n Error al mover el pdf: $pdf");
        
        $estructura = $ruta_destino.$pdf;
                return $estructura;
    }
    

    //Se inserta la ruta del pdf en la tabla id_detalle mediante su id_detalle
    private function insert_route_pdf($id,$pdf)
    {
        $estado=FALSE;
//        echo "  pdf a insertar $pdf  ";
        $Querys= new Querys();
        $conexion=$Querys->Conexion();  
        mysql_select_db('CFDI',  $conexion);
        $q="UPDATE detalle_factura_proveedor SET ruta_pdf='$pdf' WHERE id_detalle=$id";
        
        $resultado=  mysql_query($q);
        
        if (!$resultado){
            $mensaje  = 'Consulta no válida: ' . mysql_error() . "\n";
            $mensaje .= 'Consulta completa: ' . $q;                                           
            echo($mensaje);
        }    
        else 
            $estado=TRUE;
        
        mysql_close($conexion);        
        return $estado;
    }

    
    //Se busca en la pila que se recibe de la clase Pila_Nomina_XML
    private function search_pdf_in_stack($pdf)
    {       
        printf ("\n    Inicio     Search PDF   $pdf");
        $extension = pathinfo($pdf, PATHINFO_EXTENSION);
        $xml_a_buscar = basename($pdf, '.'.$extension); 
        $xml_search="$xml_a_buscar.xml";
        printf ("\n    xml a buscar    $xml_search   ");  
        $array_xml=  $this->stack_xml;
        printf ("   PESO PILA ANTES DE FOR = ".count($this->stack_xml));
        
        for($cont=0;$cont<count($array_xml);$cont++)
        {
            $clave=  array_search($xml_search, $array_xml[$cont]);
            if($clave==FALSE)
            {
                $xml_search="$xml_a_buscar.XML";
                $clave=  array_search($xml_search, $array_xml[$cont]);
            }
            if($clave!=FALSE)
            {
               $id=$array_xml[$cont]['id'];
               printf("\n  ID=$id  ");
               $ruta_xml=  $this->ruta_xml($id);
               printf("\n ruta xml=".$ruta_xml);
               $nueva_ruta=  $this->obtener_nueva_ruta($ruta_xml);
               $ruta= $this->move_pdf("/volume1/Inbox_Factura_proveedor/",$nueva_ruta, $pdf);  
               $this->insert_route_pdf($id, $ruta);

                //Al ser encontrado el xml es descartado para la inserción en el registro
                unset($this->stack_xml[$cont]);
                $this->stack_xml=  array_values($this->stack_xml);                    
             }      
                //Si el pdf no tiene pareja de xml se envia a la pila de registro
                $aux=$cont+1;
                if($aux== count($array_xml))
                {
                    array_push($this->registro_pdf, $pdf);
                }       
        }                
    }


    private function type_extension($archivo)
    {      
        $trozos = explode(".", $archivo); 
        $extension = end($trozos); 
        
        return $extension;
    }                        
}