<?php
/**
 * Description of CFDI
 *
 * @author Daniel
 */
$RoutFile = filter_input(INPUT_SERVER, "DOCUMENT_ROOT"); /* /var/services/web */

require_once 'XML.php';
require_once "$RoutFile/DAO/Log.php";
require_once "$RoutFile/Transaction/Read_factura_cliente.php";
require_once 'DataBase.php';
require_once "$RoutFile/Transaction/webservice_sat.php";
require_once "Receipt.php";
require_once "Historical.php";

class CFDI {
    public function __construct() {
        $this->Ajax();
    }
    
    private function Ajax()
    {
        $option = filter_input(INPUT_POST, "option");
        switch ($option)
        {
            case 'GetXmlStructure': $this->GetXmlStructure();  break; 
            case 'GetFiles': $this->GetFiles();  break; 
            case 'UpdateCfdi': $this->UpdateCfdi(); break;
        }        
    }   
    
    private function UpdateCfdi()
    {
        $receipt = new Receipt();
        
        $content = filter_input(INPUT_POST, "content");
        $IdUser = filter_input(INPUT_POST, "IdUser");
        $UserName = filter_input(INPUT_POST, "UserName");
        $PdfPath = filter_input(INPUT_POST, "PdfPath");
        $XmlPath = filter_input(INPUT_POST, "XmlPath");
        $IdCfdi = filter_input(INPUT_POST, "IdCfdi");
        $FileState = filter_input(INPUT_POST, "FileState");
        $key = 0;
        $TableName = '';
        
        if(strcasecmp($content, "provider")==0)
        {
            $TableName = "proveedor";
            $key = 3;
        }
        if(strcasecmp($content, "client")==0)
        {
            $TableName = "cliente";
            $key = 2;
        }
        if(strcasecmp($content, "payroll")==0)
        {
            $TableName = "nomina";
            $key = 1;
        }

        $PdfNewName = $_FILES['pdf']['name'];
        $PdfNewPath = $_FILES['pdf']['tmp_name'];
        $XmlNewName = $_FILES['xml']['name'];
        $XmlNewPath = $_FILES['xml']['tmp_name'];    
        
        $NewPdfName = pathinfo($PdfNewName, PATHINFO_FILENAME);
        $NewXmlName = pathinfo($XmlNewName, PATHINFO_FILENAME);   
        
        $NewPdfExtension = pathinfo($PdfNewName, PATHINFO_EXTENSION);    
        
        $OldXmlName = pathinfo($XmlPath, PATHINFO_FILENAME);        
        $OldExtensionXml = pathinfo($XmlPath, PATHINFO_EXTENSION);
        $oldSatReceiptName = pathinfo($XmlPath, PATHINFO_FILENAME);        
        
        $OldPdfName = pathinfo($PdfPath, PATHINFO_FILENAME);        
        $OldExtensionPdf = pathinfo($PdfPath, PATHINFO_EXTENSION);
        
        $OldPathSatReceipt = dirname($XmlPath)."/".$OldXmlName."SAT.".$OldExtensionXml;
        
        if($_FILES['xml']['error'] != UPLOAD_ERR_OK )
        {
            XML::XmlResponse ("Error", 0, "<p>".$_FILES['xml']['error'] .'</p>');
            return;
        }
        if($_FILES['xml']['pdf'] != UPLOAD_ERR_OK )
        {
            XML::XmlResponse ("Error", 0, "<p>".$_FILES['xml']['error'] .'</p>');
            return;
        }
        
        if(!file_exists($XmlPath))
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> no existe el Xml a reemplazar</p>");
            return 0;
        }
        
