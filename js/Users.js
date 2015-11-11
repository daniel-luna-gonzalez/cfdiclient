/* global OptionsDataTable, DatePicker */

var Users = function()
{
    
};

Users.prototype.CheckIfExistRoot = function()
{
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/DataBase.php",
    data: 'option=CreateInstanciaCSDOCS', 
    success:  function(xml)
    {   
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });  
    
    return 1;
};
/*
 * **************************************************************************************************
 *                                      ALTA DE USUARIO                                             *
 * **************************************************************************************************
 */
function admin_alta_usuario()
{
    var now = new Date();

    $( "#registro_admin_paso1" ).html("");/* Se ocupan las mismas cajas de texto que alta_sistema.js */
    $('#admin_ventana_trabajo').html('');
    $('#admin_ventana_trabajo').append('<div class="titulos_ventanas">Alta de Usuario</div>');
    $('#admin_ventana_trabajo').append('<br><p>Ingrese la información solicitada para dar de alta un nuevo usuario del sistema</p>');
    $( "#admin_ventana_trabajo" ).append('<br><p>Nombre(s):</p> <input type = "text" class="form_admin" id="fra_nombre" class="fra1_nombre">');
    $( "#admin_ventana_trabajo" ).append('<p>Apellido Materno:</p> <input type = "text" class="form_admin" id="fra_apellido_p">');
    $( "#admin_ventana_trabajo" ).append('<div><p>Apellido Paterno:</p> <input type = "text" class="form_admin" id="fra_apellido_ma"></div>');
    $( "#admin_ventana_trabajo" ).append('<div><p>Fecha de Nac:</p><input type = "text" class="form_admin" id="fra_fecha_nac"></div>');
    $( "#admin_ventana_trabajo" ).append('<div><p>Curp:</p><input type = "text" class="form_admin" id="fra_curp"></div>');
    $( "#admin_ventana_trabajo" ).append('<div><p><br><b>Ingrese el nombre de usuario que desee que aparezca en el sistema</b><br></p></div>');
    $( "#admin_ventana_trabajo" ).append('<div><br><p>Nombre de Usuario:</p><input type = "text" class="form_admin" id="fra_usuario" placeholder="p.e. Daniel0313"></div>');
    $( "#admin_ventana_trabajo" ).append('<div><br><b><p>Seleccione una constraseña mayor a 6 caracteres (sin utilizar espacios ni caracteres especiales p.e. !"#$%&/()=_-*[]{}+</p></b><br></div>');
    $( "#admin_ventana_trabajo" ).append('<div><br><p>Contraseña:</p><input type = "password" class="form_admin" id="fra_password1" onkeyup="valid(this)"></div>');
    $( "#admin_ventana_trabajo" ).append('<div><p>Repita su contraseña:</p><input type = "password" class="form_admin" id="fra_password2" onkeyup="valid(this)"></div>');
//    $( "#admin_ventana_trabajo" ).append('<br><center><input type="button" class="boton_admin" id="fra1_btn_fin" value="Finalizar" style="cursor: pointer" onclick="admin_comprobar_paso1();"></center>');
    
    $('#fra_fecha_nac').datepicker({defaultDate: "1991/07/21", dateFormat:'yy/mm/dd', navigationAsDateFormat:true, changeMonth: true,changeYear: true, numberOfMonths: 3,yearRange: "1945:"+now.getFullYear() });
        
    $( "#consola_administracion" ).dialog( "option", "buttons", [{text: "Agregar Usuario",click: function() { admin_comprobar_paso1(); }}]);        
    
    $("input").focus(function(){
                $(this).addClass("seleccionado");
        });
        $("input").blur(function(){
                $(this).removeClass("seleccionado");  

        });    
}

/*
 * Se analizan los datos personales introducidos por el susuario
 */
