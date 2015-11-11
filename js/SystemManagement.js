/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global BotonesWindow */

$(document).ready(function()
{
    $('#LinkNewEnterprise').click(function()
    {
        var System = new SystemManagement();
        System.NewEnterprise();
    });
    
    $('#LinkKEnterpriseList').click(function()
    {
        var System = new SystemManagement();
        System.ShowListEnterprise();
    });
});

var SystemManagement = function()
{
    
    _ShowListEnterprise = function(Enterprise)
    {
        console.log(Enterprise);
    };        
};

SystemManagement.prototype.NewEnterprise = function()
{
    var self = this;
    var enterprise = new Enterprise();
    enterprise.NewEnterpriseSystem();
    
    var self = this;
};

SystemManagement.prototype.ShowListEnterprise = function()
{
    
    var self = this;
    $( "#consola_administracion" ).dialog('option','buttons', {});
    $('#admin_ventana_trabajo').empty();
    var enterprise = new Enterprise();
    var EnterpriseList = enterprise.GetSystemEnterprises();
    
    if($.isXMLDoc(EnterpriseList))
    {
        _ShowListEnterprise(EnterpriseList);
    }
};

function show_administrador()
{
    $('#admin_ventana_trabajo').empty();    
    
    $("#consola_administracion").dialog({minHeight: 550,minWidth: 1000,   closeOnEscape:false,position:"center",title:'Consola de Administración'}).dialogExtend(BotonesWindow);          
                
    /*  Permite que varios acordeones esten abiertos al mismo tiempo  */    
    $("#accordion > div").accordion({ header: "h3", collapsible: true });
    
     $('#accordion table').on( 'click', 'tr', function ()
    {
        var active = $('#accordion table tr.TableInsideAccordionFocus');                
        $('#accordion table tr').removeClass('TableInsideAccordionFocus');
        $('#accordion table tr').removeClass('TableInsideAccordionActive');
        $(active).addClass('TableInsideAccordionFocus');
        $(this).removeClass('TableInsideAccordionHoverWithoutClass');
        $(this).addClass('TableInsideAccordionActive');     
    });
    $('#accordion table tr').hover(function()
    {
        if($(this).hasClass('TableInsideAccordionActive') || $(this).hasClass('TableInsideAccordionFocus'))
            $(this).addClass('TableInsideAccordionHoverWithClass');
        else
            $(this).addClass('TableInsideAccordionHoverWithoutClass');
    });
    $('#accordion table tr').mouseout(function()
    {
        if($(this).hasClass('TableInsideAccordionActive') || $(this).hasClass('TableInsideAccordionFocus'))
            $(this).removeClass('TableInsideAccordionHoverWithClass');
        else
            $(this).removeClass('TableInsideAccordionHoverWithoutClass');
    });
    
    $('#LinkNewEnterprise').click();
}