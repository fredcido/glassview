/**
 * @version $Id: tipo_estoque.js 218 2012-02-14 01:09:13Z helion $
 */

dojo.require("modulo.padrao.geral");
dojo.require("modulo.padrao.grid");

dojo.provide('modulo.almoxarifado.estoque');

dojo.declare( 'modulo.almoxarifado.estoque', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
	
        document.getElementById( 'gridAlmoxarifadoEstoque' ).style.height= objGrid.gridHeight +"px";
	almoxarifadoEstoque.grid = new dhtmlXGridObject('gridAlmoxarifadoEstoque');
	almoxarifadoEstoque.grid.setHeader("#,Produto,Tipo,Fluxo,Data,Quantidade,Valor unitário,Total");
	almoxarifadoEstoque.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#text_filter,#numeric_filter,#text_filter,#text_filter");
	almoxarifadoEstoque.grid.setInitWidths( objGrid.idWidth + ",*,200,200,150,150,200,200");
	almoxarifadoEstoque.grid.setColAlign("center,left,left,left,left,right,right,right");
	almoxarifadoEstoque.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
	almoxarifadoEstoque.grid.setColSorting("int,str,str,str,date,int,na,na");
	almoxarifadoEstoque.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 6);
	almoxarifadoEstoque.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 7);
	almoxarifadoEstoque.grid.setSkin( objGrid.theme );
	almoxarifadoEstoque.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoEstoqueGrid', true, 'divpagAlmoxarifadoEstoque');
	almoxarifadoEstoque.grid.attachFooter( objGeral.translate('Movimentações') + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
        almoxarifadoEstoque.grid.attachEvent( 'onRowDblClicked', almoxarifadoEstoque.edit );
	almoxarifadoEstoque.grid.init();
	almoxarifadoEstoque.grid.load( baseUrl + '/almoxarifado/estoque/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( '/almoxarifado/estoque/form/', 'Movimentação' );
    },
    
    visualizar: function()
    {
        if ( !almoxarifadoEstoque.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( 'Selecione o lançamento para visualizar.' );
            return false;
        }
            
        objGeral.createDialog( '/almoxarifado/estoque/visualizar/id/' + almoxarifadoEstoque.grid.getSelectedId(), 'Visualização de lançamento' );
        
        return true;
    },
    
    calculaTotal: function()
    {
	var qtde = parseInt( dijit.byId( 'estoque_quantidade' ).get( 'value' ) );
	var valor = parseFloat( dijit.byId( 'estoque_valor_atual' ).get( 'value' ) );
	
	dijit.byId( 'estoque_valor_total' ).set( 'value', valor * qtde );
    },
    
    atualizarGrid: function()
    {
	if ( almoxarifadoEstoque.grid )
	        objGeral.atualizarGrids( [almoxarifadoEstoque.grid] );
    },
    
    buscaProduto: function()
    {
	var produtoWidget = dijit.byId( 'produto_id' )
	var id = produtoWidget.get( 'value' );
	
	this.bloqueiaCamposMov( true );
	
	if ( objGeral.empty( id ) )
	    return false;
	
	var self = this;
	
	var obj = {
	    url: baseUrl + '/almoxarifado/estoque/dados-produto/id/' + id,
	    handle: 'json',
	    callback: function( response )
	    {
		if ( !response.valid )
		    produtoWidget.set( 'value', '' );
		else {
		    
		    self.bloqueiaCamposMov( false );
		    
		    dijit.byId( 'formAlmoxarifadoEstoque' ).setValues( response.data );
		}
	    },
	    callbackError: function()
	    {
		produtoWidget.set( 'value', '' );
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    bloqueiaCamposMov: function( flag )
    {
	var notRelease = [ 'estoque_valor_total', 'estoque_quantidade' ]
	dijit.findWidgets( dojo.byId( 'fieldset-elementosmov' ) ).forEach( 
	    function( item ) 
	    {
		if ( dojo.indexOf(  notRelease , item.get( 'id' ) ) < 0 )
		    item.set( 'readOnly', flag );
		
		item.reset && item.reset();
	    }
	);
    },
    
    tipoMovimentacao: function()
    {
	var tipo = dijit.byId( 'estoque_tipo' ).get( 'value' );
	
	dijit.byId( 'estoque_quantidade' ).set( 'readOnly', objGeral.empty( tipo ) );
	
	if ( objGeral.empty( tipo ) )
	    return false;
	
	var estoqueMax = dijit.byId( 'produto_estoque_max' ).get( 'value' );
	var estoqueMin = dijit.byId( 'produto_estoque_min' ).get( 'value' );
	var estoqueAtual = dijit.byId( 'estoque_qtde_anterior' ).get( 'value' );
	var maxQtde = null;
	
	if ( 'E' == tipo && !objGeral.empty( estoqueMax ) )
	    maxQtde = parseInt( estoqueMax ) - parseInt( estoqueAtual );
	else if ( 'S' == tipo && !objGeral.empty( estoqueMin ) )
	    maxQtde = parseInt( estoqueAtual ) - parseInt( estoqueMin );
	
	if ( maxQtde != null && maxQtde <= 0 ) {
	    
	    dijit.byId( 'estoque_quantidade' ).set( 'value', 0 );
	    maxQtde = 0;
	    
	    objGeral.msgAlerta( 'O estoque atual não permite realizar esta operação.' );
	}
	
	if ( maxQtde != null )
	    dijit.byId( 'estoque_quantidade' ).constraints.max = maxQtde;
	
	dijit.byId( 'estoque_quantidade' ).focus();
	
	objGeral.setRequired( 'terceiro_id', ( 'E' == tipo ) );
	if( 'S' == tipo ) {
	    
	    with( dijit.byId( 'terceiro_id' ) ) {
		
		set( 'readOnly', true );
		set( 'value', '' );
	    }
	}
	
	return true;
    },
    
    
    ajustar: function()
    {
        if ( !almoxarifadoEstoque.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( 'Selecione o item para realizar o ajuste.' );
            return false;
        }
	
	if ( (/A/).exec( almoxarifadoEstoque.grid.getSelectedId() ) ) {
	    
	    objGeral.msgAlerta( 'Um ajuste não pode ser feito em cima de outro.' );
            return false;
	}
	    
        objGeral.createDialog( '/almoxarifado/estoque/ajuste/id/' + almoxarifadoEstoque.grid.getSelectedId(), 'Ajuste', dojo.hitch( this, 'tipoMovimentacao' ) );
        
        return true;
    }
});

var almoxarifadoEstoque = new modulo.almoxarifado.estoque();
