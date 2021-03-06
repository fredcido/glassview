dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide('modulo.master.auditoria' );

dojo.declare( 'modulo.master.auditoria', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
        
    },
    
    initGrid: function()
    {
        objGeral.loading( true );

        masterAuditoria.gridHeader = '#,'+
                                    objGeral.translate('Query')+','+
                                    objGeral.translate('Data')+','+
                                    objGeral.translate('Path')+','+
                                    objGeral.translate('Usuário')+','+
                                    objGeral.translate('Endereço IP');

        document.getElementById('gridMasterAuditoria').style.height= objGrid.gridHeight +"px";
        masterAuditoria.grid = new dhtmlXGridObject('gridMasterAuditoria');
        masterAuditoria.grid.setHeader( masterAuditoria.gridHeader );
        masterAuditoria.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter");
        masterAuditoria.grid.setInitWidths( objGrid.idWidth + ",*,180,180,180,220");
        masterAuditoria.grid.setColAlign("center,left,left,left,left,left");
        masterAuditoria.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        masterAuditoria.grid.setColSorting("str,str,str,str,str,str");
        masterAuditoria.grid.setSkin( objGrid.theme );
        masterAuditoria.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterAuditoriaGrid', true, 'divpagMasterAuditoria');
        masterAuditoria.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        masterAuditoria.grid.attachEvent( 'onRowDblClicked', masterAuditoria.edit );
        masterAuditoria.grid.init();
        masterAuditoria.grid.load( baseUrl + '/master/auditoria/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/auditoria/form/', 
                objGeral.translate('Auditoria')
            );
    },
    
    detalhar: function()
    {
        if ( !masterAuditoria.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para detalhamento.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/master/auditoria/edit/id/' + masterAuditoria.grid.getSelectedId(),
                objGeral.translate('Auditoria')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterAuditoria.grid] );
    }
});

var masterAuditoria = new modulo.master.auditoria();