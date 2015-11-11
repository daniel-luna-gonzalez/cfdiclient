<?php

/*
 * Clase que administra la Base de Datos
 */

/**
 *
 * @author daniel
 */
$RoutFile = filter_input(INPUT_SERVER, "DOCUMENT_ROOT"); /* /var/services/web */
require_once "XML.php";
class DataBase {
    
    public function __construct()
    {        
        $this->Ajax();
    }
    
    private function Ajax()
    {
        $option = filter_input(INPUT_POST, "option");
        switch ($option)
        {
            case 'CreateInstanciaCSDOCS': $this->CreateInstanciaCSDOCS(); break;
            case 'CreateCfdiDataBase': $this->CreateCfdiDb(); break;
        }
    }
    
    public function Conexion()
    {        
        $RoutFile = filter_input(INPUT_SERVER, "DOCUMENT_ROOT"); /* /var/services/web */
        $RoutFile.="/Config/ConexionBD/BD.ini";
                
        if(!file_exists($RoutFile))
        {
            echo "<p>No existe el archivo de Conexión.</p>"; 
            return 0;            
        }
        
        $Conexion=parse_ini_file ($RoutFile,true);

        $User=$Conexion['Conexion']['User'];
        $Password=$Conexion['Conexion']['Password'];
        $Host=$Conexion['Conexion']['Host'];
        error_reporting(E_ALL ^ E_DEPRECATED);
        $enlace =  mysql_connect($Host, $User, $Password);        
        mysql_set_charset('utf8');
        return $enlace;
    }
    
    /***************************************************************************
     * 
     * Se crea una Instancia llamada CS-DOCS la cual contiene una tabla llamada Instancias
     * Esta tabla se utiliza para llevar un registro de instancias creadas por el administrador.
     * 
     ***************************************************************************/
    function CreateInstanciaCSDOCS()
    {
         
        $CreateCsDocs="CREATE DATABASE IF NOT EXISTS `CSDOCS_CFDI` /*!40100 DEFAULT CHARACTER SET utf8 */";
        if(($ResultCreateCsDocs=  $this->crear_tabla("",$CreateCsDocs))!=1)
        {
            echo  "<p><b>Error</b> al crear la instancia CSDocs $ResultCreateCsDocs</p>";
            return;
        }   
            
       $CreateInstances = "CREATE TABLE IF NOT EXISTS `Enterprises` (IdEnterprise INT NOT NULL AUTO_INCREMENT,"
               . "EnterpriseName VARCHAR(100) NOT NULL,"
               . "Alias VARCHAR(50) NOT NULL,"
               . "RFC VARCHAR(50) NOT NULL,"
               . "Password VARCHAR(20) NOT NULL,"
               . "PublicFile TEXT,"
               . "PrivateFile TEXT,"
               . "PRIMARY KEY (`IdEnterprise`)) ENGINE=InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET=utf8";
       
       if(($ResultCreateInstances = $this->crear_tabla("CSDOCS_CFDI",  $CreateInstances))!=1)
        {
            echo "<p><b>Error</b> al crear la Tabla Instancias en CSDocs. $ResultCreateInstances</p>";
            return 0;
        }               
                   
       $CreateUsers="CREATE TABLE IF NOT EXISTS `Users` (IdUser INT(11) NOT NULL AUTO_INCREMENT,"
               . "IdEnterprise INT NOT NULL,"
               . "UserName VARCHAR(50) NOT NULL,"
               . "Password VARCHAR(50) NOT NULL,"
               . "PRIMARY KEY (`IdUser`)) ENGINE=InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET=utf8";
       
       if(($ResultCreateUsers = $this->ConsultaQuery("CSDOCS_CFDI",$CreateUsers))!=1)
        {
            echo "<p><b>Error</b> al crear la Tabla Usuarios en CSDocs $ResultCreateUsers</p>";
            return 0;
        }                
        
        $ExistRoot = $this->ExistRootUser();
        if($ExistRoot==0)
        {
            $this->InsertRootUser();
        }
        else if($ExistRoot!=1)
        {
            echo $ExistRoot;
            return;
        }
            
//        XML::XmlResponse("InsertRoot", 1, "");
        
        $IfExistCfdi = $this->IfExistCfdi('CFDI');
        if($IfExistCfdi==0)
        {
            if($this->CreateEnterpriseInstance('CFDI')==0)
            {
                $this->DeleteCfdi('CFDI');
            }
            else
                XML::XmlResponse ("CreateCSDocs",1, "<p>CFDI creado...</p>");     
        }
        else if($IfExistCfdi!=1)
            XML::XmlResponse ("Error", 0, "<p>$IfExistCfdi</p>");   
        else
            XML::XmlResponse ("CreateCSDocs",1, "<p>Ya existe...</p>");     
            
    }  
    
