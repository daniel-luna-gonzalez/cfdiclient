<?php
/**
 * Description of Receipt
 *
 * @author Daniel
 */
require_once 'DataBase.php';
require_once 'XML.php';
class Receipt {
    public function __construct() {
        $option = filter_input(INPUT_POST, "option");
        switch ($option)
        {
            case 'GetXmlValidationReceipt': $this->GetXmlValidationReceipt(); break;
            case 'GetXmlSatValidationCfdiAnswer': $this->GetXmlSatValidationCfdiAnswer(); break;
        }
    }
    private function GetXmlSatValidationCfdiAnswer()
    {
        $DB = new DataBase();
        
        $IdUser = filter_input(INPUT_POST, "idLogin");
        $IdDetail = filter_input(INPUT_POST, "IdDetail");
        $Content = filter_input(INPUT_POST, "content");
        
        $Query = "";
        if(strcasecmp($Content, "proveedor")==0 or strcasecmp($Content, "cliente")==0)
        {
            $Query = "SELECT det.id_validacion , val.ruta_acuse FROM detalle_factura_$Content det inner join validacion_$Content val on det.id_validacion=val.id_validacion  WHERE det.id_detalle=$IdDetail ";
        }
        if(strcasecmp($Content, "nomina")==0)
        {
            $Query="SELECT det.id_validacion, val.ruta_acuse FROM detalle_recibo_$Content det inner join validacion_nomina val on det.id_validacion=val.id_validacion WHERE det.id_detalle_recibo_nomina=$IdDetail";
        }

        $Result = $DB->ConsultaSelect("CFDI", $Query);
        if($Result['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al recuperar el <b>Acuse de validación</b></p><br>Detalles:<br><br>".$Result['Estado']);
            return 0;
        }
        
        $Acuse = $Result['ArrayDatos'][0];
        
        if(!file_exists($Acuse['ruta_acuse']))
        {
            XML::XMLReponse("Error", 0, "<p><b>Error</b> no existe el acuse de validación</p>");
            return 0;
        }
        
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->load($Acuse['ruta_acuse']);
        $root = $doc->firstChild;
        $IdAcuse = $doc->createElement("IdValidacion", $Acuse['id_validacion']);
        $root->appendChild($IdAcuse);
        header('Content-Type: text/xml');
        echo $doc->saveXML(); 
        
    }
    
    private function GetXmlValidationReceipt()
    {
        $DB = new DataBase();
        $ReceiptPath = filter_input(INPUT_POST, "ReceiptPath");
        $IdReceipt = filter_input(INPUT_POST, "IdReceipt");
        $content = filter_input(INPUT_POST, "content");
        
        if(!strlen($ReceiptPath)>0 or !(file_exists($ReceiptPath)))
        {
            $QueryGetReceipt = "SELECT ruta_acuse FROM validacion_$content WHERE id_validacion = $IdReceipt";
            $ResultGetReceipt = $DB->ConsultaSelect("CFDI", $QueryGetReceipt);
            if($ResultGetReceipt['Estado']!=1)
            {
                XML::XmlResponse("Error", 0, "<p><b>Error</b> al intentar recuperar el comprobante de validación</p><br>Detalles:<br><br>".$ResultGetReceipt['Estado']);
                return 0;
            }
            $ReceiptPath = $ResultGetReceipt['ArrayDatos'][0]['ruta_acuse'];
        }
        
        if (file_exists($ReceiptPath)) 
        {          
            $xml = simplexml_load_file($ReceiptPath);    
            header('Content-Type: text/xml'); 
            echo $xml->saveXML(); 
        }   
        else
            XML::XmlResponse ("Error", 0, "<p><b>Error</b>, el comprobante de validación solicitado no fué encontrado</p>");
    }
    
    /* Recibe un objeto tipo DomDocument, el cual es el XML devuelto después de la validación */
    function InsertValidationCfdi($content,$validacion,$ReceiptPath)
    {        
        $DB = new DataBase();
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
        
        $q="INSERT INTO validacion_$content (FechaHora_envio, FechaHora_respuesta, emisor_rfc,"
                . "receptor_rfc, total_factura, uuid, codigo_estatus, estado, md5, web_service, ruta_acuse)"
                . " VALUES ('$FechaHoraEnvio', '$FechaHoraRespuesta', '$EmisorRfc', '$ReceptorRfc'"
                . ", $TotalFactura, '$uuid', '$CodigoEstatus', '$Estado', '$md5', '$webService', '$ReceiptPath')";
        
        $NewIdReceipt = $DB->ConsultaInsertReturnId("CFDI", $q);
        
        if(!$NewIdReceipt>0)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al registrar la validación del nuevo Cfdi</p><br>Detalles:<br><br>$NewIdReceipt");
            return 0;
        }
        
        return $NewIdReceipt;
    } 
}

$Receipt = new Receipt();