function admin_comprobar_paso1()
{
    var nombre=$('#fra_nombre').val();
    var apellido_paterno=$('#fra_apellido_paterno').val();
    var apellido_materno=$('#fra_apellido_materno').val();
    var fra_fecha_nac=$('#fra_fecha_nac').val(); 
    var fra_curp=$('#fra_curp').val(); 
    var fra_usuario=$('#fra_usuario').val();
//    var fra_correo=$('#fra_correo').val();
    var fra_password1=$('#fra_password1').val();
    var fra_password2=$('#fra_password2').val();
    
    if(nombre=="")
        {
            if($('#p_fra_nombre').length==0)
                {
                    $('<font color="red"><p id="p_fra_nombre">Campo obligatorio</p></font>').insertAfter('#fra_nombre');                    
                }
        }
        else{$('#p_fra_nombre').remove();}            
        if(apellido_paterno=="")
        {
            if($('#p_fra_apellido_paterno').length==0)
                {
                    $('<font color="red"><p id="p_fra_apellido_paterno">Campo obligatorio</p></font>').insertAfter('#fra_apellido_paterno');
                }            
        }
        else{$('#p_fra_apellido_paterno').remove();}    
        if(fra_fecha_nac=="")
        {
            if($('#p_fra_fecha_nac').length==0)
                {
                    $('<font color="red"><p id="p_fra_fecha_nac">Campo obligatorio</p></font>').insertAfter('#fra_fecha_nac');
                }            
        } 
        else{$('#p_fra_fecha_nac').remove();}
        if(fra_curp=="")
        {
            if($('#p_fra_curp').length==0)
                {
                    $('<font color="red"><p id="p_fra_curp">Campo obligatorio</p></font>').insertAfter('#fra_curp');
                }            
        } 
        else{$('#p_fra_curp').remove();}
        if(fra_usuario=="")
        {
            if($('#p_fra_usuario').length==0)
                {
                    $('<font color="red"><p id="p_fra_usuario">Campo obligatorio</p></font>').insertAfter('#fra_usuario');
                }
            
        }
        else{$('#p_fra_usuario').remove();}
//        if(fra_correo=="")
//        {
//            if($('#p_fra_correo').length==0)
//                {
//                    $('<font color="red"><p id="p_fra_correo">Campo obligatorio</p></font>').insertAfter('#fra_correo');
//                }
//            
//        }
//        else{$('#p_fra_correo').remove();}
        if(fra_password1=="")
        {
            if($('#p_fra_password1').length==0)
                {
                    $('<font color="red"><p id="p_fra_password1">Campo obligatorio</p></font>').insertAfter('#fra_password1');
                }            
        } 
        else{$('#p_fra_password1').remove();}
        if(fra_password2=="")
        {
            if($('#p_fra_password2').length==0)
                {
                    $('<font color="red"><p id="p_fra_password2">Campo obligatorio</p></font>').insertAfter('#fra_password2');
                }            
        }
        else{$('#p_fra_password2').remove();}          
        
    if(nombre!="" && apellido_paterno!="" && fra_fecha_nac!="" && fra_curp!="" && fra_usuario!="")
        {
            if(fra_password1==fra_password2 && fra_password1!="" && fra_password2!="" && fra_password1.length>=6 && fra_password2.length>=6)
            {               
                //Datos correctos               
                        $('#p_fra_password1').remove();
                        $('#p_fra_password2').remove();
                        $('#p2').remove();
                        $('#p3').remove();       
                        $('#p1').remove();    
                        admin_confirm_alta();
            }                             
                        
                    //Cuando las contraseñas son menores a 6 caracteres
            if(fra_password1.length<6 || fra_password1.length<6 && fra_password1==fra_password2)
                {
                    if($('#p2').length==0)
                        {
                            $('<font color="red"><p id="p2">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password1');                            
                        }
                }
            else
                {
                        $('#p2').remove();
                }
            if(fra_password1.length<6 || fra_password1.length<6 && fra_password1==fra_password2)
                {
                 if($('#p3').length==0)
                        {                 
                            $('<font color="red"><p id="p3">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password2');
                        }
                }
                else
                {
                        $('#p3').remove();
                }

                
            //Cuando las contraseñas no coinciden
            if(fra_password1!=fra_password2)
                {
                    if($('#p1').length==0)
                    {
                        $('<font color="red"><p id="p1">Las contraseñas no coinciden</p></font>').insertAfter('#fra_password1');
                        $( "#fra1" ).append('<font color="red"><p id="p1">Las contraseñas no coinciden</p></font>');
                    }  
                }
                else
                    {
                        $( "#p1" ).remove();
                    }
        }
        
        //Si las contraseñas son menores a 6 caracteres        
        if(fra_password1.length<6)
                {
                    if($('#p2').length==0)
                        {
                            $('<font color="red"><p id="p2">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password1');                            
                        }
                }
            else
                {
                        $('#p2').remove();
                }
            if(fra_password1.length<6)
                {
                 if($('#p3').length==0)
                        {                 
                            $('<font color="red"><p id="p3">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password2');
                        }
                }
            else
            {
                    $('#p3').remove();
            }
            
}    
    
    function admin_confirm_alta()
    {
        $('#admin_confirmacion').html('');
        $('#admin_confirmacion').append('<p><h2>Su información esta a punto de ser almacenada, ¿Es correcta?</h2></p>');
        $('#admin_confirmacion').dialog({height: 150,width:300,title:'Mensaje de Confirmación',closeOnEscape:false, modal: true,      
            buttons:{"Si": function() { $( this ).dialog( "close" );admin_insert_usuario(); },"Cancelar": function() {$( this ).dialog( "close" );}
                    }});      
    }