    private function IfExistCfdi($DataBaseName)
    {
        $Exist = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$DataBaseName'";
        $Res = $this->ConsultaSelect("", $Exist);
        if($Res['Estado']!=1)
        {
            return $Res['Estado'];
        }
        
        if(count($Res['ArrayDatos'])>0)
            return 1;
        else
            return 0;
           
    }
    
    public function CreateEnterpriseInstance($DataBaseName)
    {                
        
        $CreateDataBase = "CREATE DATABASE IF NOT EXISTS `$DataBaseName` /*!40100 DEFAULT CHARACTER SET utf8 */;";
        if(($ResultCreateDataBase = $this->ConsultaQuery('', $CreateDataBase))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Base de Datos</p><br>Detalles:<br><br>$ResultCreateDataBase");
            return 0;
        }
                
        //SE CREA LA TABLA EMISOR DE RECIBO DE NOMINA         
        $CreateEmisorNomina = "CREATE TABLE IF NOT EXISTS `emisor_recibo_nomina` (
          `idemisor` int(11) NOT NULL AUTO_INCREMENT,
          `rfc` varchar(100) NOT NULL,
          `nombre` varchar(45) NOT NULL,
          `pais` varchar(45) NOT NULL,
          `calle` varchar(200) NOT NULL,
          `estado` varchar(45) NOT NULL,
          `colonia` varchar(200) NOT NULL,
          `municipio` varchar(100) NOT NULL,
          `noExterior` int(11) DEFAULT NULL,
          `cp` int(11) NOT NULL,
          `pais_expedido` varchar(60) DEFAULT NULL,
          `calle_expedido` varchar(200) DEFAULT NULL,
          `estado_expedido` varchar(45) DEFAULT NULL,
          `colonia_expedido` varchar(200) DEFAULT NULL,
          `noExterior_expedido` int(11) DEFAULT NULL,
          `cp_expedido` int(11) DEFAULT NULL,
          PRIMARY KEY (`idemisor`,`rfc`)
        ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
        
        if(($resulCreateEmisorNomina = $this->ConsultaQuery("$DataBaseName", $CreateEmisorNomina))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Emisor Recibo de Nómina </p><br>Detalles:<br><br>$resulCreateEmisorNomina");
            return 0;
        }

        // SE CREA LA TABLA RECEPTOR DE RECIBO DE NOMINA
        $ReceptorNomina = "CREATE TABLE IF NOT EXISTS `receptor_recibo_nomina` (
        `id_receptor` int(11) NOT NULL AUTO_INCREMENT,
        `curp` varchar(200) NOT NULL,
        `rfc` varchar(100) DEFAULT NULL,
        `nombre` varchar(200) DEFAULT NULL,
        `pais` varchar(45) DEFAULT NULL,
        `calle` varchar(200) DEFAULT NULL,
        `estado` varchar(200) DEFAULT NULL,
        `colonia` varchar(200) DEFAULT NULL,
        `municipio` varchar(200) DEFAULT NULL,
        `noExterior` varchar(10) DEFAULT NULL,
        `noInterior` varchar(10) DEFAULT NULL,
        `cp` varchar(10) DEFAULT NULL,
        PRIMARY KEY (`id_receptor`,`curp`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($resulReceptorNomina = $this->ConsultaQuery("$DataBaseName", $ReceptorNomina))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Receptor Recibo de Nómina </p><br>Detalles:<br><br>$resulReceptorNomina");
            return 0;
        }
                 
            // SE CREA LA TABLA DETALLE DE RECIBO DE NOMINA
            
        $DetalleNomina = "CREATE TABLE IF NOT EXISTS `detalle_recibo_nomina` (
        `id_detalle_recibo_nomina` int(11) NOT NULL AUTO_INCREMENT,
        `id_emisor` int(11) NOT NULL,
        `id_receptor` int(11) NOT NULL,
        `id_validacion` int(11) NOT NULL,
        `curp` varchar(60) NOT NULL,
        `registro_patronal` varchar(250) DEFAULT NULL,
        `NumEmpleado` int(11) DEFAULT NULL,
        `tipoRegimen` int(11) DEFAULT NULL,
        `NumSegSocial` varchar(250) DEFAULT NULL,
        `FechaPago` date DEFAULT NULL,
        `FechaInicialPago` date DEFAULT NULL,
        `FechaFinalPago` date DEFAULT NULL,
        `NumDiasPagados` decimal(10,0) DEFAULT NULL,
        `departamento` varchar(200) DEFAULT NULL,
        `clabe` varchar(200) DEFAULT NULL,
        `banco` varchar(100) DEFAULT NULL,
        `FechaInicioLaboral` date DEFAULT NULL,
        `antiguedad` int(11) DEFAULT NULL,
        `puesto` varchar(200) DEFAULT NULL,
        `TipoContrato` varchar(200) DEFAULT NULL,
        `TipoJornada` varchar(70) DEFAULT NULL,
        `PeriodicidadPago` varchar(45) DEFAULT NULL,
        `SalarioBaseCotApor` float DEFAULT NULL,
        `RiesgoPuesto` int(11) DEFAULT NULL,
        `SalarioDiarioIntegrado` float DEFAULT NULL,
        `id_CentroCosto` int(11) DEFAULT '0',
        `xml_ruta` varchar(220) DEFAULT NULL,
        `pdf_ruta` varchar(220) DEFAULT NULL,
        `tipo_archivo` varchar(45) DEFAULT 'original',
        `tipo_archivo_pdf` varchar(45) DEFAULT 'original',
         Full TEXT, 
         PRIMARY KEY (`id_detalle_recibo_nomina`,`curp`,`id_validacion`),
         FULLTEXT (Full),
         KEY `FK1` (`id_emisor`),
         KEY `FK2_idx` (`id_receptor`),
         CONSTRAINT `FK1` FOREIGN KEY (`id_emisor`) REFERENCES `emisor_recibo_nomina` (`idemisor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
         CONSTRAINT `FK2` FOREIGN KEY (`id_receptor`) REFERENCES `receptor_recibo_nomina` (`id_receptor`) ON DELETE NO ACTION ON UPDATE NO ACTION
         ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1
        ";
        
        if(($ResultDetalleNomina = $this->ConsultaQuery("$DataBaseName", $DetalleNomina))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Detalle Recibo de Nómina </p><br>Detalles:<br><br>$ResultDetalleNomina");
            return 0;
        }

            /******************** Histórico Nomina*********************/
        $HistorialNomina = "CREATE TABLE IF NOT EXISTS `historial_nomina` (
        `id_historial` int(11) NOT NULL AUTO_INCREMENT,
        `id_validacion` int(11) NOT NULL,
        `id_detalle` int(11) NOT NULL,
        `id_usuario` int(11) DEFAULT NULL,
        `fecha_hora` datetime NOT NULL,
        `ruta_xml` varchar(250) NOT NULL,
        `ruta_pdf` varchar(250) DEFAULT NULL,
        `tipo_archivo` varchar(45) NOT NULL,
        PRIMARY KEY (`id_historial`,`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)'";
        
        if(($ResultHistorialNomina = $this->ConsultaQuery("$DataBaseName", $HistorialNomina))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Historial Recibo de Nómina </p><br>Detalles:<br><br>$ResultHistorialNomina");
            return 0;
        }
            
        $ValidacionNomina = "CREATE TABLE IF NOT EXISTS `validacion_nomina` (
        `id_validacion` int(11) NOT NULL AUTO_INCREMENT,
        `FechaHora_envio` datetime NOT NULL,
        `FechaHora_respuesta` datetime NOT NULL,
        `emisor_rfc` varchar(100) NOT NULL,
        `receptor_rfc` varchar(100) NOT NULL,                
        `total_factura` double NOT NULL,
        `uuid` varchar(100) NOT NULL,
        `codigo_estatus` varchar(100) NOT NULL,
        `estado` varchar(100) NOT NULL,
        `md5` varchar(100) NOT NULL,
        `web_service` varchar(250) NOT NULL,
        `ruta_acuse` varchar(250) NOT NULL,
        PRIMARY KEY (`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($ResultValidacionNomina = $this->ConsultaQuery("$DataBaseName", $ValidacionNomina))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Validación Recibo de Nómina </p><br>Detalles:<br><br>$ResultValidacionNomina");
            return 0;
        }
                                                 
                /*      Validación  CFDI    */
                
                
        $ValidacionProveedor = "CREATE TABLE IF NOT EXISTS `validacion_proveedor` (
        `id_validacion` int(11) NOT NULL AUTO_INCREMENT,
        `FechaHora_envio` datetime NOT NULL,
        `FechaHora_respuesta` datetime NOT NULL,
        `emisor_rfc` varchar(100) NOT NULL,
        `receptor_rfc` varchar(100) NOT NULL,                
        `total_factura` double NOT NULL,
        `uuid` varchar(100) NOT NULL,
        `codigo_estatus` varchar(100) NOT NULL,
        `estado` varchar(100) NOT NULL,
        `md5` varchar(100) NOT NULL,
        `web_service` varchar(250) NOT NULL,
        `ruta_acuse` varchar(250) NOT NULL,
        PRIMARY KEY (`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($ResultValidacionProveedor = $this->ConsultaQuery("$DataBaseName", $ValidacionProveedor))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la Validación Proveedor </p><br>Detalles:<br><br>$ResultValidacionProveedor");
            return 0;
        }

        $Existe = "CREATE TABLE IF NOT EXISTS `existe` (
        `idexiste` int(11) NOT NULL AUTO_INCREMENT,
        `id_emisor` int(11) NOT NULL,
        `nombre` varchar(200) DEFAULT NULL,
        `NoIntentos` int(11) DEFAULT '0',
        PRIMARY KEY (`idexiste`,`id_emisor`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabla que registra el numero de intentos que se trata de procesar un recibo de nomina o algun otro comprobante'";
            
        if(($ResultExiste = $this->ConsultaQuery("$DataBaseName", $Existe))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Existe </p><br>Detalles:<br><br>$ResultExiste");
            return 0;
        }
            
        $ResgistroPdf = "CREATE TABLE IF NOT EXISTS `registro_pdf` (
        `id_pdf` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(200) DEFAULT NULL,
        PRIMARY KEY (`id_pdf`)
        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Tabla que contiene los nombres de los PDF pendientes por insertar en la BD'";
        
        if(($ResultRegistroPdf = $this->ConsultaQuery("$DataBaseName", $ResgistroPdf))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Registro Pdf </p><br>Detalles:<br><br>$ResultRegistroPdf");
            return 0;
        }
 
        $sql="CREATE TABLE IF NOT EXISTS `registro_xml` (
        `id_registro_xml` int(11) NOT NULL AUTO_INCREMENT,
        `id_detalle` int(11) NOT NULL,
        `nombre_xml` varchar(200) DEFAULT NULL,
        PRIMARY KEY (`id_registro_xml`,`id_detalle`)
        ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1";
                        
        /*UTILIZADO EN CARGA RECIBO DE NOMINA */
//        $ViewInsertPdf = "CREATE OR REPLACE VIEW `insert_pdf` AS select `registro_xml`.`id_detalle` AS `id_detalle`,`registro_pdf`.`nombre` AS `nombre` from (`registro_xml` join `registro_pdf` on((`registro_xml`.`nombre_xml` = `registro_pdf`.`nombre`)))";
//        
//        if(($ResultViewInsertPdf = $this->ConsultaQuery("CSDOCS_CFDI", $ViewInsertPdf))!=1)
//        {
//            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear la vista pdf insert </p><br>Detalles:<br><br>$ResultViewInsertPdf");
//            return 0;
//        }
        
            /* Tabla emisor Factura Cliente */    
        $EmisorCliente = "CREATE TABLE IF NOT EXISTS `emisor_factura_cliente` (
        `idemisor` int(11) NOT NULL AUTO_INCREMENT,
        `rfc` varchar(100) NOT NULL,
        `nombre` varchar(100) NOT NULL,
        `pais` varchar(45) NOT NULL,
        `calle` varchar(200) NOT NULL,
        `estado` varchar(45) NOT NULL,
        `colonia` varchar(200) NOT NULL,
        `municipio` varchar(100) NOT NULL,
        `noExterior` varchar(10) DEFAULT NULL,
        `cp` varchar(10) NOT NULL,
        `localidad` varchar(250) DEFAULT NULL,
        `expedidoCalle` varchar(150) DEFAULT NULL,
        `expedidoNoExterior` varchar(10) DEFAULT NULL,
        `expedidoColonia` varchar(150) DEFAULT NULL,
        `expedidoLocalidad` varchar(150) DEFAULT NULL,
        `expedidoMunicipio` varchar(150) DEFAULT NULL,
        `expedidoEstado` varchar(150) DEFAULT NULL,
        `expedidoPais` varchar(150) DEFAULT NULL,
        `expedidoCP` varchar(10) DEFAULT NULL,
        PRIMARY KEY (`idemisor`,`rfc`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($RecultEmisorCliente = $this->ConsultaQuery("$DataBaseName", $EmisorCliente))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Emisor Cliente </p><br>Detalles:<br><br>$RecultEmisorCliente");
            return 0;
        }
               
        $ReceptorCliente = "CREATE TABLE IF NOT EXISTS `receptor_factura_cliente` (
        `id_receptor` int(11) NOT NULL AUTO_INCREMENT,
        `rfc` varchar(200) NOT NULL,
        `nombre` varchar(200) DEFAULT NULL,
        `pais` varchar(45) DEFAULT NULL,
        `calle` varchar(200) DEFAULT NULL,
        `estado` varchar(200) DEFAULT NULL,
        `colonia` varchar(200) DEFAULT NULL,
        `municipio` varchar(200) DEFAULT NULL,
        `noExterior` varchar(10) DEFAULT NULL,
        `noInterior` varchar(10) DEFAULT NULL,
        `cp` varchar(10) DEFAULT NULL,
        `localidad` varchar(150) DEFAULT NULL,
        PRIMARY KEY (`id_receptor`,`rfc`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($ResultReceptorCliente = $this->ConsultaQuery("$DataBaseName", $ReceptorCliente))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Receptor Cliente </p><br>Detalles:<br><br>$ResultReceptorCliente");
            return 0;
        }
        
            /* Tabla detalle factura cliente */
        $DetalleCliente = "CREATE TABLE IF NOT EXISTS `detalle_factura_cliente` (
        `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
        `id_emisor` int(11) NOT NULL DEFAULT '0',
        `id_receptor` int(11) NOT NULL DEFAULT '0',
        `id_validacion` int(11) NOT NULL,
        `rfc_cliente` varchar(70) DEFAULT NULL,
        `serie` varchar(45) DEFAULT NULL,
        `folio` varchar(45) DEFAULT NULL,
        `fecha` date DEFAULT NULL,
        `formaDePago` varchar(200) DEFAULT NULL,
        `subTotal` double DEFAULT NULL,
        `descuento` double DEFAULT NULL,
        `total` double DEFAULT NULL,
        `metodoDePago` varchar(45) DEFAULT NULL,
        `tipoDeComprobante` varchar(100) DEFAULT NULL,
        `TipoCambio` decimal(10,0) DEFAULT NULL,
        `Moneda` varchar(45) DEFAULT NULL,
        `ruta_xml` varchar(250) DEFAULT NULL,
        `ruta_pdf` varchar(250) DEFAULT NULL,
        `tipo_archivo` varchar(45) DEFAULT 'original',
        `tipo_archivo_pdf` varchar(45) DEFAULT 'original',
         Full TEXT,
         PRIMARY KEY (`id_detalle`,`id_validacion`),  
         FULLTEXT (Full),
         KEY `fk1_idx` (`id_emisor`),
         KEY `fk2_idx` (`id_receptor`),
         CONSTRAINT `f2` FOREIGN KEY (`id_receptor`) REFERENCES `receptor_factura_cliente` (`id_receptor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
         CONSTRAINT `f1` FOREIGN KEY (`id_emisor`) REFERENCES `emisor_factura_cliente` (`idemisor`) ON DELETE NO ACTION ON UPDATE NO ACTION
        ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1";
        
        if(($ResultDetalleCliente = $this->ConsultaQuery("$DataBaseName", $DetalleCliente))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Detalle Cliente </p><br>Detalles:<br><br>$ResultDetalleCliente");
            return 0;
        }
            
        $HistorialCliente = "CREATE TABLE IF NOT EXISTS `historial_cliente` (
        `id_historial` int(11) NOT NULL AUTO_INCREMENT,
        `id_validacion` int(11) NOT NULL,
        `id_detalle` int(11) NOT NULL,
        `id_usuario` int(11) DEFAULT NULL,
        `fecha_hora` datetime NOT NULL,
        `ruta_xml` varchar(250) NOT NULL,
        `ruta_pdf` varchar(250) DEFAULT NULL,
        `tipo_archivo` varchar(45) NOT NULL,
        PRIMARY KEY (`id_historial`,`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)'";
        
        if(($ResultHistorialCliente = $this->ConsultaQuery("$DataBaseName", $HistorialCliente))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Historial Cliente </p><br>Detalles:<br><br>$ResultHistorialCliente");
            return 0;
        } 
                
        $ValidacionCliente = "CREATE TABLE IF NOT EXISTS `validacion_cliente` (
        `id_validacion` int(11) NOT NULL AUTO_INCREMENT,
        `FechaHora_envio` datetime NOT NULL,
        `FechaHora_respuesta` datetime NOT NULL,
        `emisor_rfc` varchar(100) NOT NULL,
        `receptor_rfc` varchar(100) NOT NULL,                
        `total_factura` double NOT NULL,
        `uuid` varchar(100) NOT NULL,
        `codigo_estatus` varchar(100) NOT NULL,
        `estado` varchar(100) NOT NULL,
        `md5` varchar(100) NOT NULL,
        `web_service` varchar(250) NOT NULL,
        `ruta_acuse` varchar(250) NOT NULL,
        PRIMARY KEY (`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($ResultValidacionCliente = $this->ConsultaQuery("$DataBaseName", $ValidacionCliente))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Validación Cliente </p><br>Detalles:<br><br>$ResultValidacionCliente");
            return 0;
        }            
            
            /*  Facturas Proveedor */
        $EmisorProveedor = "CREATE TABLE IF NOT EXISTS `emisor_factura_proveedor` (
        `idemisor` int(11) NOT NULL AUTO_INCREMENT,
        `rfc` varchar(100) NOT NULL,
        `nombre` varchar(100) NOT NULL,
        PRIMARY KEY (`idemisor`,`rfc`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            
        if(($ResultEmisorProveedor = $this->ConsultaQuery("$DataBaseName", $EmisorProveedor))!=1)    
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Emisor Proveedor </p><br>Detalles:<br><br>$ResultEmisorProveedor");
            return 0;
        }
                                                                        
        $ReceptorProveedor = "CREATE TABLE IF NOT EXISTS `receptor_factura_proveedor` (
        `id_receptor` int(11) NOT NULL AUTO_INCREMENT,
        `rfc` varchar(200) NOT NULL,
        `nombre` varchar(200) DEFAULT NULL,
        `pais` varchar(45) DEFAULT NULL,
        `calle` varchar(200) DEFAULT NULL,
        `estado` varchar(200) DEFAULT NULL,
        `colonia` varchar(200) DEFAULT NULL,
        `municipio` varchar(200) DEFAULT NULL,
        `noExterior` varchar(10) DEFAULT NULL,
        `noInterior` varchar(10) DEFAULT NULL,
        `cp` varchar(10) DEFAULT NULL,
        `localidad` varchar(150) DEFAULT NULL,
        PRIMARY KEY (`id_receptor`,`rfc`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";   
            
        if(($ResultReceptorProveedor = $this->ConsultaQuery("$DataBaseName", $ReceptorProveedor))!=1)    
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Receptor Proveedor </p><br>Detalles:<br><br>$ResultReceptorProveedor");
            return 0;
        }
            
            
        $DetalleProveedor = "CREATE TABLE IF NOT EXISTS `detalle_factura_proveedor` (
        `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
        `id_emisor` int(11) NOT NULL DEFAULT '0',
        `id_receptor` int(11) NOT NULL DEFAULT '0',
        `id_validacion` int(11) NOT NULL,
        `rfc_cliente` varchar(70) DEFAULT NULL,
        `serie` varchar(45) DEFAULT NULL,
        `folio` varchar(45) DEFAULT NULL,
        `fecha` date DEFAULT NULL,
        `formaDePago` varchar(200) DEFAULT NULL,
        `subTotal` double DEFAULT NULL,
        `descuento` double DEFAULT NULL,
        `total` double DEFAULT NULL,
        `metodoDePago` varchar(45) DEFAULT NULL,
        `tipoDeComprobante` varchar(100) DEFAULT NULL,
        `TipoCambio` decimal(10,0) DEFAULT NULL,
        `Moneda` varchar(45) DEFAULT NULL,
        `ruta_pdf` varchar(250) DEFAULT NULL,
        `ruta_xml` varchar(250) DEFAULT NULL,
        `tipo_archivo` varchar(45) DEFAULT 'original',
        `tipo_archivo_pdf` varchar(45) DEFAULT 'original',
         Full TEXT,
         PRIMARY KEY (`id_detalle`,`id_validacion`),
         FULLTEXT (Full),
         KEY `fk1_idx` (`id_emisor`),
         KEY `fk2_idx` (`id_receptor`),
         CONSTRAINT `f3` FOREIGN KEY (`id_emisor`) REFERENCES `emisor_factura_proveedor` (`idemisor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
         CONSTRAINT `f4` FOREIGN KEY (`id_receptor`) REFERENCES `receptor_factura_proveedor` (`id_receptor`) ON DELETE NO ACTION ON UPDATE NO ACTION
        )  ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1";
        
        if(($ResultDetalleProveedor = $this->ConsultaQuery("$DataBaseName", $DetalleProveedor))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Detalle Proveedor </p><br>Detalles:<br><br>$ResultDetalleProveedor");
            return 0;
        }
            
        $HistorialProveedor = "CREATE TABLE IF NOT EXISTS `historial_proveedor` (
        `id_historial` int(11) NOT NULL AUTO_INCREMENT,
        `id_validacion` int(11) NOT NULL,
        `id_detalle` int(11) NOT NULL,
        `id_usuario` int(11) DEFAULT NULL,
        `fecha_hora` datetime NOT NULL,
        `ruta_xml` varchar(250) NOT NULL,
        `ruta_pdf` varchar(250) DEFAULT NULL,
        `tipo_archivo` varchar(45) NOT NULL,
        PRIMARY KEY (`id_historial`,`id_validacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)'";
          
        if(($ResultHistorialProveedor = $this->ConsultaQuery("$DataBaseName", $HistorialProveedor))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Historial Proveedor </p><br>Detalles:<br><br>$ResultHistorialProveedor");
            return 0;
        }
        
          /*Registro de Usuarios */  
        $Users = "CREATE TABLE IF NOT EXISTS `Usuarios` (
        `IdUsuario` int(11) NOT NULL AUTO_INCREMENT,
        `IdCorreo` int(11) NOT NULL,
        `nombre_usuario` varchar(50) NOT NULL,
        `password` varchar(45) NOT NULL,
        `nombre` varchar(45) NOT NULL,
        `apellido_materno` varchar(45) NOT NULL,
        `apellido_paterno` varchar(45) DEFAULT NULL,
        `curp` varchar(45) DEFAULT NULL,
        `fecha_nac` date DEFAULT NULL,
        `tipo_usuario` varchar(45) NOT NULL,
        `estatus` int(11) DEFAULT '1',
        `fecha_alta` DATETIME  DEFAULT NULL,
        `fecha_baja` DATETIME  DEFAULT NULL,
        PRIMARY KEY (`IdUsuario`,`nombre_usuario`)
        ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8";
        
        if(($ResultUsers = $this->ConsultaQuery("$DataBaseName", $Users))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Usuarios </p><br>Detalles:<br><br>$ResultUsers");
            return 0;
        }
                           
        $Correo = "CREATE TABLE IF NOT EXISTS `correo` (
        `id_correo` int(11) NOT NULL AUTO_INCREMENT,
        `id_empleado` int(11) NOT NULL,
        `servidor` varchar(45) NULL,
        `smtp` varchar(45) DEFAULT NULL,
        PuertoSmtp varchar(4) NOT NULL,
        `puerto` varchar(45) DEFAULT NULL,
        `seguridad` varchar(45) DEFAULT NULL,
        `auth` varchar(10) DEFAULT NULL,
        `password` varchar(45) DEFAULT NULL,
        `correo` varchar(45) DEFAULT NULL,
        `titulo_mostrar` varchar(45) DEFAULT NULL,
        `host_imap` varchar(150) DEFAULT NULL,
        `estatus` int(11) DEFAULT '1',
        PRIMARY KEY (`id_correo`,`id_empleado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        if(($ResultCorreo = $this->ConsultaQuery("$DataBaseName", $Correo))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Correo </p><br>Detalles:<br><br>$ResultCorreo");
            return 0;
        }
            
        /* Vista utilizada en Carga_Nomina_XML */
        $VistaExistDetalle = "CREATE OR REPLACE VIEW exist_detalle AS select id_detalle_recibo_nomina, id_emisor, id_receptor, FechaPago, curp from detalle_recibo_nomina";
        
        if(($ResultVistaExistDetalle = $this->ConsultaQuery("$DataBaseName", $VistaExistDetalle))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Vista Existe Detalle </p><br>Detalles:<br><br>$ResultVistaExistDetalle");
            return 0;
        }
            
            // SE CREA LA TABLA RECEPTOR DE RECIBO DE NOMINA
        $MotorCorreo = "CREATE TABLE IF NOT EXISTS `motor_correo` (
        `id_motor` int(11) NOT NULL AUTO_INCREMENT,
        `id_correo` varchar(60) DEFAULT NULL,
        `id_emisor` int(11) DEFAULT NULL,
        `id_receptor` int(11) DEFAULT NULL,
        `id_detalle` int(11) DEFAULT NULL,
        `emisor_correo` varchar(100) DEFAULT NULL,
        `monto_factura` decimal(10,0) DEFAULT NULL,
        `folio` varchar(45) DEFAULT NULL,
        `fecha_factura` datetime DEFAULT NULL,
        `hora_recibido` datetime DEFAULT NULL,
        `fecha_ingreso` datetime DEFAULT NULL,
        `ruta_xml` text,
        `ruta_pdf` text,
        `estatus_insert` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`id_motor`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Motor de Correo almacena los inserts realizados cuando se descarga, CFDI de cuentas de correo'";
        
        if(($ResultMotorCorreo = $this->ConsultaQuery("$DataBaseName", $MotorCorreo))!=1)
        {
            XML::XmlResponse("Error", 0, "<p><b>Error</b> al crear Motor Correo </p><br>Detalles:<br><br>$ResultMotorCorreo");           
            return 0;
        }
        
        return 1;
    }
    
    private function DeleteCfdi($DataBaseName)
    {
        $DeleteCfdi = "DROP DATABASE IF EXISTS $DataBaseName";
        $this->ConsultaQuery("", $DeleteCfdi);
    }
    
    /*
     * Se comprueba la existencia del usuario Root en el sistema
     * return: true Sí existe el usuario Root ó false sino existe.
     */
    function ExistRootUser()
    {               
        $sql="SELECT *FROM Users WHERE Login='root'";
        $Result = $this->ConsultaSelect("CSDOCS_CFDI", $sql);
        if($Result['Estado']!=1)
        {
            return $Result['Estado'];
        }
        
        if(count($Result['ArrayDatos'])>0)
            return 1;
        else
            return 0;
    }
    
    /*
     * Al crear la Tabla Usuario se inserta por default el Usuario Root
     */
    function InsertRootUser()
    {
        $estado=false;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();            
            return $estado;
        }
       
        $sql="INSERT INTO Users (IdUser , UserName, Password) VALUES(1, 'root','root')";
        mysql_select_db("CSDOCS_CFDI",  $conexion);  
        $resultado=mysql_query($sql,  $conexion);
        if(!$resultado)
            {
                
            }                      
        mysql_close($conexion);
            
        return $estado;
    }     
               