        if(!file_exists($OldPathSatReceipt))
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> no existe el comprobante de validación del Xml</p>");
            return 0;
        }
        
        /* Validación del mismo nombre del xml y el pdf */
        if(file_exists($PdfNewPath) and file_exists($XmlNewPath))
        {
            if(strcasecmp($NewPdfName, $NewXmlName)!=0)
            {
                XML::XmlResponse("Error", 0, "<p>El Pdf y el Xml deben tener el mismo nombre</p>");                
                return 0;
            }
        }
        
        $ReadXml = new Read_factura_cliente();
        $Validation = new webservice_sat();
        
        $ValidationXml = $ReadXml->validacion_estructura($XmlPath);
        if($ValidationXml!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> el xml es inválido</p>");
            return 0;
        }
        
        $XmlDetail = $ReadXml->GetDetail($XmlNewPath);
        
        $ValidateXml = $Validation->valida_cfdi($XmlDetail['emisor']['rfc'], $XmlDetail['receptor']['rfc'], $XmlDetail['encabezado']['total'], $XmlDetail['timbreFiscalDigital']['UUID']);
        if(!is_object($ValidateXml))
        {
            XML::XMLReponse("Error", 0, "<p><b>Error</b> el xml es inválido</p>");
            return 0;
        }                
        
        /* Se crea el directorio donde se almacenan las actualizaciones de un CFDI */
        if(!file_exists(dirname($XmlPath) ."/copias"))
        {
            mkdir(dirname($XmlPath)."/copias",0777,true);
        }
        
        /* Se mueve el antiguo xml y se sube el nuevo reemplazando la ruta del antiguo xml */
        $NewRouteDestinationXml = dirname($XmlPath)."/copias/".  basename($XmlPath);

        if(file_exists($NewRouteDestinationXml))
        {
//                echo "ya existe el xml en la ruta destino<br><br>";
                $OldXmlName = pathinfo($this->RenameFile(dirname($NewRouteDestinationXml),basename($NewRouteDestinationXml)), PATHINFO_FILENAME);
//                echo "<p>Nuevo nombre $NewName</p>";
                $NewRouteDestinationXml = dirname($NewRouteDestinationXml)."/$OldXmlName.$OldExtensionXml";
        }
        
        if(!rename($XmlPath, $NewRouteDestinationXml))
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b>al trasladar el xml antiguo al histórico de copias</p>");
                return 0;
        }
        
        if(!move_uploaded_file($XmlNewPath, $XmlPath))
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b>al trasladar el nuevo Xml a su ruta correspondiente</p>");
                return 0;
        }
        
        /* Se mueve el comprobante SAT al directorio de copias */                
        $NewPathSatReceipt = dirname($NewRouteDestinationXml)."/".$OldXmlName."SAT.xml";

        if(!rename($OldPathSatReceipt,$NewPathSatReceipt ))
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b>al trasladar el comprobante de validación antiguo al histórico de copias</p>");
                return 0;
        }        
        
        $NewSatReceipt = dirname($XmlPath)."/".$oldSatReceiptName."SAT.$OldExtensionXml";

        $ValidateXml->save($NewSatReceipt);
        
        /* Sí existe un PDF este se mueve a copias */
        $NewRouteDestinationPdf = dirname($XmlPath)."/copias";        
        if(file_exists($PdfPath))
        {
            $NewPdfPath = $NewRouteDestinationPdf."/". $OldXmlName ."." .$OldExtensionPdf;
            if(!rename($PdfPath, $NewPdfPath))
            {
                XML::XmlResponse("Error", 0, "<p><b>Error</b>al trasladar el xml antiguo al histórico de copias</p>");
                    return 0;
            }
        }
                    
        /* Sí se adjunta un nuevo pdf se introduce en la ruta del XML */
        if(file_exists($PdfNewPath))
        {
            $NewPdfPath = dirname($XmlPath)."/".$OldPdfName.".$OldExtensionPdf";
            if(!move_uploaded_file($PdfNewPath, $NewPdfPath))
            {
                XML::XmlResponse("Error", 0, "<p><b>Error</b>al trasladar el nuevo PDF a su ruta correspondiente</p>");
                    return 0;
            }                       
        }               
        $NewIdReceipt = $receipt->InsertValidationCfdi($TableName, $ValidateXml, $NewPathSatReceipt); 
        
        $historical = new Historical();
        $NewIdHistorical = $historical->InsertHistorical($TableName, $IdUser, $IdCfdi, $NewIdReceipt, $NewRouteDestinationXml, $NewRouteDestinationPdf."/". $OldXmlName ."." .$OldExtensionPdf, $FileState);
        
        if($NewIdHistorical==0)
            return 0;
        
        if($this->UpdateMetadatas($TableName, $IdCfdi, $XmlDetail, $XmlPath, $PdfPath)!=1)
            return 0;
        
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Update');
        $doc->appendChild($root);       
        $mensaje_=$doc->createElement('Mensaje','<p>Datos actualizados con éxito</p>');
        $root->appendChild($mensaje_);
        $Fecha = $doc->createElement("Fecha", $XmlDetail['encabezado']['fecha']);
        $root->appendChild($Fecha);
        $Folio = $doc->createElement("Folio", $XmlDetail['encabezado']['folio']);
        $root->appendChild($Folio);
        $Subtotal = $doc->createElement("subTotal", $XmlDetail['encabezado']['subTotal']);
        $root->appendChild($Subtotal);
        $Total = $doc->createElement("Total", $XmlDetail['encabezado']['total']);
        $root->appendChild($Total);
        header ("Content-Type:text/xml");
        echo $doc->saveXML();
           
