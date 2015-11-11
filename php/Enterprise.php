<?php
/**
 * Description of Enterprise
 *
 * @author Daniel
 */
$RoutFile = filter_input(INPUT_SERVER, "DOCUMENT_ROOT"); /* /var/services/web */

require_once 'XML.php';
require_once 'DataBase.php';

class Enterprise {
    public function __construct() {
        $option = filter_input(INPUT_POST, "option");
        switch ($option)
        {
            case 'GetListEnterprisesXml': $this->GetListEnterprisesXml();  break; 
            case 'GetSystemEnterprises': $this->GetSystemEnterprises(); break;
            case 'NewEnterpriseSystem': $this->NewEnterpriseSystem(); break;
        }    
    }
    
    private function NewEnterpriseSystem()
    {
        $DB = new DataBase();
        $IdUser = filter_input(INPUT_POST, "IdUser");
        $UserName = filter_input(INPUT_POST, "UserName");
        $EnterpriseAlias = filter_input(INPUT_POST, "EnterpriseAlias");
        $EnterpriseName = filter_input(INPUT_POST, "NewNameEnterprise");
        $EnterpriseRFC = filter_input(INPUT_POST, "NewRfcEnterprise");
        $EnterprisePassword = filter_input(INPUT_POST, "NewPasswordEnterprise");
        $RoutFile = filter_input(INPUT_SERVER, "DOCUMENT_ROOT"); /* /var/services/web */
        
        if(!file_exists($RoutFile."/Config/Enterprise"))
        {
            if(!($mkdir = mkdir($RoutFile."/Config/Enterprise")))
            {
                XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear el directorio de Empresas</p><br>Detalles:<br><br>$mkdir");
                return 0;
            }
        }
        
        $QueryComplement = "";
        
        foreach ($_FILES as $FileName =>$value)
        {
            
            if($value['error']!= UPLOAD_ERR_OK )
            {
                XML::XmlResponse ("Error", 0, "<p>".$_FILES[$FileName]['error'] .'</p>');
                return 0;
            }
            
            $TmpName = $value['tmp_name'];
            $name = $value['name'];
            
            $QueryComplement.=",'/Config/Enterprise/$FileName$name$EnterpriseAlias'";

            if(!($move = move_uploaded_file($TmpName, $RoutFile."/Config/Enterprise/$EnterpriseAlias")))
            {
                XML::XmlResponse("Error", 0, "<p><b>Error</b> al escribir en el servidor el documento $FileName</p><br>Detalles:<br><br>$move");
                return 0;
            }
        }
        
        if(($CreateEnterprise = $DB->CreateEnterpriseInstance($EnterpriseName)!=1))
            return 0;        
        

        $QInsertEnterprise = "INSERT INTO Enterprises (Alias, EnterpriseName, RFC, Password, PublicFile, PrivateFile) VALUES "
                . "('$EnterpriseAlias', '$EnterpriseName', '$EnterpriseRFC', '$EnterprisePassword' $QueryComplement)";
        
        if(($ResultInser = $DB->ConsultaQuery("CSDOCS_CFDI", $QInsertEnterprise))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al registrar la nueva empresa</p><br>Detalles:<br><br>$ResultInser");
            return 0;
        }
        
        XML::XmlResponse("NewEnterprise", 1, "Empresa $EnterpriseName dada de alta con éxito");
        
    }
    
    private function GetSystemEnterprises()
    {
        $DB = new DataBase();
        $QueryEnterprises = "SELECT *FROM Enterprises";
        $ResultQuery = $DB->ConsultaSelect("CSDOCS_CFDI", $QueryEnterprises);
        
        if($ResultQuery['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al obtener el listado de empresas</p><br>Detalles:<br><br>".$ResultQuery['Estado']);
            return 0;
        }
        
        $EnterprisesList = $ResultQuery['ArrayDatos'];
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Enterprises');
        for($cont = 0; $cont<count($EnterprisesList); $cont++)
        {
            $Enterprise = $doc->createElement("Enterprise");
            $IdEnterprise = $doc->createElement("IdEnterprise", $EnterprisesList[$cont]['IdEnterprise']);
            $Enterprise->appendChild($IdEnterprise);
            $EnterpriseName = $doc->createElement("EnterpriseName", $EnterprisesList[$cont]['EnterpriseName']);
            $Enterprise->appendChild($EnterpriseName);
            $Alias = $doc->createElement("EnterpriseAlias", $EnterprisesList[$cont]['Alias']);
            $Enterprise->appendChild($Alias);
            $root->appendChild($Enterprise);
        }
        $doc->appendChild($root);
        header ("Content-Type:text/xml");
        echo $doc->saveXML();
    }
    
    private function GetListEnterprisesXml()
    {
        $DB = new DataBase();
        $content = filter_input(INPUT_POST, "content");
        
        $QuerySelect = '';
        if(strcasecmp($content, "provider")==0)
            $QuerySelect = "SELECT *FROM emisor_factura_proveedor";
        if(strcasecmp($content, "client")==0)
            $QuerySelect = "SELECT *FROM emisor_factura_cliente";
        if(strcasecmp($content, "payroll")==0)
            $QuerySelect = "SELECT *FROM emisor_recibo_nomina";
            
        
        $Result = $DB->ConsultaSelect("CFDI", $QuerySelect);
        if($Result['Estado']!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al obtener el listado de empresas</p><br>Detalles:<br><br>".$Result['Estado']);
            return 0;
        }

        $Enterprises = $Result['ArrayDatos'];
        $doc  = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Enterprises');
        
        for($cont=0; $cont < count($Enterprises); $cont++)
        {            
            $Enterprise = $doc->createElement("Enterprise");
            $IdEnterprise = $doc->createElement("IdEnterprise", $Enterprises[$cont]["idemisor"]);
            $Enterprise->appendChild($IdEnterprise);
            $EnterpriseName = $doc->createElement("Name", $Enterprises[$cont]['nombre']);
            $Enterprise->appendChild($EnterpriseName);
            $EnterpriseRFC = $doc->createElement("RFC", $Enterprises[$cont]['rfc']);
            $Enterprise->appendChild($EnterpriseRFC);  
            $root->appendChild($Enterprise);
        }
        $doc->appendChild($root);
        header ("Content-Type:text/xml");
        echo $doc->saveXML();
    }
}

$enterprise = new Enterprise();
