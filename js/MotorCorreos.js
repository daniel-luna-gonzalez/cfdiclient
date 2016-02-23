/*
 * Muestra el resultado de las búsquedas realizadas en cada uno de los correos registrados
 * para extraer sus comprobantes localizados en la bandeja de entrada
 */
/*
 * @parametro: String -> exito, invalido, repetido, desconocido
 * depende del parametro es el listado que devuelve
 */

var TableMCValidosdT, TableMCValidosDT;
var TableMCInvalidosdT, TableMCInvalidosDT;
var TableMCRepetidosdT, TableMCRepetidosDT;

var TableResultExtractiondT, TableResultExtractionDT;

var ventana_resultado={draggable:false,modal:true,maxHeigth:600,heigth:400, width:300,closeOnEscape:false,resizable:false, title:'Mensaje inesperado...', buttons: { "Aceptar": function (){ $(this).dialog("close");  }   }};

  var AnchoPantalla = $(window).width();
  var AnchoDialogMotor = AnchoPantalla * .80;
  var AltoPantalla = $(window).height();
  var AltoDialogMotor = AltoPantalla * 0.80;

$(document).ready(function()
{
   $('#icono_motor_correo') .click(function()
   {
       $('#div_listado_motor_validos').empty();
       $('#div_listado_motor_repetidos').empty();
       $('#div_listado_motor_invalidos').empty();
       mostrar_dialog_motor_correos();
   });
   
   /* Acciones al pulsar sobre las pestañas del dialog para el llenado de las tablas cuando este vacio el div */
   $('#li_div_listado_motor_validos').click(function()
   {
       var emptyTest = $('#div_listado_motor_validos').is(':empty');
       if(emptyTest){get_list_motor_correos('valido');}
   });
   $('#li_div_listado_motor_repetidos').click(function()
   {
       var emptyTest = $('#div_listado_motor_repetidos').is(':empty');
       if(emptyTest){get_list_motor_correos('repetido');}
   });
   $('#li_div_listado_motor_invalidos').click(function()
   {
       var emptyTest = $('#div_listado_motor_invalidos').is(':empty');
       if(emptyTest){get_list_motor_correos('invalido');}
   });
});

function mostrar_dialog_motor_correos()
{
    get_list_motor_correos('valido');
    $.fn.tabbedDialog = function () {
            this.tabs({active: 0});
            this.dialog({height: AltoDialogMotor,width:AnchoDialogMotor, modal: true,closeOnEscape:false,buttons: { "Cerrar": function() { $(this).dialog("close"); } } });
            this.find('.ui-tab-dialog-close').append($('a.ui-dialog-titlebar-close'));
            this.find('.ui-tab-dialog-close').css({'position':'absolute','right':'0', 'top':'23px'});
            this.find('.ui-tab-dialog-close > a').css({'float':'none','padding':'0'});
            var tabul = this.find('ul:first');
            this.parent().addClass('ui-tabs').prepend(tabul).draggable('option','handle',tabul); 
            this.siblings('.ui-dialog-titlebar').remove();
//            tabul.addClass('ui-dialog-titlebar');
        };
        $('#div_motor_correos').tabbedDialog();

}