    /*
     * Se reciben dos cadenas:
     * 1.- Específica los campos a insertar
     * 2.- Especifica los valores a insertar en esos campos
     */        
    function ConsultaInsert($bd,$query)
    {
       $estado=true;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            
            return $estado;
        }               

        mysql_select_db($bd,  $conexion);  
        $insertar=mysql_query($query,  $conexion);
        if(!$insertar)
            {
                $estado= mysql_error();    
                return $estado;
            }    
        mysql_close($conexion);
            
        return $estado;
    }
    
    function ConsultaInsertReturnId($bd,$query)
    {
       $estado=0;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            
            return $estado;
        }               

        mysql_select_db($bd,  $conexion);  
        $insertar=mysql_query($query,  $conexion);
        if(!$insertar)
            {
                $estado= mysql_error();    
                return $estado;
            }    
        $estado= mysql_insert_id($conexion);
        mysql_close($conexion);
            
        return $estado;
    }
    
    function crear_tabla($bd,$query)
    {
        $estado=true;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            
            return $estado;
        }

        mysql_select_db($bd,  $conexion);  
        $insertar=mysql_query($query,  $conexion);
        if(!$insertar)
            {
                $estado= mysql_error();    
                return $estado;
            }    
        mysql_close($conexion);
            
        return $estado;
    }
    
    /*******************************************************************************
     * Regresa un array asociativo si la consulta tuvo éxito sino devuelve el error
     *                                                              
     *  Resultado = {
     * 
     *          Estado=> True/False ,
     *          ArrayDatos=>  'Resultado de Consulta'
     * 
     *******************************************************************************/
    function ConsultaSelect($bd,$query)
    {
        $estado=true;
        $ResultadoConsulta=array();
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            $error=array("Estado"=>$estado, "ArrayDatos"=>0);
            return $error;
        }

        mysql_selectdb($bd, $conexion);
        $select=mysql_query($query,  $conexion);
        if(!$select)
            {
                $estado= mysql_error(); 
                $error=array("Estado"=>$estado, "ArrayDatos"=>0);
                return $error;
            }
            else
            {
                while(($ResultadoConsulta[] = mysql_fetch_assoc($select)) || array_pop($ResultadoConsulta)); 
            }
        
        
        mysql_close($conexion);
            
        $Resultado=array("Estado"=>$estado, "ArrayDatos"=>$ResultadoConsulta);
        return $Resultado;
    }
    
    public  function QuerySelectArray($bd,$query)
    {

        $estado=true;
        $ResultadoConsulta=array();
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            $error=array("Estado"=>$estado, "ArrayDatos"=>0);
            return $error;
        }

        mysql_selectdb($bd, $conexion);
        $select=mysql_query($query,  $conexion);
        if(!$select)
            {
                $estado= mysql_error(); 
                $error=array("Estado"=>$estado, "ArrayDatos"=>0);
                return $error;
            }
            else
                while($ResultadoConsulta[]=mysql_fetch_array($select));
        
        
        mysql_close($conexion);
            
        $Resultado=array("Estado"=>$estado, "ArrayDatos"=>$ResultadoConsulta);
        return $Resultado;
    }
    
    
    /***************************************************************************
     * Realiza una consulta especifícando la instancia de BD y el query a ejecutar.
     */
    function ConsultaQuery($DataBasaName, $query)
    {
        $estado=true;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();            
            return $estado;
        }

        mysql_selectdb($DataBasaName, $conexion);
        $select=mysql_query($query,  $conexion);
        if(!$select)
            {
                $estado= mysql_error(); 
            }
            
        mysql_close($conexion);            
        return $estado;
    }
    /*******************************************************************************
     * Regresa un array asociativo si la consulta tuvo éxito sino devuelve el error
     *                                                              
     *  Resultado = {
     * 
     *          Estado=> True/False ,
     *          ArrayDatos=>  'Resultado de Consulta'
     * 
     *******************************************************************************/
    
    function getDataBase($IdDataBase)
    {
        $estado=true;
        $conexion=  $this->Conexion();
        if (!$conexion) {
            $estado= mysql_error();
            $error=array("Estado"=>$estado, "ArrayDatos"=>0);
            return $error;
        }

        mysql_select_db("CSDOCS_CFDI",  $conexion);  
        $query="SELECT NombreInstancia FROM instancias WHERE IdInstancia=$IdDataBase";
        $select=mysql_query($query,  $conexion);
        if(!$select)
            {
                $estado= mysql_error(); 
                $error=array("Estado"=>$estado, "ArrayDatos"=>0);
                return $error;
            }    
        
        $ResultadoConsulta=  mysql_fetch_assoc($select);
        mysql_close($conexion);
            
        $Resultado=array("Estado"=>$estado, "ArrayDatos"=>$ResultadoConsulta);
        return $Resultado;
    }
}

$database = new DataBase();
