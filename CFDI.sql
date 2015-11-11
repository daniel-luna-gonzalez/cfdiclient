-- phpMyAdmin SQL Dump
-- version 4.3.0
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 28-04-2015 a las 13:24:29
-- Versión del servidor: 5.5.41-MariaDB
-- Versión de PHP: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `CFDI`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `correo`
--

CREATE TABLE IF NOT EXISTS `correo` (
`id_correo` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `servidor` varchar(45) DEFAULT NULL,
  `smtp` varchar(45) DEFAULT NULL,
  `PuertoSmtp` varchar(4) NOT NULL,
  `puerto` varchar(45) DEFAULT NULL,
  `seguridad` varchar(45) DEFAULT NULL,
  `auth` varchar(10) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `correo` varchar(45) DEFAULT NULL,
  `titulo_mostrar` varchar(45) DEFAULT NULL,
  `host_imap` varchar(150) DEFAULT NULL,
  `estatus` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `correo`
--

INSERT INTO `correo` (`id_correo`, `id_empleado`, `servidor`, `smtp`, `PuertoSmtp`, `puerto`, `seguridad`, `auth`, `password`, `correo`, `titulo_mostrar`, `host_imap`, `estatus`) VALUES
(1, 6, 'gmail', 'smtp.gmail.com', '465', '993', 'ssl', 'true', 'Team12345', 'teamdemocfdi@gmail.com', 'Demo CFDI Team', 'imap.gmail.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura_cliente`
--

CREATE TABLE IF NOT EXISTS `detalle_factura_cliente` (
`id_detalle` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL DEFAULT '0',
  `id_receptor` int(11) NOT NULL DEFAULT '0',
  `id_validacion` int(11) NOT NULL,
  `rfc_cliente` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL,
  `serie` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `folio` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `formaDePago` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `subTotal` double DEFAULT NULL,
  `descuento` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `metodoDePago` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipoDeComprobante` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoCambio` decimal(10,0) DEFAULT NULL,
  `Moneda` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ruta_xml` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ruta_pdf` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_archivo` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `tipo_archivo_pdf` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `Full` text COLLATE utf8_spanish_ci
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_factura_cliente`
--

INSERT INTO `detalle_factura_cliente` (`id_detalle`, `id_emisor`, `id_receptor`, `id_validacion`, `rfc_cliente`, `serie`, `folio`, `fecha`, `formaDePago`, `subTotal`, `descuento`, `total`, `metodoDePago`, `tipoDeComprobante`, `TipoCambio`, `Moneda`, `ruta_xml`, `ruta_pdf`, `tipo_archivo`, `tipo_archivo_pdf`, `Full`) VALUES
(1, 1, 1, 1, 'FERJ850627R18', 'A', '4', '2014-01-23', 'PAGO EN UNA SOLA EXHIBICIÓN', 20000, 0, 23200, 'NO IDENTIFICADO', 'ingreso', '0', 'PESO MXN', '/volume1/web/_root/Factura_Cliente/2014/FERJ850627R18/cfdia0000000004.xml', '/volume1/web/_root/Factura_Cliente/2014/FERJ850627R18/cfdia0000000004.PDF', 'original', 'original', 'A, 4, 2014-01-23T17:45:34, PAGO EN UNA SOLA EXHIBICIÓN, 20000.00, , 23200.00, NO IDENTIFICADO, ingreso, , PESO MXN, BENITO JUAREZ, MEXICO DISTRITO FEDERAL, AIC911114399, AGUILAR INGENIEROS CONSULTORES S.C., MEXICO, GABRIEL MANCERA, DISTRITO FEDERAL, DEL VALLE, BENITO JUAREZ, 1108, 03100, , , , , , , , , FERJ850627R18, FERNANDEZ RUEDA JUAN PABLO, MEXICO, CARR. FEDERAL A CUERNAVACA, DISTRITO FEDERAL, SAN PEDRO MARTIR, TLALPAN, 5671, 27, 14650, 14650, 1.00, NO APLICA, PAGO POR LA REVISION ESTRUCTURAL AL DISEÑO DEL MEZANINE PARA LA BODEGA DE LA TIENDA PUMA EN EL CENTRO COMERCIAL PARQUE LINDAVISTA. 2013-245, 20000.00, 20000.00, QNAiGd77uUkmSJ2OEaUMty6tG0RSev4LJRz3SdTlqSF1TzqPZVNag5bhrWjC44rftfWVl/kE6qNqAu76bWsSfy9Rzb/KGcwnoOA8QZLTYEzlyW7cZSaQzzXC0fCY/RbB81ktpslrrgF3dsI1IQ1QD7Ef9J30hBoLjEdC0oHilfs=, 2014-01-23T17:48:30, AE428416-F6A8-4ED2-BE22-E07F62B87ECD, 00001000000300209963, 1.0, bAVtcwwcpfMv9qqtv648LU1d5W169Wo4vDyd8gRUygKVEq0q2CR68/Vr1Nuq7oVIl2gS5GX/TTbFCjmMcmWOjZvcJge/iyJlyDUZcbSut/sifkiLs3zTMCoY28Wt0pCxujIoR2DAenN7vw8tCMX0srTKr7QjjAlqmb7nxYZgRog=, 16.00, 3200.00, IVA, '),
(2, 2, 2, 2, 'GCS940307TZ3', '', '133', '2014-06-16', 'PAGO EN UNA SOLA EXHIBICION', 435478, 0, 505154.48, 'TRANSFERENCIA', 'ingreso', '1', 'Peso Mexicano', '/volume1/web/_root/Factura_Cliente/2014/GCS940307TZ3/F0000000133.xml', '', 'original', 'original', ', 133, 2014-06-16T11:01:37, PAGO EN UNA SOLA EXHIBICION, 435478.00, , 505154.48, TRANSFERENCIA, ingreso, 1.00, Peso Mexicano, CALZADA SANTA CRUZ 138 B, PORTALES, 03300, BENITO JUAREZ, DISTRITO FEDERAL, DISTRITO FEDERAL, MEXICO, BEC990723MJ2, BECHATEX, S.A. DE C.V., MEXICO, CALZADA SANTA CRUZ, DISTRITO FEDERAL, PORTALES, BENITO JUAREZ, 138, 03300, , , , , , , , , GCS940307TZ3, GCS IMAGEN EMPRESARIAL, S.A. DE C.V., MEXICO, XOLA, DISTRITO FEDERAL, NARVARTE, BENITO JUAREZ, 1663, , 03030, 03030, 1994.000, METROS, M. ARMY, 62.00, 123628.00, 3089.000, METROS, M. MAGIC BLACKDIGO STRECH MR, 50.00, 154450.00, 3148.000, METROS, GABARDINA SATINADA, 50.00, 157400.00, OECvtasgIiHrYEMJXuEytJuKj9hhRSP2KK0SwG9z0J7oOj7jFWdoAAyi47lECYm9vmwkOygcBcc5Op6AbKYY6riTKGr2zkxqB4qKD/Z/dvjdo+H6I2pedvRXvdX0s6d8mdN7HXS2waSTrlDZyteA71sDRvtZ9L3Mt+1ytrF+8Bo=, 2014-06-16T11:01:40, E5E2677F-5881-4B57-8197-1BFEABC6474D, 00001000000202864883, 1.0, kEDV9pGhhspE2BgIjRZkjidP9xx+/8ll2RUd+Ymww1aEAkihDHAlhdzo5QwFjOo+mwPLOHX0AZmYSu+shJz8CkEo9pW3lCusFLepF3BXpqXx+v1mPsW4YpKq5anMoH7ugC5DFF0tGJFAYFyxhPd43kJpCFmOT0aJ1Bk1DssEzSQ=, 16.00, 69676.48, IVA, ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura_proveedor`
--

CREATE TABLE IF NOT EXISTS `detalle_factura_proveedor` (
`id_detalle` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL DEFAULT '0',
  `id_receptor` int(11) NOT NULL DEFAULT '0',
  `id_validacion` int(11) NOT NULL,
  `rfc_cliente` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL,
  `serie` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `folio` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `formaDePago` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `subTotal` double DEFAULT NULL,
  `descuento` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `metodoDePago` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipoDeComprobante` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoCambio` decimal(10,0) DEFAULT NULL,
  `Moneda` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ruta_pdf` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ruta_xml` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_archivo` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `tipo_archivo_pdf` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `Full` text COLLATE utf8_spanish_ci
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_factura_proveedor`
--

INSERT INTO `detalle_factura_proveedor` (`id_detalle`, `id_emisor`, `id_receptor`, `id_validacion`, `rfc_cliente`, `serie`, `folio`, `fecha`, `formaDePago`, `subTotal`, `descuento`, `total`, `metodoDePago`, `tipoDeComprobante`, `TipoCambio`, `Moneda`, `ruta_pdf`, `ruta_xml`, `tipo_archivo`, `tipo_archivo_pdf`, `Full`) VALUES
(1, 1, 1, 1, 'GCS940307TZ3', 'A', '1', '2014-01-23', 'PAGO EN UNA SOLA EXHIBICION', 141000, 0, 163560, 'NO IDENTIFICADO', 'ingreso', '0', 'PESO MXN', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000130.pdf', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000130.xml', 'copia', 'original', 'A, 1, 2014-01-23T10:24:25, PAGO EN UNA SOLA EXHIBICIÓN, 141000.00, , 163560.00, NO IDENTIFICADO, ingreso, , PESO MXN, BENITO JUAREZ, MEXICO DISTRITO FEDERAL, AIC911114399, AGUILAR INGENIEROS CONSULTORES S.C., MEXICO, GABRIEL MANCERA, DISTRITO FEDERAL, DEL VALLE, BENITO JUAREZ, 1108, 03100, , , , , , , , , HGP1307014D5, HABITUM GIA PROMOTORA S.A.P.I DE C.V., MEXICO, PERIFERICO SUR, DISTRITO FEDERAL, JARDINES DE LA MONTAY'),
(2, 1, 1, 3, 'GCS940307TZ3', '', '136', '2014-06-19', 'PAGO EN UNA SOLA EXHIBICION', 434210.9, 0, 503684.65, 'TRANSFERENCIA', 'ingreso', '1', 'Peso Mexicano', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000136.pdf', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000136.xml', 'original', 'original', ', 136, 2014-06-19T12:27:46, PAGO EN UNA SOLA EXHIBICION, 434210.90, , 503684.65, TRANSFERENCIA, ingreso, 1.00, Peso Mexicano, CALZADA SANTA CRUZ 138 B, PORTALES, 03300, BENITO JUAREZ, DISTRITO FEDERAL, DISTRITO FEDERAL, MEXICO, BEC990723MJ2, BECHATEX, S.A. DE C.V., MEXICO, CALZADA SANTA CRUZ, DISTRITO FEDERAL, PORTALES, BENITO JUAREZ, 138, 03300, , , , , , , , , GCS940307TZ3, GCS IMAGEN EMPRESARIAL, S.A. DE C.V., MEXICO, XOLA, DISTRITO FEDERAL, NARVARTE, BENITO JUAREZ, 1663, , 03030, 03030, 2986.700, METROS, M. FANTASY, 48.00, 143361.60, 3016.800, METROS, M. MAGIC MR, 48.50, 146314.80, 2389.000, METROS, M. MYSTERIO, 60.50, 144534.50, A5CeYvBCV0/SNS+vPbqVoXRKiedQdtqgvvdqo2ZZMe6EciqIyJH/3V95UAhJ7Ty8HkgYqIJEkLCNKsXe0Eyu6thQpHKM5qqk1BiYMVAOtWwz7YjCl1fK8+BBvrjf32azowsKNLfoFEhLAikJmEBCxvfPcBclZ8AG5KyTqmg//Qs=, 2014-06-19T12:27:59, BCBCD6B7-E8B1-46D8-92A7-519FB00FE56D, 00001000000202864883, 1.0, bRGGBQpa1zkai6bVLntPqU/HNO4Kwqk0cKwTMA4ordODKMw6JNgyhg5WERfbPZKmB0VCQBjkWLZTl1fFiopDmIHvxylwmZ8cMsFjJR9CWIaF8ERj+ps2F/eKCBTHFYEIALYXafYJRwesCv0XUIyFcQxP5y2v+CDWEoLlFG53KN0=, 16.00, 69473.75, IVA, ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_recibo_nomina`
--

CREATE TABLE IF NOT EXISTS `detalle_recibo_nomina` (
`id_detalle_recibo_nomina` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL,
  `id_receptor` int(11) NOT NULL,
  `id_validacion` int(11) NOT NULL,
  `curp` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `registro_patronal` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `NumEmpleado` int(11) DEFAULT NULL,
  `tipoRegimen` int(11) DEFAULT NULL,
  `NumSegSocial` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FechaPago` date DEFAULT NULL,
  `FechaInicialPago` date DEFAULT NULL,
  `FechaFinalPago` date DEFAULT NULL,
  `NumDiasPagados` decimal(10,0) DEFAULT NULL,
  `departamento` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `clabe` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `banco` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `FechaInicioLaboral` date DEFAULT NULL,
  `antiguedad` int(11) DEFAULT NULL,
  `puesto` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoContrato` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoJornada` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PeriodicidadPago` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL,
  `SalarioBaseCotApor` float DEFAULT NULL,
  `RiesgoPuesto` int(11) DEFAULT NULL,
  `SalarioDiarioIntegrado` float DEFAULT NULL,
  `id_CentroCosto` int(11) DEFAULT '0',
  `xml_ruta` varchar(220) COLLATE utf8_spanish_ci DEFAULT NULL,
  `pdf_ruta` varchar(220) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_archivo` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `tipo_archivo_pdf` varchar(45) COLLATE utf8_spanish_ci DEFAULT 'original',
  `Full` text COLLATE utf8_spanish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `emisor_factura_cliente`
--

CREATE TABLE IF NOT EXISTS `emisor_factura_cliente` (
`idemisor` int(11) NOT NULL,
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
  `expedidoCP` varchar(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `emisor_factura_cliente`
--

INSERT INTO `emisor_factura_cliente` (`idemisor`, `rfc`, `nombre`, `pais`, `calle`, `estado`, `colonia`, `municipio`, `noExterior`, `cp`, `localidad`, `expedidoCalle`, `expedidoNoExterior`, `expedidoColonia`, `expedidoLocalidad`, `expedidoMunicipio`, `expedidoEstado`, `expedidoPais`, `expedidoCP`) VALUES
(1, 'AIC911114399', 'AGUILAR INGENIEROS CONSULTORES S.C.', 'MEXICO', 'GABRIEL MANCERA', 'DISTRITO FEDERAL', 'DEL VALLE', 'BENITO JUAREZ', '1108', '03100', '', '', '0', '', '', '', '', '', '0'),
(2, 'BEC990723MJ2', 'BECHATEX, S.A. DE C.V.', 'MEXICO', 'CALZADA SANTA CRUZ', 'DISTRITO FEDERAL', 'PORTALES', 'BENITO JUAREZ', '138', '03300', '', '', '0', '', '', '', '', '', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `emisor_factura_proveedor`
--

CREATE TABLE IF NOT EXISTS `emisor_factura_proveedor` (
`idemisor` int(11) NOT NULL,
  `rfc` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `emisor_factura_proveedor`
--

INSERT INTO `emisor_factura_proveedor` (`idemisor`, `rfc`, `nombre`) VALUES
(1, 'BEC990723MJ2', 'BECHATEX, S.A. DE C.V.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `emisor_recibo_nomina`
--

CREATE TABLE IF NOT EXISTS `emisor_recibo_nomina` (
`idemisor` int(11) NOT NULL,
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
  `cp_expedido` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `existe`
--

CREATE TABLE IF NOT EXISTS `existe` (
`idexiste` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `NoIntentos` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabla que registra el numero de intentos que se trata de procesar un recibo de nomina o algun otro comprobante';

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `exist_detalle`
--
CREATE TABLE IF NOT EXISTS `exist_detalle` (
`id_detalle_recibo_nomina` int(11)
,`id_emisor` int(11)
,`id_receptor` int(11)
,`FechaPago` date
,`curp` varchar(60)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cliente`
--

CREATE TABLE IF NOT EXISTS `historial_cliente` (
`id_historial` int(11) NOT NULL,
  `id_validacion` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `ruta_xml` varchar(250) NOT NULL,
  `ruta_pdf` varchar(250) DEFAULT NULL,
  `tipo_archivo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_nomina`
--

CREATE TABLE IF NOT EXISTS `historial_nomina` (
`id_historial` int(11) NOT NULL,
  `id_validacion` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `ruta_xml` varchar(250) NOT NULL,
  `ruta_pdf` varchar(250) DEFAULT NULL,
  `tipo_archivo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_proveedor`
--

CREATE TABLE IF NOT EXISTS `historial_proveedor` (
`id_historial` int(11) NOT NULL,
  `id_validacion` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `ruta_xml` varchar(250) NOT NULL,
  `ruta_pdf` varchar(250) DEFAULT NULL,
  `tipo_archivo` varchar(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='tipo_archivo (original o copia)';

--
-- Volcado de datos para la tabla `historial_proveedor`
--

INSERT INTO `historial_proveedor` (`id_historial`, `id_validacion`, `id_detalle`, `id_usuario`, `fecha_hora`, `ruta_xml`, `ruta_pdf`, `tipo_archivo`) VALUES
(1, 2, 1, 1, '2015-04-27 17:18:24', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/copias/F0000000130.xml', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/copias/F0000000130.pdf', 'original');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motor_correo`
--

CREATE TABLE IF NOT EXISTS `motor_correo` (
`id_motor` int(11) NOT NULL,
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
  `estatus_insert` varchar(100) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Motor de Correo almacena los inserts realizados cuando se descarga, CFDI de cuentas de correo';

--
-- Volcado de datos para la tabla `motor_correo`
--

INSERT INTO `motor_correo` (`id_motor`, `id_correo`, `id_emisor`, `id_receptor`, `id_detalle`, `emisor_correo`, `monto_factura`, `folio`, `fecha_factura`, `hora_recibido`, `fecha_ingreso`, `ruta_xml`, `ruta_pdf`, `estatus_insert`) VALUES
(1, '1', 1, 1, 2, 'dluna@cs-docs.com', '503685', '136', '2014-06-19 12:27:46', NULL, '2015-04-28 08:37:46', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000136.xml', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000136.pdf', 'valido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor_factura_cliente`
--

CREATE TABLE IF NOT EXISTS `receptor_factura_cliente` (
`id_receptor` int(11) NOT NULL,
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
  `localidad` varchar(150) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `receptor_factura_cliente`
--

INSERT INTO `receptor_factura_cliente` (`id_receptor`, `rfc`, `nombre`, `pais`, `calle`, `estado`, `colonia`, `municipio`, `noExterior`, `noInterior`, `cp`, `localidad`) VALUES
(1, 'FERJ850627R18', 'FERNANDEZ RUEDA JUAN PABLO', 'MEXICO', 'CARR. FEDERAL A CUERNAVACA', 'DISTRITO FEDERAL', 'SAN PEDRO MARTIR', 'TLALPAN', '5671', NULL, '14650', NULL),
(2, 'GCS940307TZ3', 'GCS IMAGEN EMPRESARIAL, S.A. DE C.V.', 'MEXICO', 'XOLA', 'DISTRITO FEDERAL', 'NARVARTE', 'BENITO JUAREZ', '1663', NULL, '03030', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor_factura_proveedor`
--

CREATE TABLE IF NOT EXISTS `receptor_factura_proveedor` (
`id_receptor` int(11) NOT NULL,
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
  `localidad` varchar(150) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `receptor_factura_proveedor`
--

INSERT INTO `receptor_factura_proveedor` (`id_receptor`, `rfc`, `nombre`, `pais`, `calle`, `estado`, `colonia`, `municipio`, `noExterior`, `noInterior`, `cp`, `localidad`) VALUES
(1, 'GCS940307TZ3', 'GCS IMAGEN EMPRESARIAL, S.A. DE C.V.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receptor_recibo_nomina`
--

CREATE TABLE IF NOT EXISTS `receptor_recibo_nomina` (
`id_receptor` int(11) NOT NULL,
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
  `cp` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pdf`
--

CREATE TABLE IF NOT EXISTS `registro_pdf` (
`id_pdf` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Tabla que contiene los nombres de los PDF pendientes por insertar en la BD';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE IF NOT EXISTS `Usuarios` (
`IdUsuario` int(11) NOT NULL,
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
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_baja` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`IdUsuario`, `IdCorreo`, `nombre_usuario`, `password`, `nombre`, `apellido_materno`, `apellido_paterno`, `curp`, `fecha_nac`, `tipo_usuario`, `estatus`, `fecha_alta`, `fecha_baja`) VALUES
(6, 1, 'team', '123456', 'Demo CFDI Team', '', 'Team', 'TEAM', '1991-07-21', 'usuario', 1, '2015-04-27 18:20:43', NULL),
(7, 0, 'daniel2', '123456', 'Daniel', 'González', 'Luna', 'lugu910721hmccnl07', '1991-07-21', 'usuario', 1, '2015-04-27 18:24:24', NULL),
(8, 0, 'fer', '123456', 'Fernando', '', 'Valera', 'ffefe', '1991-09-24', 'usuario', 1, '2015-04-27 18:25:54', NULL),
(9, 0, 'karen', '123456', 'Karen', 'Gomez', 'Pliego ', 'ipgk', '1991-07-17', 'usuario', 1, '2015-04-27 23:42:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `validacion_cliente`
--

CREATE TABLE IF NOT EXISTS `validacion_cliente` (
`id_validacion` int(11) NOT NULL,
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
  `ruta_acuse` varchar(250) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `validacion_cliente`
--

INSERT INTO `validacion_cliente` (`id_validacion`, `FechaHora_envio`, `FechaHora_respuesta`, `emisor_rfc`, `receptor_rfc`, `total_factura`, `uuid`, `codigo_estatus`, `estado`, `md5`, `web_service`, `ruta_acuse`) VALUES
(1, '2015-04-27 23:54:09', '2015-04-27 23:54:10', 'AIC911114399', 'FERJ850627R18', 23200, 'AE428416-F6A8-4ED2-BE22-E07F62B87ECD', 'S - Comprobante obtenido satisfactoriamente.', 'Vigente', '48a69d2d6123f72b0cff5a48c2bb1b17', 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl', '/volume1/web/_root/Factura_Cliente/2014/FERJ850627R18/cfdia0000000004SAT.xml'),
(2, '2015-04-28 09:42:03', '2015-04-28 09:42:03', 'BEC990723MJ2', 'GCS940307TZ3', 505154.48, 'E5E2677F-5881-4B57-8197-1BFEABC6474D', 'S - Comprobante obtenido satisfactoriamente.', 'Vigente', 'ad86cf90608bf1117a24252026c4dc69', 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl', '/volume1/web/_root/Factura_Cliente/2014/GCS940307TZ3/F0000000133SAT.xml');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `validacion_nomina`
--

CREATE TABLE IF NOT EXISTS `validacion_nomina` (
`id_validacion` int(11) NOT NULL,
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
  `ruta_acuse` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `validacion_proveedor`
--

CREATE TABLE IF NOT EXISTS `validacion_proveedor` (
`id_validacion` int(11) NOT NULL,
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
  `ruta_acuse` varchar(250) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `validacion_proveedor`
--

INSERT INTO `validacion_proveedor` (`id_validacion`, `FechaHora_envio`, `FechaHora_respuesta`, `emisor_rfc`, `receptor_rfc`, `total_factura`, `uuid`, `codigo_estatus`, `estado`, `md5`, `web_service`, `ruta_acuse`) VALUES
(1, '2015-04-27 16:49:51', '2015-04-27 16:49:55', 'BEC990723MJ2', 'GCS940307TZ3', 515813.89, 'B15BEA8D-0F61-4FDA-B96F-0CE42DA9A448', 'S - Comprobante obtenido satisfactoriamente.', 'Vigente', 'e6d2a588add4258693eee2cc235500ba', 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000130SAT.xml'),
(2, '2015-04-27 17:18:20', '2015-04-27 17:18:24', 'AIC911114399', 'HGP1307014D5', 163560, 'C09124CA-1FBC-46C3-A36E-B4F7CE40F23C', 'S - Comprobante obtenido satisfactoriamente.', 'Vigente', 'ee4a2f3154fe3185d44bbf6e1a20f897', 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/copias/F0000000130SAT.xml'),
(3, '2015-04-28 08:37:42', '2015-04-28 08:37:45', 'BEC990723MJ2', 'GCS940307TZ3', 503684.65, 'BCBCD6B7-E8B1-46D8-92A7-519FB00FE56D', 'S - Comprobante obtenido satisfactoriamente.', 'Vigente', 'cf843e3c70d6ac1dfecddce5d190d1bc', 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl', '/volume1/web/_root/Factura_Proveedor/2014/GCS940307TZ3/F0000000136SAT.xml');

-- --------------------------------------------------------

--
-- Estructura para la vista `exist_detalle`
--
DROP TABLE IF EXISTS `exist_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `exist_detalle` AS select `detalle_recibo_nomina`.`id_detalle_recibo_nomina` AS `id_detalle_recibo_nomina`,`detalle_recibo_nomina`.`id_emisor` AS `id_emisor`,`detalle_recibo_nomina`.`id_receptor` AS `id_receptor`,`detalle_recibo_nomina`.`FechaPago` AS `FechaPago`,`detalle_recibo_nomina`.`curp` AS `curp` from `detalle_recibo_nomina`;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `correo`
--
ALTER TABLE `correo`
 ADD PRIMARY KEY (`id_correo`,`id_empleado`);

--
-- Indices de la tabla `detalle_factura_cliente`
--
ALTER TABLE `detalle_factura_cliente`
 ADD PRIMARY KEY (`id_detalle`,`id_validacion`), ADD KEY `fk1_idx` (`id_emisor`), ADD KEY `fk2_idx` (`id_receptor`), ADD FULLTEXT KEY `Full` (`Full`);

--
-- Indices de la tabla `detalle_factura_proveedor`
--
ALTER TABLE `detalle_factura_proveedor`
 ADD PRIMARY KEY (`id_detalle`,`id_validacion`), ADD KEY `fk1_idx` (`id_emisor`), ADD KEY `fk2_idx` (`id_receptor`), ADD FULLTEXT KEY `Full` (`Full`);

--
-- Indices de la tabla `detalle_recibo_nomina`
--
ALTER TABLE `detalle_recibo_nomina`
 ADD PRIMARY KEY (`id_detalle_recibo_nomina`,`curp`,`id_validacion`), ADD KEY `FK1` (`id_emisor`), ADD KEY `FK2_idx` (`id_receptor`), ADD FULLTEXT KEY `Full` (`Full`);

--
-- Indices de la tabla `emisor_factura_cliente`
--
ALTER TABLE `emisor_factura_cliente`
 ADD PRIMARY KEY (`idemisor`,`rfc`);

--
-- Indices de la tabla `emisor_factura_proveedor`
--
ALTER TABLE `emisor_factura_proveedor`
 ADD PRIMARY KEY (`idemisor`,`rfc`);

--
-- Indices de la tabla `emisor_recibo_nomina`
--
ALTER TABLE `emisor_recibo_nomina`
 ADD PRIMARY KEY (`idemisor`,`rfc`);

--
-- Indices de la tabla `existe`
--
ALTER TABLE `existe`
 ADD PRIMARY KEY (`idexiste`,`id_emisor`);

--
-- Indices de la tabla `historial_cliente`
--
ALTER TABLE `historial_cliente`
 ADD PRIMARY KEY (`id_historial`,`id_validacion`);

--
-- Indices de la tabla `historial_nomina`
--
ALTER TABLE `historial_nomina`
 ADD PRIMARY KEY (`id_historial`,`id_validacion`);

--
-- Indices de la tabla `historial_proveedor`
--
ALTER TABLE `historial_proveedor`
 ADD PRIMARY KEY (`id_historial`,`id_validacion`);

--
-- Indices de la tabla `motor_correo`
--
ALTER TABLE `motor_correo`
 ADD PRIMARY KEY (`id_motor`);

--
-- Indices de la tabla `receptor_factura_cliente`
--
ALTER TABLE `receptor_factura_cliente`
 ADD PRIMARY KEY (`id_receptor`,`rfc`);

--
-- Indices de la tabla `receptor_factura_proveedor`
--
ALTER TABLE `receptor_factura_proveedor`
 ADD PRIMARY KEY (`id_receptor`,`rfc`);

--
-- Indices de la tabla `receptor_recibo_nomina`
--
ALTER TABLE `receptor_recibo_nomina`
 ADD PRIMARY KEY (`id_receptor`,`curp`);

--
-- Indices de la tabla `registro_pdf`
--
ALTER TABLE `registro_pdf`
 ADD PRIMARY KEY (`id_pdf`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
 ADD PRIMARY KEY (`IdUsuario`,`nombre_usuario`);

--
-- Indices de la tabla `validacion_cliente`
--
ALTER TABLE `validacion_cliente`
 ADD PRIMARY KEY (`id_validacion`);

--
-- Indices de la tabla `validacion_nomina`
--
ALTER TABLE `validacion_nomina`
 ADD PRIMARY KEY (`id_validacion`);

--
-- Indices de la tabla `validacion_proveedor`
--
ALTER TABLE `validacion_proveedor`
 ADD PRIMARY KEY (`id_validacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `correo`
--
ALTER TABLE `correo`
MODIFY `id_correo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `detalle_factura_cliente`
--
ALTER TABLE `detalle_factura_cliente`
MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `detalle_factura_proveedor`
--
ALTER TABLE `detalle_factura_proveedor`
MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `detalle_recibo_nomina`
--
ALTER TABLE `detalle_recibo_nomina`
MODIFY `id_detalle_recibo_nomina` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `emisor_factura_cliente`
--
ALTER TABLE `emisor_factura_cliente`
MODIFY `idemisor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `emisor_factura_proveedor`
--
ALTER TABLE `emisor_factura_proveedor`
MODIFY `idemisor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `emisor_recibo_nomina`
--
ALTER TABLE `emisor_recibo_nomina`
MODIFY `idemisor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `existe`
--
ALTER TABLE `existe`
MODIFY `idexiste` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `historial_cliente`
--
ALTER TABLE `historial_cliente`
MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `historial_nomina`
--
ALTER TABLE `historial_nomina`
MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `historial_proveedor`
--
ALTER TABLE `historial_proveedor`
MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `motor_correo`
--
ALTER TABLE `motor_correo`
MODIFY `id_motor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `receptor_factura_cliente`
--
ALTER TABLE `receptor_factura_cliente`
MODIFY `id_receptor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `receptor_factura_proveedor`
--
ALTER TABLE `receptor_factura_proveedor`
MODIFY `id_receptor` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `receptor_recibo_nomina`
--
ALTER TABLE `receptor_recibo_nomina`
MODIFY `id_receptor` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `registro_pdf`
--
ALTER TABLE `registro_pdf`
MODIFY `id_pdf` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `validacion_cliente`
--
ALTER TABLE `validacion_cliente`
MODIFY `id_validacion` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `validacion_nomina`
--
ALTER TABLE `validacion_nomina`
MODIFY `id_validacion` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `validacion_proveedor`
--
ALTER TABLE `validacion_proveedor`
MODIFY `id_validacion` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