function get_list_motor_correos(opcion_lista_motor)
{
    $( "#admin_div_loading" ).append('<div id="mensaje_div_loading"></div>');
    $('#mensaje_div_loading').append('<p>Comprobando Buzón por favor espere, este proceso podría durar varios minutos</p>');
    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */            

   $.ajax({
        async:true, 
        cache:false,
        dataType:"html", 
        type: 'POST',   
        url: "php/mail.php",
        data: "opcion=get_list_motor&opcion_lista_motor="+opcion_lista_motor, 
        success:  function(xml)
        {            
            $('#mensaje_div_loading').remove();
            $('#admin_div_loading').dialog('close');    
            $('#ventana_resultado').empty();            

            if($.parseXML( xml )===null){ Error(xml); return 0;}else xml=$.parseXML( xml );
        
            $('#div_listado_motor_validos').empty();
            crear_tabla_listado_motor_correo(opcion_lista_motor,xml);
            
            $(xml).find("Error").each(function()
            {
                var mensaje=$(this).find("Mensaje").text();
                Error(mensaje);
                $('#UsersPlaceWaiting').remove();
            });                 

        },
        beforeSend:function(){},
        error: function(jqXHR, textStatus, errorThrown){Error(textStatus +"<br>"+ errorThrown);}
        });       
   
}
//var TableMCValidosdT, TableMCValidosDT;
//var TableMCInvalidosdT, TableMCInvalidosDT;
//var TableMCRepetidosdT, TableMCRepetidosDT;
function crear_tabla_listado_motor_correo(tipo_lista,xml)
{
    var id_tabla='tabla_listado_motor_'+tipo_lista;
    var id_div='';
    if(tipo_lista==='valido'){id_div="#div_listado_motor_validos"; }
    if(tipo_lista==='repetido'){id_div="#div_listado_motor_repetidos";}
    if(tipo_lista==='invalido'){id_div="#div_listado_motor_invalidos";}
    
    $(id_div).empty();
    
    $(id_div).append('<table id = "'+id_tabla+'" class = "display hover"><thead><tr><th>Emisor</th><th>Receptor</th><th>Correo</th><th>Monto Factura</th><th>Folio</th><th>Fecha</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody></tbody></table>');
    
    var TableTd, TableTD;
    
    if(tipo_lista==='valido')
    {
        TableMCValidosdT = $("#"+id_tabla).dataTable(OptionsDataTable);
        TableMCValidosDT = new $.fn.dataTable.Api("#"+id_tabla);
        TableTd = TableMCValidosdT;
        TableTD = TableMCValidosDT;
    }
    if(tipo_lista==='invalido')
    {
        TableMCInvalidosdT = $("#"+id_tabla).dataTable(OptionsDataTable);
        TableMCInvalidosDT = new $.fn.dataTable.Api("#"+id_tabla);
        TableTd = TableMCInvalidosdT;
        TableTD = TableMCInvalidosDT;
    }
    if(tipo_lista==='repetido')
    {
        TableMCRepetidosdT = $("#"+id_tabla).dataTable(OptionsDataTable);        
        TableTD = TableMCRepetidosDT = new $.fn.dataTable.Api("#"+id_tabla);
        TableTd = TableMCRepetidosdT;
        TableTD = TableMCRepetidosDT;
    }
    
    $(xml).find("Listado").each(function()
    {
        var id_motor = $(this).find('id_motor').text();          
         var emisor = $(this).find('emisor').text();          
         var receptor = $(this).find('receptor').text();          
         var emisor_correo = $(this).find('emisor_correo').text();          
         var monto_factura = $(this).find('monto_factura').text();          
         var folio = $(this).find('folio').text();          
         var fecha_factura = $(this).find('fecha_factura').text();          
         var estatus = $(this).find('estatus').text();          
         var ruta_xml = $(this).find('ruta_xml').text();          
         var ruta_pdf = $(this).find('ruta_pdf').text();          
         
        var acciones;
        acciones = '<img src = "img/delete_icon.png" title = "Eliminar Registro" style = "cursor:pointer" width = "30px" height="30px" onclick = "eliminar_registro_motor(\''+id_motor+'\',\''+tipo_lista+'\')">\n\
                    <img src = "img/folder_xml.png" title = "Vista Previa del xml" style = "cursor:pointer" width = "30px" height="30px" onclick = "_ShowXmlPreview(\'proveedor\',\''+ruta_xml+'\')">';
        if(ruta_pdf!='S/PDF')
            acciones+='<img src = "img/pdf_icon.png" title = "Vista Previa PDF" style = "cursor:pointer" width = "30px" height="30px" onclick = "vista_revia_pdf_historico(\''+ruta_pdf+'\')">';
        
        var Data = [emisor,receptor,emisor_correo,monto_factura,folio,fecha_factura,estatus,   acciones
         ];
         
         var ai = TableTD.row.add(Data).draw();
         var n = TableTd.fnSettings().aoData[ ai[0] ].nTr;
         n.setAttribute('id',id_motor);
    });       
}

_ShowXmlPreview = function(content, Path)
    {
        var cfdi = new CFDI(content);
        var xml = cfdi.GetXmlStructureByPath(content, Path);
        if($.isXMLDoc(xml))
        {
            var preview = new Preview();
            preview.CfdiPreview(content, xml);
            $('#div_cfdi_copia_historico').dialog({minWidth:500,height: visor_Height,width:visor_Width, modal: true,closeOnEscape:false,title:'Visor CFDI',buttons: {"Descargar Histórico": function (){ self.DownloadHistorical(content); }, "Cerrar": function() { $(this).dialog("destroy"); } } });
        }                
    };

/*
 * Borra una fila del listado de resultado de la extracción de correos
 */


