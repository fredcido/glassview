dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.conta' );

dojo.declare( 'modulo.financeiro.conta', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroConta.gridHeader = '#,'+
                                                objGeral.translate('Descrição')+','+
                                                objGeral.translate('Banco')+','+
                                                objGeral.translate('Agência')+','+
                                                objGeral.translate('Número')+','+
                                                objGeral.translate('Saldo')+','+
                                                objGeral.translate('Status');
	
        document.getElementById('gridFinanceiroConta').style.height= objGrid.gridHeight +"px";
        financeiroConta.grid = new dhtmlXGridObject('gridFinanceiroConta');
        financeiroConta.grid.setHeader( financeiroConta.gridHeader );
        financeiroConta.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter");
        financeiroConta.grid.setInitWidths( objGrid.idWidth + ",*,300,150,150,200,100");
        financeiroConta.grid.setColAlign("center,left,left,right,right,right,left");
        financeiroConta.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
        financeiroConta.grid.setColSorting("str,str,str,str,str,na,str");
        financeiroConta.grid.setSkin( objGrid.theme );
        financeiroConta.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroContaGrid', true, 'divpagfinanceiroConta');
        financeiroConta.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroConta.grid.attachEvent( 'onRowDblClicked', financeiroConta.edit );
        financeiroConta.grid.init();
        financeiroConta.grid.load( baseUrl + '/financeiro/conta/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/conta/form/', 
                objGeral.translate('Conta')
            );
    },
    
    edit: function()
    {
        if ( !financeiroConta.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/conta/edit/id/' + financeiroConta.grid.getSelectedId(), 
                objGeral.translate('Conta'),
                financeiroConta.setRequired
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroConta.grid] );
    },
    
    setRequired: function(){
        
        this.tipoconta = dijit.byId('fn_conta_tipo').attr('value');
        
        if( this.tipoconta == '' ){
            
            dijit.byId('fn_banco_id').set( 'readOnly', true );
            dijit.byId('fn_conta_agencia').set( 'readOnly', true );
            dijit.byId('fn_conta_numero').set( 'readOnly', true );
            dijit.byId('fn_conta_saldo_inicial').set( 'readOnly', true );
            
            dijit.byId('fn_banco_id').set( 'value', '' );
            dijit.byId('fn_conta_agencia').set( 'value', '' );
            dijit.byId('fn_conta_numero').set( 'value', '' );
            dijit.byId('fn_conta_saldo_inicial').set( 'value', '' );

        }else{
            
            dijit.byId('fn_banco_id').attr( 'readOnly', false );
            dijit.byId('fn_conta_agencia').attr( 'readOnly', false );
            dijit.byId('fn_conta_numero').attr( 'readOnly', false );

            if( objGeral.empty( dojo.byId( 'lancamentos' ).value) ){

                dijit.byId('fn_conta_saldo_inicial').attr( 'readOnly', false );
            }

            switch( this.tipoconta )
            {
            case 'B':
                objGeral.setRequired('fn_banco_id' , true);
                objGeral.setRequired('fn_conta_agencia' , true);
                objGeral.setRequired('fn_conta_numero' , true);
            break;
            
            case 'C':
                dijit.byId('fn_banco_id').set( 'readOnly', true );
                dijit.byId('fn_banco_id').set( 'value', '' );
                dijit.byId('fn_conta_agencia').set( 'readOnly', true );
                dijit.byId('fn_conta_agencia').set( 'value', '' );
                objGeral.setRequired('fn_banco_id' , false);
                objGeral.setRequired('fn_conta_numero' , false);
                objGeral.setRequired('fn_conta_agencia' , false);
            break;
            
            default:
                objGeral.setRequired('fn_banco_id' , false);
                objGeral.setRequired('fn_conta_numero' , false);
                objGeral.setRequired('fn_conta_agencia' , false);
            }
        }
    }
});

var financeiroConta = new modulo.financeiro.conta();