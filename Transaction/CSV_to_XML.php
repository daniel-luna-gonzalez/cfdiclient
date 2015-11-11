<?php
class CSV_to_XML {
    public function __construct() { 
    }
    function CsvtoXml($Ruta_csv,$Nombre_csv)
    {
        $estado=FALSE;
          // Open csv to read
         $nombre=$Ruta_csv.$Nombre_csv;

         //Se valida el tipo de archivo que se recibe
         $trozos = explode(".", $Nombre_csv); 
        $extension = end($trozos); 
        if($extension=="csv")
        {
                //Creamos Array que contiene los nodos del XML
            $arr_datos = array();

            //Array para almacenar datos de una fila (Usuario)
            $arr_datos_usuario=array();

            $fila = 1;   
            if (($gestor = fopen($nombre, "r")) !== FALSE) 
                {
                while (($datos = fgetcsv($gestor, 0, ",")) !== FALSE)
                {
                    $numero = count($datos);
//                    "<p> $numero campos en la linea $fila: <br /></p>\n";
                    for ($c=0; $c < $numero; $c++)
                    {
                        //Se llena el arreglo de nodos 
                        if($fila==1)
                        {          
                            $arr_datos[$c]=$datos[$c];
                        }
                        if($fila>1)
                        {
                            $arr_datos_usuario[$c]=$datos[$c];
                        }
                    }
                    if($fila>1)
                    {
    //                  //Si la columna curp no es mayor a 10 no se toma en cuenta para generar xml
                        if(strlen($arr_datos_usuario[3])>10)
                        {
                            $this->Crear_xml($arr_datos_usuario);           
                        }
    //                  Se limpia el array de datos de empleado para capturar los del siguiente 
                        while(count($arr_datos_usuario)) array_pop($arr_datos_usuario);
                    }
                     $fila++;        
            }

            fclose($gestor);
            $estado=TRUE;
            }
            else
            {
                $estado=FALSE;
            }                
        }
                 
        return $estado;
    }
    function Crear_xml($datos_usuario)
    {
        $arr_datos_usuario=$datos_usuario;
        // Creación de documento DOM
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;


        $root = $doc->createElement('Nomina');
        $doc->appendChild($root);
        $root->setAttribute('xmlns', 'http://www.sat.gob.mx/nomina');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi', 'http://www.sat.gob.mx/nomina file:///C:/Users/Marco/Desktop/FActura%20Proyectos/nomina.xsd');
        $root->setAttribute('Version', '1.1');
        $aux=1;

            $root->setAttribute('RegistroPatronal', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('NumEmpleado', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('CURP', $arr_datos_usuario[$aux]);$aux=13;
            $root->setAttribute('TipoRegimen', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('NumSeguridadSocial', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('FechaPago', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('FechaInicialPago', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('FechaFinalPago', $arr_datos_usuario[$aux]);    $aux++;
            $root->setAttribute('NumDiasPagados', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('Departamento', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('CLABE', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('Banco', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('FechaInicioRelLaboral', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('Antiguedad', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('Puesto', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('TipoContrato', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('TipoJornada', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('PeriodicidadPago', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('SalarioBaseCotApor', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('RiesgoPuesto', $arr_datos_usuario[$aux]);$aux++;
            $root->setAttribute('SalarioDiarioIntegrado', $arr_datos_usuario[$aux]);
            //echo $aux;

        //21
        //
        //EMISOR
        //
        //
            $emisor=$doc->createElement('Emisor');
            $emisor->setAttribute('rfc',$arr_datos_usuario[301]);
            $emisor->setAttribute('nombre', $arr_datos_usuario[302]);
            $emisor_c=$doc->createElement('DomicilioFiscal');
            $emisor_c->setAttribute('pais', $arr_datos_usuario[303]);
            $emisor_c->setAttribute('calle', $arr_datos_usuario[304]);
            $emisor_c->setAttribute('estado', $arr_datos_usuario[305]);
            $emisor_c->setAttribute('colonia', $arr_datos_usuario[306]);
            $emisor_c->setAttribute('municipio', $arr_datos_usuario[307]);
            $emisor_c->setAttribute('noExterior', $arr_datos_usuario[308]);
            $emisor_c->setAttribute('codigoPostal', $arr_datos_usuario[309]);
            
            $emisor_e=$doc->createElement('ExpedidoEn');   
            $emisor_e->setAttribute('pais', $arr_datos_usuario[310]);
            $emisor_e->setAttribute('calle', $arr_datos_usuario[311]);
            $emisor_e->setAttribute('estado', $arr_datos_usuario[312]);
            $emisor_e->setAttribute('colonia', $arr_datos_usuario[313]);
            $emisor_e->setAttribute('noExterior', $arr_datos_usuario[314]);
            $emisor_e->setAttribute('codigoPostal', $arr_datos_usuario[315]);
                        
            $emisor->appendChild($emisor_c);
            $emisor->appendChild($emisor_e);
            $root->appendChild($emisor);
            
            //RECEPTOR
            $receptor=$doc->createElement('Receptor');
            $receptor->setAttribute('rfc', $arr_datos_usuario[4]);
            $nombre=$arr_datos_usuario[0];
            $nombre_utf = utf8_encode ( $nombre ); 
            $receptor->setAttribute('nombre', $nombre_utf);
            
            $receptor_c=$doc->createElement('Domicilio');
            $receptor_c->setAttribute('pais', $arr_datos_usuario[5]);
            $receptor_c->setAttribute('calle', $arr_datos_usuario[6]);
            $receptor_c->setAttribute('estado', $arr_datos_usuario[7]);
            $receptor_c->setAttribute('colonia', $arr_datos_usuario[8]);
            $receptor_c->setAttribute('municipio', $arr_datos_usuario[9]);
            $receptor_c->setAttribute('noExterior', $arr_datos_usuario[10]);
            $receptor_c->setAttribute('noInterior', $arr_datos_usuario[11]);
            $receptor_c->setAttribute('codigoPostal', $arr_datos_usuario[12]);
            $receptor->appendChild($receptor_c);
            $root->appendChild($receptor);
            
            
            //Percepciones
                $child=$doc->createElement('Percepciones');
                $child->setAttribute('TotalGravado', $arr_datos_usuario[31]);
                $child->setAttribute('TotalExento', $arr_datos_usuario[32]);
        $aux=33;
            for($cont=0;$cont<28;$cont++)// en el arreglo en el nodo 33 empiezan las percepciones 
            {        
                $child_c=$doc->createElement('Percepcion');
                $child_c->setAttribute('TipoPercepcion', $arr_datos_usuario[$aux]);$aux++;
                $child_c->setAttribute('Clave', $arr_datos_usuario[$aux]);$aux++;
                $child_c->setAttribute('Concepto', $arr_datos_usuario[$aux]);$aux++;
                $child_c->setAttribute('ImporteGravado', $arr_datos_usuario[$aux]);$aux++;
                $child_c->setAttribute('ImporteExento', $arr_datos_usuario[$aux]);$aux++;

                $child->appendChild($child_c);
                $root->appendChild($child);
                //$aux=$aux+6;
            }
            //Deducciones
//            
                    $deducc=$doc->createElement('Deducciones');
                    $deducc->setAttribute('TotalGravado', $arr_datos_usuario[173]);
                    $deducc->setAttribute('TotalExento', $arr_datos_usuario[174]);
                 $aux=175;
                for($cont=0;$cont<21;$cont++)// en el arreglo en el nodo 175 empiezan las deducciones 
                {
                    $deducc_c=$doc->createElement('Deduccion');
                    $deducc_c->setAttribute('TipoDeduccion', $arr_datos_usuario[$aux]);$aux++;
                    $deducc_c->setAttribute('Clave', $arr_datos_usuario[$aux]);$aux++;
                    $deducc_c->setAttribute('Concepto', $arr_datos_usuario[$aux]);$aux++;
                    $deducc_c->setAttribute('ImporteGravado', $arr_datos_usuario[$aux]);$aux++;            
                    $deducc_c->setAttribute('ImporteExento', $arr_datos_usuario[$aux]);$aux++;
                    $deducc->appendChild($deducc_c);
                    $root->appendChild($deducc);        
                }
        $aux=280;
            //Incapacidades nodo 280
        $incapa=$doc->createElement('Incapacidades');
        for($cont=0;$cont<3;$cont++)
        {
            $incapa_c=$doc->createElement('Incapacidad');
            $incapa_c->setAttribute('DiasIncapacidad', $arr_datos_usuario[$aux]);$aux++;
            $incapa_c->setAttribute('TipoIncapacidad', $arr_datos_usuario[$aux]);$aux++;
            $incapa_c->setAttribute('Descuento', $arr_datos_usuario[$aux]);$aux++;
            $incapa->appendChild($incapa_c);
            $root->appendChild($incapa);
        }
            //Horas Extra
        $aux=289;
            $hrs=$doc->createElement('HorasExtras');
        for($cont=0;$cont<3;$cont++)
        {
            $hrs_c=$doc->createElement('HorasExtra');            
            $hrs_c->setAttribute('Dias', $arr_datos_usuario[$aux]);$aux++;
            $hrs_c->setAttribute('TipoHoras', $arr_datos_usuario[$aux]);$aux++;
            $hrs_c->setAttribute('HorasExtra', $arr_datos_usuario[$aux]);$aux++;
            $hrs_c->setAttribute('ImportePagado', $arr_datos_usuario[$aux]);$aux++;
            $hrs->appendChild($hrs_c);
            $root->appendChild($hrs);
        }
        //Quitamos / a la fecha 
        $healthy = array("/","-","."," ");
        $yummy   = array("",);
        $fecha = str_replace($healthy, $yummy, $arr_datos_usuario[15]);
        $this->SaveXML($doc,$fecha.$arr_datos_usuario[3]);  //El XML se llama como igual que el CURP del empleado y la fecha de la nomina
    }
    function SaveXML($doc,$nombre_xml)
    {
        //Directorio Oculto para el usuario donde se almacenarán los XML (Pendiente de Cambio)
        //mkdir('/usr/CFDI/Inbox_Nomina_Timbrado'); 
        $doc->save("/usr/CFDI/Inbox_Nomina_Timbrado/".$nombre_xml.".xml");
        
    }    
}

//$csvtoxml=new CSV_to_XML("C:\wamp\www\csv\\","NOMINA.31.07.13");

?>