function admin_insert_usuario()
{
    var fra1_nombre=$('#fra_nombre').val();
    var fra_apellido_p=$('#fra_apellido_p').val();
    var fra_apellido_ma=$('#fra_apellido_ma').val();
    var fra_fecha_nac=$('#fra_fecha_nac').val();
    var fra_curp=$('#fra_curp').val();
    var fra_usuario=$('#fra_usuario').val();
    var fra_password1=$('#fra_password1').val();
    $('#admin_ventana_trabajo').append('<center><img src="img/loading.gif" id="loading_send" title="Enviando correos" width="20" heigth="20"><center>'); 

    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Users.php",
    data: "option=NewUser&nombre="+fra1_nombre+"&apellido_p="+fra_apellido_p+"&apellido_m="+fra_apellido_ma
        +"&fecha_nac="+fra_fecha_nac+"&curp="+fra_curp+"&usuario="+fra_usuario+"&password="+fra_password1, 
    success:  function(xml)
    {   
        $('#loading_send').remove();
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        $(xml).find('NewUser').each(function()
        {
            var Mensaje = $(this).find('Mensaje').text();
            Notificacion(Mensaje);
            $('#admin_alta_usuario').click();    
        });
        
        $(xml).find('RepeatedUser').each(function()
        {
            var Mensaje = $(this).find('Mensaje').text();
            Notificacion(Mensaje);
                
        });
        
        
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
}

/*
 * **************************************************************************************************
 *                                      LISTADO DE USUARIOS                                         *
 * **************************************************************************************************
 */

function listado_usuarios_activos()
{
    $( "#consola_administracion" ).dialog( "option", "buttons", []);
    $('#admin_ventana_trabajo').html('');
    $( "#admin_div_loading" ).dialog({closeOnEscape:false,position:"center",open: function(event, ui) {$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();}});
    $("#admin_div_loading").siblings('div.ui-dialog-titlebar').remove();/* Borra barra de título */
    $('#admin_ventana_trabajo').append('<div class="titulos_ventanas">Listado de Usuarios del Sistema</div><br><br>');
 
    $('#admin_ventana_trabajo').append('<center><img src="img/loading.gif" id="loading_send" title="Enviando correos" width="20px" heigth="20px"><center>');         
    
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Users.php",
    data: "option=UserList", 
    success:  function(xml)
    {   
        $('#loading_send').remove();
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        if($(xml).find('UserList').length>0)
            build_tabla_usuarios(xml);
        
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
}

function build_tabla_usuarios(xml)
{
    var TableUserListdT, TableUserListDT;
    $('#div_tabla_registros').remove();
    $('#admin_ventana_trabajo').append('<div id="div_tabla_registros"></div>');
    
    $('#div_tabla_registros').append('<table id = "TableUserList" class = "display hover"></table>');
    $('#TableUserList').append('<thead><tr><th>Nombre Usuario</th><th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Curp</th><th>Fecha Nacimiento</th><th>Fecha Alta</th><th>Operaciones</th></tr></thead>');
    
    TableUserListdT = $('#TableUserList').dataTable(OptionsDataTable);    
    TableUserListDT = new $.fn.dataTable.Api('#TableUserList');
//    TableUserListdT.fnSetColumnVis(0,false);

    $(xml).find('User').each(function()
    {
        var id_login = $(this).find('IdUser').text();
        var nombre_usuario = $(this).find('UserName').text();
        var nombre = $(this).find('Name').text();
        var apellido_paterno = $(this).find('LastName').text();
        var apellido_materno = $(this).find('MLastName').text();
        var curp = $(this).find('curp').text();
        var fecha_nac = $(this).find('BornDate').text();
        var fecha_alta = $(this).find('RegistrationDate').text();
        
        var EditImg = '<img src = "img/edit_icon.png" style = "cursor:pointer" width = "40px" heigth = "40px" title = "Editar Usuario" onclick = "return_xml_info_admin('+id_login+')">';
        var DeleteImg = '<img src = "img/delete_icon.png" style = "cursor:pointer" width = "40px" heigth = "40px" title = "Elminar Usuario" onclick = "admin_confirmacion_delete('+id_login+',\''+nombre_usuario+'\')">';
        
        var data = 
       [
            nombre_usuario,
            nombre,
            apellido_paterno,
            apellido_materno,
            curp,                
            fecha_nac,
            fecha_alta,
            EditImg+DeleteImg
       ];   

        var ai = TableUserListDT.row.add(data).draw();
        var n = TableUserListdT.fnSettings().aoData[ ai[0] ].nTr;
        n.setAttribute('id',id_login);                                          
    });
    
    $('#TableUserList tbody').on( 'click', 'tr', function ()
    {
        TableUserListDT.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');        
    } ); 
}

function admin_confirmacion_delete(id,usuario)
{
    $( "#admin_confirmacion_delete" ).html('');
    $( "#admin_confirmacion_delete" ).append('<p><h2><center>¿Desea continuar con la eliminación del usuario "'+usuario+'"?</center></h2></p>');
        $( "#admin_confirmacion_delete" ).dialog({height: 200, width:300,modal: true, closeOnEscape:false,title:'Mensaje de confirmación',
            buttons: {"Aceptar": function (){ $(this).dialog("destroy"); delete_user(id);},"Cerrar": function (){$(this).dialog("destroy");}}                          
        });
}

function delete_user(id)
{
    $('#admin_ventana_trabajo').append('<center><img src="img/loading.gif" id="loading_send" title="Enviando correos" width="20" heigth="20"><center>');    
    
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Users.php",
    data: "option=DeleteUser&IdUser="+id, 
    success:  function(xml)
    {   
        $('#loading_send').remove();
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        $(xml).find('DeletedUser').each(function()
        {
            var Mensaje = $(this).find('Mensaje').text();
            Notificacion(Mensaje);
            listado_usuarios_activos();    
        });
        
        
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
}

function return_xml_info_admin(id)
{
    
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Users.php",
    data: "option=GetUserInfo&IdUser="+id, 
    success:  function(xml)
    {   
        $('#loading_send').remove();
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        if($(xml).find('UserInfo').length>0)
        {
            show_info_user(xml);
        }
        
        
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
}

/* Se muestra la información del usuario para ser editada */
function show_info_user(xml)
{        
    console.log(xml);
    var now = new Date();
    
    $(xml).find('User').each(function()
    {
        var id_login = $(this).find('IdUser').text();
        var nombre_usuario = $(this).find('UserName').text();
        var nombre = $(this).find('Name').text();
        var apellido_paterno = $(this).find('LastName').text();
        var apellido_materno = $(this).find('MLastName').text();
        var curp = $(this).find('curp').text();
        var fecha_nac = $(this).find('DateBorn').text();
        var fecha_alta = $(this).find('RegistrationDate').text();
        var password = $(this).find('password').text();
        
       
        $( "#admin_edit_usuario" ).html('');
        $( "#admin_edit_usuario" ).append('<p><h2><center>Información del Ususario</center></h2></p>');

        $( "#admin_edit_usuario" ).append('<br><p><label>Nombre(s):</label><input type = "text" id="fra_nombre" class="fra1_nombre"></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Apellido Materno:</label><input type = "text" id="fra_apellido_p"></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Apellido Paterno:</label><input type = "text" id="fra_apellido_ma"></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Fecha de Nac:</label><input type = "text" id="fra_fecha_nac" readonly></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Curp:</label><input type = "text" id="fra_curp"></p>');
        $( "#admin_edit_usuario" ).append('<p><br><b>Ingrese el nombre de usuario que desee que aparezca en el sistema</b><br></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Nombre de Usuario:</label><input type = "text" id="fra_usuario" placeholder="p.e. Daniel0313"></p>');
        $( "#admin_edit_usuario" ).append('<br><br><b>Seleccione una constraseña mayor a 6 caracteres (sin utilizar espacios ni caracteres especiales p.e. !"#$%&/()=_-*[]{}+</b><br>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Contraseña:</label><input type = "password" id="fra_password1" onkeyup="valid(this)"></p>');
        $( "#admin_edit_usuario" ).append('<br><p><label>Repita su contraseña:</label><input type = "password" id="fra_password2" onkeyup="valid(this)"></p>');
        $( "#admin_edit_usuario" ).append('<input type = "text" id="fra_id_login" style="visibility:hidden">');
    //    $( "#admin_edit_usuario" ).append('<br><center><input type="button" id="fra1_btn_fin" class="boton_admin" value="Finalizar" style="cursor: pointer" onclick="admin_confirmacion_modificar();"></center>');

        $('#fra_id_login').val(id_login);/* Campo que guarda el id_login del usuario a modificar */
        $('#fra_nombre').val(nombre);
        $('#fra_apellido_p').val(apellido_paterno);
        $('#fra_apellido_ma').val(apellido_materno);
        $('#fra_fecha_nac').val(fecha_nac);
        $('#fra_curp').val(curp);
        $('#fra_usuario').val(nombre_usuario);
        $('#fra_password1').val(password);
        $('#fra_password2').val(password);

        $('#fra_fecha_nac').datepicker(DatePicker);
    });
    
        $( "#admin_edit_usuario" ).dialog({height: 600,width:500,modal: true,closeOnEscape:false,title:'Modificar Usuario',
            buttons: {"Actualizar": function (){admin_confirmacion_modificar();}, "Cancelar": function (){ $(this).dialog("close"); }}
        });
        
        $("input").focus(function(){
                $(this).addClass("seleccionado");
        });
        $("input").blur(function(){
                $(this).removeClass("seleccionado");  

        });
}
function admin_confirmacion_modificar()
{
    $('#admin_confirmacion_modificar').html('');
    $('#admin_confirmacion_modificar').append('<p><h2>¿La información que va almacenar es correcta?</h2></p>');
    $( "#admin_confirmacion_modificar" ).dialog(
        {height: 200,width:300, modal: true,  closeOnEscape:false, title:'Mensaje de confirmación',
            buttons: {"Aceptar": function (){$(this).dialog("destroy");modificar_usuario();},
                "Cerrar": function (){$(this).dialog("destroy");}}                          
        });
    
}
/* Manda la nueva información del usuario para ser modificada */

function modificar_usuario()
{
    var id_login=$('#fra_id_login').val();
    var fra1_nombre=$('#fra_nombre').val();
    var fra_apellido_p=$('#fra_apellido_p').val();
    var fra_apellido_ma=$('#fra_apellido_ma').val();
    var fra_fecha_nac=$('#fra_fecha_nac').val();
    var fra_curp=$('#fra_curp').val();
    var fra_usuario=$('#fra_usuario').val();
    var fra_password1=$('#fra_password1').val();
    $('#div_loading').append('<center><img src="img/loading.gif" id="loading_send" title="Enviando correos" width="20" heigth="20"><center>'); 

    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Users.php",
    data: "option=ModifyUser&id_login="+id_login+"&nombre="+fra1_nombre+"&apellido_paterno="+fra_apellido_p+
            "&apellido_materno="+fra_apellido_ma+"&fecha_nac="+fra_fecha_nac+"&curp="+fra_curp+
            "&nombre_usuario="+fra_usuario+"&password="+fra_password1, 
    success:  function(xml)
    {   
        $('#loading_send').remove();
        if($.parseXML( xml )===null){Error(xml); return 0;}else xml=$.parseXML( xml );

        $(xml).find('ModifyUser').each(function()
        {
            var Mensaje = $(this).find('Mensaje').text();
            Notificacion(Mensaje);
            listado_usuarios_activos();
            $('#admin_edit_usuario').dialog('destroy');
        });
        
        
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            return 0;
        });    
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
}

/*
 * Se analizan los datos personales introducidos por el susuario
 */
function admin_modificar_comprobar()
{
    var nombre=$('#fra_nombre').val();
    var apellido_paterno=$('#fra_apellido_paterno').val();
    var apellido_materno=$('#fra_apellido_materno').val();
    var fra_fecha_nac=$('#fra_fecha_nac').val(); 
    var fra_curp=$('#fra_curp').val(); 
    var fra_usuario=$('#fra_usuario').val();
//    var fra_correo=$('#fra_correo').val();
    var fra_password1=$('#fra_password1').val();
    var fra_password2=$('#fra_password2').val();
    
    if(nombre=="")
        {
            if($('#p_fra_nombre').length==0)
                {
                    $('<font color="red"><p id="p_fra_nombre">Campo obligatorio</p></font>').insertAfter('#fra_nombre');                    
                }
        }
        else{$('#p_fra_nombre').remove();}            
        if(apellido_paterno=="")
        {
            if($('#p_fra_apellido_paterno').length==0)
                {
                    $('<font color="red"><p id="p_fra_apellido_paterno">Campo obligatorio</p></font>').insertAfter('#fra_apellido_paterno');
                }            
        }
        else{$('#p_fra_apellido_paterno').remove();}    
        if(fra_fecha_nac=="")
        {
            if($('#p_fra_fecha_nac').length==0)
                {
                    $('<font color="red"><p id="p_fra_fecha_nac">Campo obligatorio</p></font>').insertAfter('#fra_fecha_nac');
                }            
        } 
        else{$('#p_fra_fecha_nac').remove();}
        if(fra_curp=="")
        {
            if($('#p_fra_curp').length==0)
                {
                    $('<font color="red"><p id="p_fra_curp">Campo obligatorio</p></font>').insertAfter('#fra_curp');
                }            
        } 
        else{$('#p_fra_curp').remove();}
        if(fra_usuario=="")
        {
            if($('#p_fra_usuario').length==0)
                {
                    $('<font color="red"><p id="p_fra_usuario">Campo obligatorio</p></font>').insertAfter('#fra_usuario');
                }
            
        }
        else{$('#p_fra_usuario').remove();}
//        if(fra_correo=="")
//        {
//            if($('#p_fra_correo').length==0)
//                {
//                    $('<font color="red"><p id="p_fra_correo">Campo obligatorio</p></font>').insertAfter('#fra_correo');
//                }
//            
//        }
//        else{$('#p_fra_correo').remove();}
        if(fra_password1=="")
        {
            if($('#p_fra_password1').length==0)
                {
                    $('<font color="red"><p id="p_fra_password1">Campo obligatorio</p></font>').insertAfter('#fra_password1');
                }            
        } 
        else{$('#p_fra_password1').remove();}
        if(fra_password2=="")
        {
            if($('#p_fra_password2').length==0)
                {
                    $('<font color="red"><p id="p_fra_password2">Campo obligatorio</p></font>').insertAfter('#fra_password2');
                }            
        }
        else{$('#p_fra_password2').remove();}          
        
    if(nombre!="" && apellido_paterno!="" && fra_fecha_nac!="" && fra_curp!="" && fra_usuario!="")
        {
            if(fra_password1==fra_password2 && fra_password1!="" && fra_password2!="" && fra_password1.length>=6 && fra_password2.length>=6)
            {               
                //Datos correctos               
                        $('#p_fra_password1').remove();
                        $('#p_fra_password2').remove();
                        $('#p2').remove();
                        $('#p3').remove();       
                        $('#p1').remove();    
                        modificar_usuario();
            }                             
                        
                    //Cuando las contraseñas son menores a 6 caracteres
            if(fra_password1.length<6 || fra_password1.length<6 && fra_password1==fra_password2)
                {
                    if($('#p2').length==0)
                        {
                            $('<font color="red"><p id="p2">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password1');                            
                        }
                }
            else
                {
                        $('#p2').remove();
                }
            if(fra_password1.length<6 || fra_password1.length<6 && fra_password1==fra_password2)
                {
                 if($('#p3').length==0)
                        {                 
                            $('<font color="red"><p id="p3">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password2');
                        }
                }
                else
                {
                        $('#p3').remove();
                }

                
            //Cuando las contraseñas no coinciden
            if(fra_password1!=fra_password2)
                {
                    if($('#p1').length==0)
                    {
                        $('<font color="red"><p id="p1">Las contraseñas no coinciden</p></font>').insertAfter('#fra_password1');
                        $( "#fra1" ).append('<font color="red"><p id="p1">Las contraseñas no coinciden</p></font>');
                    }  
                }
                else
                    {
                        $( "#p1" ).remove();
                    }
        }
        
        //Si las contraseñas son menores a 6 caracteres        
        if(fra_password1.length<6)
                {
                    if($('#p2').length==0)
                        {
                            $('<font color="red"><p id="p2">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password1');                            
                        }
                }
            else
                {
                        $('#p2').remove();
                }
            if(fra_password1.length<6)
                {
                 if($('#p3').length==0)
                        {                 
                            $('<font color="red"><p id="p3">La contraseña debe ser mayor a 6 caracteres</p></font>').insertAfter('#fra_password2');
                        }
                }
            else
            {
                    $('#p3').remove();
            }
            
}    