//        $log = new Log();        
//        $log->write_line(8,$IdUser,$IdCfdi,$key);/* Registro Log */
    }
    
    private function RenameFile($destination,$NewRouteDestinationXml)
    {
        $increment = 1; //start with no suffix
        $name = pathinfo($NewRouteDestinationXml, PATHINFO_FILENAME);
        $extension = pathinfo($NewRouteDestinationXml, PATHINFO_EXTENSION);
        while(file_exists($destination."/".$name . $increment . '.' . $extension)) {
            $increment++;
        }

        $basename = $name . $increment . '.' . $extension;
        return $basename;
    }
    
    private function UpdateMetadatas($TableName, $IdCfdi, $Array, $XmlPath, $PdfPath)
    {
        $DB = new DataBase();
        $Full = $this->GetFullText($Array, '');        
        $Update = '';
        $serie = $Array['encabezado']['serie'];
        $folio = $Array['encabezado']['folio'];
        $fecha = $Array['encabezado']['fecha'];
        $formaDePago = $Array['encabezado']['formaDePago'];
        $subTotal = $Array['encabezado']['subTotal'];
        $descuento = $Array['encabezado']['descuento'];
        $total = $Array['encabezado']['total'];
        $metodoDePago = $Array['encabezado']['metodoDePago'];
        $tipoDeComprobante = $Array['encabezado']['tipoDeComprobante'];
        $tipoCambio = $Array['encabezado']['TipoCambio'];
        $moneda = $Array['encabezado']['Moneda'];
        
//        echo ($subTotal);
//        return;
        
        if(!(is_numeric("$descuento")))
            $descuento = 0;
        if(!(is_numeric("$total")))
            $total = 0;
        if(!(is_numeric("$subTotal")))
            $subTotal = 0;
        if(!(is_numeric("$tipoCambio")))
            $tipoCambio=0;
        
        if(strcasecmp($TableName, 'proveedor')==0 or strcasecmp($TableName, 'cliente')==0)
            $Update = "UPDATE detalle_factura_$TableName SET "
                . "serie = '$serie', folio = '$folio', fecha = '$fecha', subTotal = $subTotal,"
                . "descuento = $descuento, total = $total, metodoDePago = '$metodoDePago', tipoDeComprobante = '$tipoDeComprobante',"
                . "TipoCambio =$tipoCambio, Moneda='$moneda', ruta_pdf = '$PdfPath', ruta_xml ='$XmlPath', tipo_archivo='copia', Full = '$Full' "  
                . " WHERE Id_detalle = $IdCfdi";
        
        if(strcasecmp($TableName, 'nomina')==0)
            $Update = "UPDATE detalle_factura_$TableName SET "
                . "serie = '$serie', folio = '$folio', fecha = '$fecha', formapago = '$formaDePago', subTotal = $subTotal,"
                . "descuento = $descuento, total = $total, metodoDePago = '$metodoDePago', tipoDeComprobante = '$tipoDeComprobante',"
                . "TipoCambio =$tipoCambio, Moneda='$moneda', ruta_pdf = '$PdfPath', ruta_xml ='$XmlPath', tipo_archivo='copia', Full = '$Full' "  
                . " WHERE Id_detalle = $IdCfdi";
        
        
        if(($result = $DB->ConsultaQuery("CFDI", $Update))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al actualizar los datos del nuevo CFDI</p><br>Detalles:<br><br>$result");
            return 0;
        }
        
        return 1;
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
    
    private function GetRelativePath($Path)
    {
        $directorio = explode("/", $Path); 
        $ruta_nueva_pdf='';
        for ($cont=0;$cont<count($directorio);$cont++)/* desde el nodo 3 para quitar /volume1/web/ */
        {
            if($cont+1!=(count($directorio)))
            {
                $ruta_nueva_pdf.=$directorio[$cont].'/';        
            }                               
        }
        
        $ruta=$ruta_nueva_pdf;             
        return $ruta;
    }
    
    private function GetFiles()
    {
        $DB = new DataBase();
        $IdUser = filter_input(INPUT_POST, "IdUser");
        $IdUserName = filter_input(INPUT_POST, "UserName");
        $content = filter_input(INPUT_POST, "content");
        $IdReceiver = filter_input(INPUT_POST, "IdReceiver");
        $StartDate = filter_input(INPUT_POST, "StartDate");
        $EndDate = filter_input(INPUT_POST, "EndDate");
        $IdTransmiter = filter_input(INPUT_POST, "IdTransmiter");
        $SearchWord = trim(filter_input(INPUT_POST, "SearchWord"),' ');
        $WhereTransmiter = '';
        $Match = '';
        $Key = 0;
        $TableName = '';
        $q = '';
        
        if($IdTransmiter>0)
            $WhereTransmiter = "AND det.id_emisor = $IdTransmiter";
        
        if(strcasecmp($content, "provider")==0)
        {
            $Key = 3;
            $TableName = "proveedor";
        }
        if(strcasecmp($content, "client")==0)
        {
            $Key = 2;
            $TableName = "cliente";
        }
        if(strcasecmp($content, "payroll")==0)
        {
            $Key=1;
            $TableName = "nomina";
        }       
        if(strlen($SearchWord)>0)
            $Match = " AND MATCH (det.Full) AGAINST ('$SearchWord' IN BOOLEAN MODE) ";
        
        if(strcasecmp($content, 'Provider')==0 or strcasecmp($content, 'Client')==0)
        {
            if($StartDate=="" and $EndDate=="")
                $q="SELECT det.id_detalle, det.fecha, det.folio, det.subTotal, det.descuento, det.total,ruta_xml, det.ruta_pdf, det.id_validacion, det.tipo_archivo, val.ruta_acuse FROM detalle_factura_$TableName det inner join validacion_$TableName val on det.id_validacion=val.id_validacion WHERE det.id_receptor=$IdReceiver $WhereTransmiter $Match";
            if($StartDate!="" and $EndDate!="")
                $q="SELECT det.id_detalle, det.fecha, det.folio, det.subTotal, det.descuento, det.total,ruta_xml, det.ruta_pdf, det.id_validacion, det.tipo_archivo, val.ruta_acuse FROM detalle_factura_$TableName det inner join validacion_$TableName val on det.id_validacion=val.id_validacion WHERE id_receptor=$IdReceiver $WhereTransmiter AND (det.fecha BETWEEN '$StartDate' AND '$EndDate') $Match";
            if($StartDate!="" and $EndDate=="")
                $q="SELECT det.id_detalle, det.fecha, det.folio, det.subTotal, det.descuento, det.total,ruta_xml, det.ruta_pdf, det.id_validacion, det.tipo_archivo, val.ruta_acuse FROM detalle_factura_$TableName det inner join validacion_$TableName val on det.id_validacion=val.id_validacion WHERE det.fecha>='$StartDate' AND id_receptor=$IdReceiver $WhereTransmiter $Match";
            if($StartDate=="" and $EndDate!="")
                $q="SELECT det.id_detalle, det.fecha, det.folio, det.subTotal, det.descuento, det.total,ruta_xml, det.ruta_pdf, det.id_validacion, det.tipo_archivo, val.ruta_acuse FROM detalle_factura_$TableName det inner join validacion_$TableName val on det.id_validacion=val.id_validacion WHERE det.fecha<='$EndDate' AND id_receptor=$IdReceiver $WhereTransmiter $Match";
        }
        
        
        if(strcasecmp($content, 'PayRoll')==0)
        {
            if($StartDate=="" and $EndDate=="")
                $q="SELECT det.id_detalle, det.fecha, det.folio, det.subTotal, det.descuento, det.total,ruta_xml, det.ruta_pdf, det.id_validacion, det.tipo_archivo, val.ruta_acuse FROM detalle_factura_cliente det inner join validacion_cliente val on det.id_validacion=val.id_validacion WHERE det.id_receptor=$IdReceiver and det.id_emisor=$IdTransmiter";
            if($StartDate!="" and $EndDate!="")
                $q="SELECT id_detalle,FechaPago,SalarioBaseCotApor,SalarioDiarioIntegrado,xml_ruta,pdf_ruta FROM detalle_recibo_nomina WHERE id_receptor=$IdReceiver AND id_emisor=$IdTransmiter AND (FechaPago BETWEEN '$StartDate' AND '$EndDate')";
            if($StartDate!="" and $EndDate=="")
                $q="SELECT id_detalle,FechaPago,SalarioBaseCotApor,SalarioDiarioIntegrado,xml_ruta,pdf_ruta FROM detalle_recibo_nomina WHERE FechaPago>='$StartDate' AND id_receptor=$IdReceiver AND id_emisor=$IdTransmiter";
            if($StartDate=="" and $EndDate!="")
                $q="SELECT id_detalle,FechaPago,SalarioBaseCotApor,SalarioDiarioIntegrado,xml_ruta,pdf_ruta FROM detalle_recibo_nomina WHERE FechaPago<='$EndDate' AND id_receptor=$IdReceiver AND id_emisor=$IdTransmiter";
        }
        $ResultGetFiles = $DB->ConsultaSelect("CFDI", $q);
        if($ResultGetFiles['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al intentar recuperar los CFDI's</p><br>Detalles:<br><br>".$ResultGetFiles['Estado']);
            return 0;
        }
        
        $FilesArray = $ResultGetFiles['ArrayDatos'];
        
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Files');

        for($cont = 0; $cont < count($FilesArray); $cont++)
        {
            $Cfdi = $doc->createElement("File");
            $IdCfdi = $doc->createElement("IdCfdi", $FilesArray[$cont]['id_detalle']);
            $Cfdi->appendChild($IdCfdi);
            $Fecha = $doc->createElement("Date", $FilesArray[$cont]['fecha']);
            $Cfdi->appendChild($Fecha);
            $folio = $doc->createElement("Folio", $FilesArray[$cont]['folio']);
            $Cfdi->appendChild($folio);
            $CfdiSubtotal = $doc->createElement("subTotal", $FilesArray[$cont]['subTotal']);
            $Cfdi->appendChild($CfdiSubtotal);
            $CfdiTotal = $doc->createElement("Total", $FilesArray[$cont]['total']);
            $Cfdi->appendChild($CfdiTotal);
            $CfdiRutaXml = $doc->createElement("XmlPath", $FilesArray[$cont]['ruta_xml']);
            $Cfdi->appendChild($CfdiRutaXml);
            $CfdiPdf = $doc->createElement("PdfPath", $FilesArray[$cont]['ruta_pdf']);
            $Cfdi->appendChild($CfdiPdf);
            $CfdiState = $doc->createElement("StateCfdi", $FilesArray[$cont]['tipo_archivo']);
            $Cfdi->appendChild($CfdiState);
            $CfdiIdValidation = $doc->createElement("IdValidationReceipt", $FilesArray[$cont]['id_validacion']);
            $Cfdi->appendChild($CfdiIdValidation);
            $CfdiReceiptPath = $doc->createElement("ReceiptValidationPath", $FilesArray[$cont]['ruta_acuse']);
            $Cfdi->appendChild($CfdiReceiptPath);
            $root->appendChild($Cfdi);
        }
        
        $doc->appendChild($root);
        header ("Content-Type:text/xml");
        echo $doc->saveXML();
    }
    
    private function GetXmlStructure()
    {
        
        $XmlPath = filter_input(INPUT_POST, "XmlPath");
        $content = filter_input(INPUT_POST, "content");
        $IdLogin = filter_input(INPUT_POST, "IdLogin");
        
        if (file_exists($XmlPath)) 
        {           
            $xml_contents = file_get_contents($XmlPath);
            $xml_ = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml_contents);
            $xml = simplexml_load_string($xml_);

            header('Content-Type: text/xml');
            echo $xml->saveXML();          
            
            $log=new Log();     
            if($content == 'nomina')
                $clave_log=1;
            if($content == 'cliente')
                $clave_log=2;
            if($content == 'proveedor')
                $clave_log=3;
            
//            $log->write_line(18, $IdLogin, 0 , $clave_log);/* Registro Log */ 
        }
        else
            XML::XmlResponse ("Error", 0, "<p><b>Error</b>, no existe el documento solicitado.</p>");
    }
    
}

$CFDI = new CFDI();