function eliminar_registro_motor(id_motor,tipo_lista)
{   
        
//    var id_tabla='tabla_listado_motor_'+tipo_lista;   

    
    $( "#admin_div_loading" ).append('<div id="mensaje_div_loading"></div>');
    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */ 
    
    
    ajax=objetoAjax();
    ajax.open("POST", 'php/mail.php',true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8;");
    ajax.send("opcion=eliminar_registro_motor&id_registro_motor="+id_motor);
    ajax.onreadystatechange=function() 
    {
        if (ajax.readyState===4 && ajax.status===200) 
       {
           $('#mensaje_div_loading').remove();
            $('#admin_div_loading').dialog('close');    
            $('#ventana_resultado').empty();
            if(ajax.responseXML==null)
            {$('#ventana_resultado').append(ajax.responseText);$('#ventana_resultado').dialog(ventana_resultado);return;}
            var xml=ajax.responseXML;
            var root=xml.getElementsByTagName("EliminarRegistro");  
//            $('#ventana_resultado').dialog();
            for (i=0;i<root.length;i++) 
            { 
                var estado =root[i].getElementsByTagName("estado")[0].childNodes[0].nodeValue;
                var mensaje=root[i].getElementsByTagName("mensaje")[0].childNodes[0].nodeValue;
                if(estado==1)
                {
                    efecto_notificacion(mensaje,"Registro Eliminado");
                    
                    var div_listado="div_listado_motor_"+tipo_lista;
                    $(div_listado).empty();
                    get_list_motor_correos(tipo_lista);
//                    $('#ventana_resultado').dialog('option', 'title', 'Registro eliminado con éxito');
//                    $('#ventana_resultado').append('<p><center><img src="img/success.png" title="carga carga pdf" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');

                }   
                else
                {             
                    efecto_notificacion(mensaje,"Error al eliminar el registro");
//                    $('#ventana_resultado').dialog('option', 'title', 'Error al elminar registro');  
//                    $('#ventana_resultado').append('<p><center><img src="img/Alert.png" title="error" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');
                }                                                
            }
            
       }
   }
}

function actualizar_tabla(opcion_lista_motor)
{
    
}


/*
 * 
 * @param {type} id_correo
 * @param {type} servidor
 * @param {type} host_imap
 * @param {type} puerto
 * @param {type} correo
 * @param {type} password
 * @returns {undefined}
 * 
 * Realiza la descarga de correos y retorna un XML con el detalle de descargas e inserciones realizadas
 */
function motor_correo_descarga(id_correo,servidor,host_imap,puerto,correo,password)
{
    $( "#admin_div_loading" ).dialog({modal:true,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */
    var id_usuario_sistema=$('#id_usr').val();   /* Log */
    var nombre_usuario=$('#form_user').val();
    var parametros="opcion=motor_descarga_correo&id_usuario="+id_usuario_sistema+"&nombre_usuario="+nombre_usuario+"&id_correo="+id_correo+"&servidor="+servidor+"&host="+host_imap+'&puerto='+puerto+'&correo='+correo+'&password='+password;
       
    $.ajax({
        async:true, 
        cache:false,
        dataType:"html", 
        type: 'POST',   
        url: "php/mail.php",
        data: parametros, 
        success:  function(xml)
        {            
            $('#admin_div_loading').dialog('close');    
            $('#ventana_resultado').empty();

            if($.parseXML( xml )===null){ Error(xml); return 0;}else xml=$.parseXML( xml );
            
            crear_tabla_resultado_descarga(xml);            
            
            $(xml).find("Error").each(function()
            {
                var mensaje=$(this).find("Mensaje").text();
                Error(mensaje);
                $('#UsersPlaceWaiting').remove();
            });                 

        },
        beforeSend:function(){},
        error: function(jqXHR, textStatus, errorThrown){$('#admin_div_loading').dialog('close');    Error(textStatus +"<br>"+ errorThrown);}
        });       
}

function crear_tabla_resultado_descarga(xml)
{
    $('#div_resultado_descarga_correo').empty();    
    $('#div_resultado_descarga_correo').append('<div class = "titulos_ventanas">Resultado</div><br><br>');
    $('#div_resultado_descarga_correo').append('<table id = "TableExtractionResult" class = "display hover"><thead><tr><th>Emisor</th><th>Receptor</th><th>Correo</th><th>Monto Factura</th><th>Folio</th><th>Fecha Factura</th><th>Estatus</th><th>Acciones</th></tr></thead></table>');
    
    TableResultExtractiondT = $("#TableExtractionResult").dataTable(OptionsDataTable);
    TableResultExtractionDT = new $.fn.dataTable.Api("#TableExtractionResult");
    
    $(xml).find("DescargaCorreo").each(function()
    {
        var id_motor = $(this).find('id_motor').text();         
        var emisor = $(this).find('nombre_emisor').text();           
        var receptor = $(this).find('nombre_receptor').text();
        var emisor_correo = $(this).find('emisor_correo').text();
        var monto_factura = $(this).find('total_factura').text();
        var folio = $(this).find('folio_factura').text();              
        var fecha_factura = $(this).find('fecha_factura').text();         
        var estatus = $(this).find('estatus').text();
        var ruta_xml = $(this).find('ruta_xml').text();
        var ruta_pdf = $(this).find('ruta_pdf').text();
                   
        var acciones;
        acciones = '\n\
                    <img src = "img/folder_xml.png" title = "Vista Previa del xml" style = "cursor:pointer" width = "30px" height="30px" onclick = "obtener_copia_cfdi(\'proveedor\',\''+ruta_xml+'\')">';
        if(ruta_pdf!='S/PDF')
            acciones+='<img src = "img/pdf_icon.png" title = "Vista Previa PDF" style = "cursor:pointer" width = "30px" height="30px" onclick = "vista_revia_pdf_historico(\''+ruta_pdf+'\')">';               
        
         var Data = [emisor,receptor,emisor_correo,monto_factura,folio,fecha_factura,estatus,acciones];
         
         var ai = TableResultExtractionDT.row.add(Data).draw();
         var n = TableResultExtractiondT.fnSettings().aoData[ ai[0] ].nTr;
         n.setAttribute('id',id_motor);
    });
    
    $('#div_resultado_descarga_correo').dialog({height: AltoDialogMotor,width:AnchoDialogMotor, title:"Resultado de Escaneo de correo electrónico", modal: true,closeOnEscape:false,buttons: { "Cerrar": function() { $(this).dialog("close"); } } });         
       
}



function registro_correo()
{
    $( "#consola_administracion" ).dialog( "option", "buttons", []);
    $('#admin_ventana_trabajo').empty();
    $('#admin_ventana_trabajo').append('<div class="titulos_ventanas">Registrar una nueva cuenta</div>');
    $('#admin_ventana_trabajo').append('<div id="alta_div_imap_comun">\n\
        <p>Registre una cuenta de correo electrónico para monitorear los CFDI\'s que recibe a través de ella.</p>\n\
        <p>Usuario: <input type="text" placeholder="cuenta@" id="alta_imap_usuario"> @ \n\
        <select  id="alta_imap_select_tipo_correo"> <option value="hotmail">Hotmail</option>\n\
        <option value="yahoo">yahoo.com</option>\n\
        <option value="gmail">gmail.com</option>\n\
        <option value="live">live.com</option>\n\
        <option value="otro">Empresa...</option></select></p>  \n\
        <p>Contraseña: <input type="password" id="alta_imap_pass"></p>\n\
    </div>');
    
    $('#admin_ventana_trabajo').append('<div id="alta_div_correo_empresa" style="display:none">\n\
        <p>Introduzca la información de la cuenta de correo IMAP que desee registrar. Sino los conoce consulte \n\
        al administrador del servidor de cuentas de correo.</p>\n\
        <input type="button" id="alta_boton_imap_correos_anteriores" value="Mostrar Opciones Anteriores">\n\
        <p>Host: <input type="text" id="alta_imap_host"></p>\n\
        <p>Puerto: <input type="text" id="alta_imap_puerto" value="993"> 993 por defecto</p>\n\
        <p>Usuario: <input type="text" id="alta_imap_usuario_empresa"></p>\n\
        <p>Contraseña: <input type="password" id="alta_imap_pass_empresa"></p>\n\
    </div>');
    
    $('#alta_boton_imap_correos_anteriores').button();
    
    $('#alta_boton_imap_correos_anteriores').click(function()
    {
        $('#alta_div_imap_comun').show();
        $('#alta_div_correo_empresa').hide();
        $("#alta_imap_select_tipo_correo option[value=hotmail]").attr("selected",true);
    });
    
    $('#alta_imap_select_tipo_correo').change(function(){
        $('.alert_alta_imap_host').remove();
        var select_correo=$('#alta_imap_select_tipo_correo').val();
        if(select_correo=='otro')
        {
            $('#alta_div_imap_comun').hide();
            $('#alta_div_correo_empresa').show();
        }
    });
    
    $( "#consola_administracion" ).dialog( "option", "buttons", [
    {
        text: "Aceptar",
        click: function() { comprobar_registro(); }
    }    
    ]);
    
    /* Cambio de color en formularios al tener el foco */
    $("input").focus(function(){

                $(this).addClass("seleccionado");
        });
        $("input").blur(function(){
                $(this).removeClass("seleccionado");  

        });
    
}

function comprobar_registro()
{
    var id_usuario_sistema=$('#id_usr').val();   /* Log */
    
    var host;
    var usuario;
    var pass;
    var puerto;
    $('.alert_alta_imap_host').remove();
    var parametros='';
    /* Selección cuando es Gmail, Hotmail, Yahoo, Live */
    if($('#alta_imap_select_tipo_correo').val()!='otro')
    {
        host =$('#alta_imap_select_tipo_correo').val();
        usuario=$('#alta_imap_usuario').val();
        pass=$('#alta_imap_pass').val();
        $('#alta_imap_usuario').val('');
        $('#alta_imap_pass').val('');
        var alerta=0;
        if(usuario.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_select_tipo_correo');}
        if(pass.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_pass');}
        if(alerta==1){return;}
        $('.alert_alta_imap_host').remove();
        parametros="opcion=prueba_imap&id_usuario="+id_usuario_sistema+"&host="+host+'&usuario='+usuario+'&password='+pass;
    }
    else/* Selección cuando es Imap Empresarial */
    {
        host =$('#alta_imap_host').val();
        usuario=$('#alta_imap_usuario_empresa').val();
        pass=$('#alta_imap_pass_empresa').val();
        puerto=$('#alta_imap_puerto').val();
        
        if(host.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_host');}
        if(puerto.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_puerto');}
        if(usuario.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_usuario_empresa');}
        if(pass.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#alta_imap_puerto');}
        if(alerta==1){return;}
        parametros="opcion=prueba_imap&id_usuario="+id_usuario_sistema+"&host=otro&server="+host+'&puerto='+puerto+'&usuario='+usuario+'&password='+pass;
    }
    prueba_imap(parametros);
}


/* Se comprueba la conexión IMAP 
 * Se utiliza en las modificaciones y en las Altas*/
function prueba_imap(parametros)
{
    
    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */   
   
   $.ajax({
    async:false, 
    cache:false,
//    processData: false,
//    contentType: false,
    dataType:"html", 
    type: 'POST',   
    url: "php/mail.php",
    data:parametros, 
    success:  function(xml)
    {           
        $('#admin_div_loading').dialog('close');
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );                
        $(xml).find('Resultado').each(function()
        {
            var estado = $(this).find('estado').text();
            var mensaje = $(this).find('mensaje').text();
            if(estado==0)
            {              
                Error(mensaje);
            }
            if(estado==1)
            {                   
                Exito(mensaje);

                $('#alta_imap_usuario').val('');
                $('#alta_imap_pass').val('');                    
                $('#alta_imap_host').val('');
                $('#alta_imap_puerto').val('');
                $('#alta_imap_usuario_empresa').val('');
                $('#alta_imap_pass_empresa').val('');
            } 
        });
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });  
}

function get_lista_correos()
{
    $( "#consola_administracion" ).dialog( "option", "buttons", []);
    $('#admin_ventana_trabajo').empty();
    if($('#tabla_lista_correos').length===0)
    

    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */
    
    ajax=objetoAjax();
    ajax.open("POST", 'php/mail.php',true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8;");
    ajax.send("opcion=get_lista_correo");
    ajax.onreadystatechange=function() 
    {
        if (ajax.readyState===4 && ajax.status===200) 
       {
            $('#admin_div_loading').dialog('close');           
            if(ajax.responseXML==null)
            {$('#ventana_resultado').empty();$('#ventana_resultado').append(ajax.responseText); $('#ventana_resultado').dialog({modal:true,maxHeigth:550,heigth:500, width:300,closeOnEscape:false, title:'Mensaje inesperado...', buttons: { "Aceptar": function (){ $(this).dialog("close");  }   }});return;}
//            llenar_tabla_correos(ajax.responseXML);
            crear_tabla_lista_correos(ajax.responseXML);
       }
   }
}

function llenar_tabla_correos(xml)
{
    var root=xml.getElementsByTagName("Correo");  
    if(root.length===0)
    {
        var td1=document.createElement('td');
        var tr=document.createElement('tr');
        
        td1.setAttribute("colspan","3");
        td1.innerHTML="No existen registros....";
        
        tr.appendChild(td1);
                   
        $('#tabla_lista_correos').append(tr);
    }
    var tbBody = document.createElement("tbody");
    for (i=0;i<root.length;i++) 
    { 
        var id_correo =root[i].getElementsByTagName("id_correo")[0].childNodes[0].nodeValue;
        var servidor =root[i].getElementsByTagName("servidor")[0].childNodes[0].nodeValue;
        var host_imap=root[i].getElementsByTagName("host_imap")[0].childNodes[0].nodeValue;
        var puerto=root[i].getElementsByTagName("puerto")[0].childNodes[0].nodeValue;
        var correo=root[i].getElementsByTagName("correo")[0].childNodes[0].nodeValue;
        var password=root[i].getElementsByTagName("password")[0].childNodes[0].nodeValue;
        
        var td1=document.createElement('td');
        var td2=document.createElement('td');
        var td3=document.createElement('td');
        var td4=document.createElement('td');
        var tr=document.createElement('tr');
        
        tr.setAttribute("id","fila_tabla_correo_"+id_correo);
        
        var img=document.createElement('img');
        img.setAttribute("src","img/delete_icon.png");
        img.setAttribute("width","30px");
        img.setAttribute("heigth","30px");
        img.style.cursor="pointer";
        var eliminar_correo='confirmacio_eliminar_correo_imap(\''+id_correo+'\',\''+correo+'\')';
        img.setAttribute("onclick",eliminar_correo);
        img.setAttribute("title","Eliminar "+correo);
        
        var edit=document.createElement('img');
        edit.setAttribute('src', 'img/edit_icon.png');
        edit.setAttribute('width', '30px');
        edit.setAttribute('heigth', '30px');
        var funcion='ventana_modificar_correo_imap(\''+id_correo+'\',\''+ servidor+'\',\''+ host_imap+'\',\''+puerto+'\',\''+ correo+'\',\''+ password+'\')';
        edit.setAttribute('onclick', funcion); 
        edit.style.cursor="pointer";
        edit.setAttribute('title', 'Editar '+correo);
        
        var descarga=document.createElement('img');
        descarga.setAttribute('src', 'img/descarga_mail.png');
        descarga.setAttribute('width', '30px');
        descarga.setAttribute('heigth', '30px');
        var funcion_descarga='motor_correo_descarga(\''+id_correo+'\',\''+ servidor+'\',\''+ host_imap+'\',\''+puerto+'\',\''+ correo+'\',\''+ password+'\')';
        descarga.setAttribute('onclick', funcion_descarga); 
        descarga.style.cursor="pointer";
        descarga.setAttribute('title', 'Editar '+correo);
        
        if(servidor==="yahoo")
        {correo=correo+'@yahoo.com';}
        if(servidor==='gmail'){correo=correo+'@gmail.com';}
        
        td1.innerHTML=correo;
        td2.appendChild(img);
        td3.appendChild(edit);
        td4.appendChild(descarga);
        
        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
                   
        tbBody.appendChild(tr);
        $('#tabla_lista_correos').append(tbBody);
    }
    
    
    $('#tabla_lista_correos').dataTable( 
        {
//            "bJQueryUI": true,
		"sPaginationType": "full_numbers",
//                "scrollY":        "110px",
        "scrollCollapse": true,
                "oLanguage": {
			"sLengthMenu": "Mostrar _MENU_ registros por página",
			"sZeroRecords": "No se encontraron resultados",
			"sInfo": "Mostrados _START_ de _END_ de _TOTAL_ registro(s)",
			"sInfoEmpty": "Mostrados 0 de 0 of 0 registros",
			"sInfoFiltered": "(Filtrando desde _MAX_ total registros)"
                            }
        } );
}



function crear_tabla_lista_correos(xml)
{
 
    var tabla =document.createElement("table");
//      tabla.setAttribute("align", "center");
       tabla.setAttribute("id", "tabla_lista_correos");
//      tabla.setAttribute("border", "1");
      tabla.setAttribute("class", "display");
      var tbBody = document.createElement("tbody");
      var tr = document.createElement("tr");    
      var th1=document.createElement("th");
      var th2=document.createElement("th");
      var th3=document.createElement("th");
      var th4=document.createElement("th");

      var thead=document.createElement("thead");
       th1.innerHTML="Correo";
       tr.appendChild(th1);
       th2.innerHTML="Eliminar";
       tr.appendChild(th2);
       th3.innerHTML="Editar";
       tr.appendChild(th3);
       th4.innerHTML="Iniciar descarga";
       tr.appendChild(th4);    

       
       thead.appendChild(tr);       
       tabla.appendChild(thead);
    
    var root=xml.getElementsByTagName("Correo");  
//    if(root.length===0)
//    {
//        var td1=document.createElement('td');
//        var tr=document.createElement('tr');
//        
//        td1.setAttribute("colspan","3");
//        td1.innerHTML="No existen registros....";
//        
//        tr.appendChild(td1);
//                   
//        $('#tabla_lista_correos').append(tr);
//    }
    
    
    var tbBody = document.createElement("tbody");
    for (i=0;i<root.length;i++) 
    { 
        var id_correo =root[i].getElementsByTagName("id_correo")[0].childNodes[0].nodeValue;
        var servidor =root[i].getElementsByTagName("servidor")[0].childNodes[0].nodeValue;
        var host_imap=root[i].getElementsByTagName("host_imap")[0].childNodes[0].nodeValue;
        var puerto=root[i].getElementsByTagName("puerto")[0].childNodes[0].nodeValue;
        var correo=root[i].getElementsByTagName("correo")[0].childNodes[0].nodeValue;
        var password=root[i].getElementsByTagName("password")[0].childNodes[0].nodeValue;
        
        var td1=document.createElement('td');
        var td2=document.createElement('td');
        var td3=document.createElement('td');
        var td4=document.createElement('td');
        var tr1=document.createElement('tr');
        
        tr1.setAttribute("id","fila_tabla_correo_"+id_correo);
        
        var img=document.createElement('img');
        img.setAttribute("src","img/delete_icon.png");
        img.setAttribute("width","30px");
        img.setAttribute("heigth","30px");
        img.style.cursor="pointer";
        var eliminar_correo='confirmacio_eliminar_correo_imap(\''+id_correo+'\',\''+correo+'\')';
        img.setAttribute("onclick",eliminar_correo);
        img.setAttribute("title","Eliminar "+correo);
        
        var edit=document.createElement('img');
        edit.setAttribute('src', 'img/edit_icon.png');
        edit.setAttribute('width', '30px');
        edit.setAttribute('heigth', '30px');
        var funcion='ventana_modificar_correo_imap(\''+id_correo+'\',\''+ servidor+'\',\''+ host_imap+'\',\''+puerto+'\',\''+ correo+'\',\''+ password+'\')';
        edit.setAttribute('onclick', funcion); 
        edit.style.cursor="pointer";
        edit.setAttribute('title', 'Editar '+correo);
        
        var descarga=document.createElement('img');
        descarga.setAttribute('src', 'img/descarga_mail.png');
        descarga.setAttribute('width', '30px');
        descarga.setAttribute('heigth', '30px');
        var funcion_descarga='motor_correo_descarga(\''+id_correo+'\',\''+ servidor+'\',\''+ host_imap+'\',\''+puerto+'\',\''+ correo+'\',\''+ password+'\')';
        descarga.setAttribute('onclick', funcion_descarga); 
        descarga.style.cursor="pointer";
        descarga.setAttribute('title', 'Iniciar descarga de '+correo);
        
        if(servidor==="yahoo")
        {correo=correo+'@yahoo.com';}
        if(servidor==='gmail'){correo=correo+'@gmail.com';}
        
        td1.innerHTML=correo;
        td2.appendChild(img);
        td3.appendChild(edit);
        td4.appendChild(descarga);
        
        tr1.appendChild(td1);
        tr1.appendChild(td2);
        tr1.appendChild(td3);
        tr1.appendChild(td4);
                   
        tbBody.appendChild(tr1);
        tabla.appendChild(tbBody);
    }
            
    $('#admin_ventana_trabajo').append(tabla);    
    
    $(tabla).dataTable( 
        {
//            "bJQueryUI": true,
		"sPaginationType": "full_numbers",
//                "scrollY":        "110px",
        "scrollCollapse": true,
                "oLanguage": {
			"sLengthMenu": "Mostrar _MENU_ registros por página",
			"sZeroRecords": "No se encontraron resultados",
			"sInfo": "Mostrados _START_ de _END_ de _TOTAL_ registro(s)",
			"sInfoEmpty": "Mostrados 0 de 0 of 0 registros",
			"sInfoFiltered": "(Filtrando desde _MAX_ total registros)"
                            }
        } );
    
    
}

/* Modificar Correo IMAP*/
function ventana_modificar_correo_imap(id_correo, servidor, host_imap, puerto, correo, password)
{        
    $('#div_modificar_correo_imap').remove();
    $('#admin_ventana_trabajo').append('<div id="div_modificar_correo_imap"></div>');
    $('#div_modificar_correo_imap').append('<p>Servidor: <select id="modificar_imap_select_tipo_correo"> <option value="hotmail">Hotmail</option>\n\
        <option value="yahoo">Yahoo</option>\n\
        <option value="gmail">Gmail</option>\n\
        <option value="live">Live</option>\n\
        <option value="otro">Empresa...</option></select></p>');
    $('#div_modificar_correo_imap').append('<p>IMAP: <input type="text" value="'+host_imap+'" id="modificar_imap_host"></p>');

     $('#div_modificar_correo_imap').append('<p>Puerto: <input type="text" value="'+puerto+'" id="modificar_imap_puerto"></p>');
    $('#div_modificar_correo_imap').append('<p>Correo: <input type="text" value="'+correo+'" id="modificar_imap_correo"></p>');
    $('#div_modificar_correo_imap').append('<p>Contraseña: <input type="password" value="'+password+'" id="modificar_imap_password1"></p>');
    $('#div_modificar_correo_imap').append('<p>Contraseña: <input type="password" value="'+password+'" id="modificar_imap_password2"></p>');
    
    $("#modificar_imap_select_tipo_correo option[value="+servidor+"]").attr("selected",true);    
    
    $('#modificar_imap_select_tipo_correo').change(function()
    {
        if($('#modificar_imap_select_tipo_correo').val()===servidor)
        {
            $('#modificar_imap_host').val('');
            $('#modificar_imap_host').val(host_imap);
        }
        else
        {
            var servidor_comun='';
            if($('#modificar_imap_select_tipo_correo').val()==='gmail'){servidor_comun="imap.gmail.com";}
            if($('#modificar_imap_select_tipo_correo').val()==='hotmail'){servidor_comun="imap-mail.outlook.com";}
            if($('#modificar_imap_select_tipo_correo').val()==='live'){servidor_comun="imap-mail.outlook.com";}
            if($('#modificar_imap_select_tipo_correo').val()==='yahoo'){servidor_comun="imap.mail.yahoo.com";}
            $('#modificar_imap_host').val('');
            $('#modificar_imap_host').val(servidor_comun);
        }
    });                                    
    
    $('#div_modificar_correo_imap').dialog({title:"Modificar un Correo",modal:true,closeOnEscape:false,position:"center",width:500,heigth:900,resizable:false,draggable:false,buttons: {"Modificar": function (){ comprobar_datos_modificar_imp(id_correo); },"Cancelar": function (){$(this).dialog("close");}}});    
}
/* Se comprueba que no existan campos vacios o caracteres especiales */
function comprobar_datos_modificar_imp(id_correo)
{
    var id_usuario_sistema=$('#id_usr').val();   /* Log */
    $('.alert_alta_imap_host').remove();
    
    var alerta=0;
    var host =$('#modificar_imap_host').val();
    var servidor=$('#modificar_imap_select_tipo_correo').val();
    var usuario=$('#modificar_imap_correo').val();
    var pass1=$('#modificar_imap_password1').val();
    var pass2=$('#modificar_imap_password2').val();
    var puerto=$('#modificar_imap_puerto').val();

    if(host.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#modificar_imap_host');}
    if(puerto.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#modificar_imap_puerto');}
    if(usuario.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#modificar_imap_correo');}
    if(pass1.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#modificar_imap_password1');}
    if(pass2.length==0){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Campo Requerido</font></p>').insertAfter('#modificar_imap_password2');}
    if(pass1!==pass2){alerta=1;$('<p class="alert_alta_imap_host"><font color="red">Las contraseñas no coinciden</font></p>').insertAfter('#modificar_imap_password1');
        $('<p class="alert_alta_imap_host"><font color="red">Las contraseñas no coinciden</font></p>').insertAfter('#modificar_imap_password2');}
    if(alerta==1){return;}
    
    var parametros="opcion=modificar_correo&id_usuario="+id_usuario_sistema+"&id_correo="+id_correo+"&servidor="+servidor+"&host="+host+'&puerto='+puerto+'&usuario='+usuario+'&password='+pass1;
    confirmar_modificar_imap(parametros);
}
/* Mensaje de confirmación para modificar los datos de una cuenta de correo IMAP */
function confirmar_modificar_imap(parametros)
{            
    if($('#confirmacion_modificar_correo').length>0){$('#confirmacion_modificar_correo').remove();}
    $('#admin_ventana_trabajo').append('<div id="confirmacion_modificar_correo"></div>');
    $('#confirmacion_modificar_correo').append('<p>¿Está seguro(a) de modificar los datos de la cuenta de correo?</p>');
    /* Mensaje de Confirmación */
    $('#confirmacion_modificar_correo').dialog({title:"Mensaje de Confirmación",modal:true,closeOnEscape:false,position:"center",width:300,heigth:250,resizable:false,draggable:false,buttons: {"Aceptar": function (){  $(this).dialog("close"); modificar_correo_imap(parametros);},"Cancelar": function (){$(this).dialog("close");}}});
}
function modificar_correo_imap(parametros)
{
    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */
    
    ajax=objetoAjax();
    ajax.open("POST", 'php/mail.php',true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8;");
    ajax.send(parametros);
    ajax.onreadystatechange=function() 
    {
        if (ajax.readyState===4 && ajax.status===200) 
       {
           $('#admin_div_loading').dialog('close');    
            $('#ventana_resultado').empty();
            if(ajax.responseXML==null)
            {$('#ventana_resultado').append(ajax.responseText);$('#ventana_resultado').dialog(ventana_error);return;}
            
            var xml=ajax.responseXML;
            var root=xml.getElementsByTagName("Modificar");  
            $('#ventana_resultado').dialog();
            for (i=0;i<root.length;i++) 
            { 
                var estado =root[i].getElementsByTagName("estado")[0].childNodes[0].nodeValue;
                var mensaje=root[i].getElementsByTagName("mensaje")[0].childNodes[0].nodeValue;
                if(estado==1)
                {                   
                    $('#ventana_resultado').dialog('option', 'title', 'Información de usuario modificada');
                    $('#ventana_resultado').append('<p><center><img src="img/success.png" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');
                    $('#div_modificar_correo_imap').dialog('close');
                    get_lista_correos();
                }else{                                  
                    $('#ventana_resultado').dialog('option', 'title', 'Error en la operación');  
                    $('#ventana_resultado').append('<p><center><img src="img/Alert.png" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');
                }                          
            }
       }
       
   }
}
/* Eliminar un Correo IMAP */
function confirmacio_eliminar_correo_imap(id_correo,correo)
{
    if($('#confirmacion_eliminar_correo').length>0){$('#confirmacion_eliminar_correo').remove();}
    $('#admin_ventana_trabajo').append('<div id="confirmacion_eliminar_correo"></div>');
    $('#confirmacion_eliminar_correo').append('<p>¿Está seguro(a) de dar de baja el correo '+correo+'?</p>');
    /* Mensaje de Confirmación */
    $('#confirmacion_eliminar_correo').dialog({modal:true,closeOnEscape:false,position:"center",width:300,heigth:250,resizable:false,draggable:false,title:"Eliminar un Correo",buttons: {"Aceptar": function (){  $(this).dialog("close"); eliminar_correo_imap(id_correo);},"Cancelar": function (){$(this).dialog("close");}}});

}
function eliminar_correo_imap(id_correo)
{       
    $( "#admin_div_loading" ).dialog({modal:true,closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */
               
    ajax=objetoAjax();
    ajax.open("POST", 'php/mail.php',true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8;");
    ajax.send("opcion=eliminar_correo&id_correo="+id_correo);
    ajax.onreadystatechange=function() 
    {
        if (ajax.readyState===4 && ajax.status===200) 
       {
            $('#admin_div_loading').dialog('close');    
            $('#ventana_resultado').empty();
            if(ajax.responseXML==null)
            {$('#ventana_resultado').append(ajax.responseText);$('#ventana_resultado').dialog(ventana_error);return;}
            
            var xml=ajax.responseXML;
            var root=xml.getElementsByTagName("Eliminar");  
            $('#ventana_resultado').dialog();
            for (i=0;i<root.length;i++) 
            { 
                var estado =root[i].getElementsByTagName("estado")[0].childNodes[0].nodeValue;
                var mensaje=root[i].getElementsByTagName("mensaje")[0].childNodes[0].nodeValue;
                if(estado==1)
                {                   
                    $('#ventana_resultado').dialog('option', 'title', 'Información de usuario modificada');
                    $('#ventana_resultado').append('<p><center><img src="img/success.png" title="carga carga pdf" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');
                    $('#fila_tabla_correo_'+id_correo).remove();
                }   
                else
                {              
                    $('#ventana_resultado').dialog('option', 'title', 'Error al actualizar datos de usuario');  
                    $('#ventana_resultado').append('<p><center><img src="img/Alert.png" title="error" width="40" heigth="40"></center></p>'+'<p>'+mensaje+'</p>');
                }                                                
            }
       }
   }
}