/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global EnvironmentData */

var Enterprise = function()
{
    _FormsNewEnterpriseSystem = function()
    {        
        $('#admin_ventana_trabajo').empty();
        $('#admin_ventana_trabajo').append('<div class="titulos_ventanas">Agregar una empresa al sistema</div><br>');
        $('#admin_ventana_trabajo').append('<p>Ingrese los datos solicitados de la nueva empresa.</p><br>');
        $('#admin_ventana_trabajo').append('<table id = "NewEnterpriseTable"></table>');
        $('#NewEnterpriseTable').append('<tr><td>Alias</td><td><input type = "text" id = "NewEnterpriseAliasForm" class = "StandardForm required" FieldType = "VARCHAR" FieldLength = "50"></td></tr>');
        $('#NewEnterpriseTable').append('<tr><td>Nombre</td><td><input type = "text" id = "NewEnterpriseNameForm" class = "StandardForm required" FieldType = "VARCHAR" FieldLength = "100"></td></tr>');
        $('#NewEnterpriseTable').append('<tr><td>RFC</td><td><input type = "text" id = "NewEnterpriseRfcForm" class = "StandardForm required" FieldType = "VARCHAR" FieldLength = "50"></td></tr>');
        $('#NewEnterpriseTable').append('<tr><td>Firma electrónica (Pública)</td><td><input type = "file" id = "NewEnterprisePublicFileForm" FieldType = "file" class = "StandardForm required"></td></tr>');
        $('#NewEnterpriseTable').append('<tr><td>Firma electrónica (Privada)</td><td><input type = "file" id = "NewEnterprisePrivateFileForm" FieldType = "file" class = "StandardForm required"></td></tr>');
        $('#NewEnterpriseTable').append('<tr><td>Clave</td><td><input type = "password" id = "NewEnterprisePasswordForm" class = "StandardForm required" FieldType = "VARCHAR" FieldLength = "20"></td></tr>');
        
        var validator = new ClassFieldsValidator();
        validator.InspectCharacters($('#NewEnterpriseTable input'));
        
        var buttons = {"Aceptar":{"text":"Aceptar",click:function(){_NewEnterpriseSystem();}}};
        $( "#consola_administracion" ).dialog('option','buttons', buttons);
    };
    
    _NewEnterpriseSystem = function()
    {        
        var validator = new ClassFieldsValidator();
        var Validation = validator.ValidateFields($('#NewEnterpriseTable input'));
        
        var RegularExpresion = /^([a-zA-Z0-9\_])+$/g;
        var EnterpriseAlias = $('#NewEnterpriseAliasForm').val();
        if(!RegularExpresion.test(EnterpriseAlias))
        {
            Validation = 0;
            validator.AddClassRequiredActive($('#NewEnterpriseAliasForm'));
        }
        else
            validator.RemoveClassRequiredActive($('#NewEnterpriseAliasForm'));
        console.log(Validation);
        if(Validation===0)
            return;
        
        var NewNameEnterprise = $('#NewEnterpriseNameForm').val();
        var NewRfcEnterprise = $('#NewEnterpriseRfcForm').val();
        var NewPasswordEnterprise = $('#NewEnterprisePasswordForm').val();
        var data = new FormData();
        var PublicFile = $('#NewEnterprisePublicFileForm').prop('files');
        var PrivatecFile = $('#NewEnterprisePrivateFileForm').prop('files');
        var Files = new Array();
        var cont=0;
        
        Files[Files.length] = PublicFile[0];
        Files[Files.length] = PrivatecFile[0];
        
        data.append('PublicFile', Files[0]);
        data.append('PrivateFile', Files[1]);
        data.append('EnterpriseAlias',EnterpriseAlias);
        data.append("NewNameEnterprise",NewNameEnterprise);
        data.append("NewRfcEnterprise",NewRfcEnterprise);
        data.append("NewPasswordEnterprise",NewPasswordEnterprise);
        data.append("option","NewEnterpriseSystem");
        data.append("IdUser",EnvironmentData.IdUser);
        data.append("UserName",EnvironmentData.UserName);
                        
        $.ajax({
        async:false, 
        cache:false,
        processData: false,
        contentType: false,
        dataType:"html", 
        type: 'POST',   
        url: "php/Enterprise.php",
        data: data, 
        success:  function(xml)
        {   
            if($.parseXML(xml)===null){Error(xml); return 0;}else xml=$.parseXML( xml );
            
            if($(xml).find('NewEnterprise').length>0)
            {
                var Mensaje = $(xml).find('Mensaje').text();
                Notificacion(Mensaje);
                $('#LinkNewEnterprise').click();
            }
            
            $(xml).find("Error").each(function()
            {
                var mensaje = $(this).find("Mensaje").text();
                Error(mensaje);
                xml = 0;
                return 0;
            });                            
        },
        beforeSend:function(){},
        error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
        });
    };
};

Enterprise.prototype.GetListEnterprisesXml = function(content) /* Emisores (Facturas) */
{
    var xml = 0;
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Enterprise.php",
    data: 'option=GetListEnterprisesXml&IdUser='+EnvironmentData.IdUser+ '&UserName='+EnvironmentData.UserName+'&content='+content, 
    success:  function(response)
    {   
        if($.parseXML(response)===null){Error(response); return 0;}else xml=$.parseXML( response );
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            xml = 0;
            return 0;
        });             
        return xml;
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
    
    return xml;
};

Enterprise.prototype.GetSystemEnterprises = function()
{
    var xml = 0;
    $.ajax({
    async:false, 
    cache:false,
    dataType:"html", 
    type: 'POST',   
    url: "php/Enterprise.php",
    data: 'option=GetSystemEnterprises', 
    success:  function(response)
    {   
        if($.parseXML(response)===null){Error(response); return 0;}else xml=$.parseXML( response );
        $(xml).find("Error").each(function()
        {
            var mensaje = $(this).find("Mensaje").text();
            Error(mensaje);
            xml = 0;
            return 0;
        });             
        return xml;
    },
    beforeSend:function(){},
    error: function(jqXHR, textStatus, errorThrown){ Error(textStatus +"<br>"+ errorThrown);}
    });
    
    return xml;
};
/*------------------------------------------------------------------------------
 * Descripción: Método llamado desde SystemManagement para el registro de una
 *              nueva empresa (Instancia)
 ------------------------------------------------------------------------------*/
Enterprise.prototype.NewEnterpriseSystem = function()
{
    _FormsNewEnterpriseSystem();
    var validator = new ClassFieldsValidator();        
};