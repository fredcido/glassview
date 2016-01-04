dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.tipolancamento' );

dojo.declare( 'modulo.financeiro.tipolancamento', [modulo.padrao.geral, modulo.padrao.grid],
{
    treeView: null,
    
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroTipoLancamento.gridHeader = '#,'+
                                                objGeral.translate('Código')+','+
                                                objGeral.translate('Descrição')+','+
                                                objGeral.translate('Projeto')+','+
                                                objGeral.translate('Categoria')+','+
                                                objGeral.translate('Status');
	
        document.getElementById('gridFinanceiroTipoLancamento').style.height= objGrid.gridHeight +"px";
        financeiroTipoLancamento.grid = new dhtmlXGridObject('gridFinanceiroTipoLancamento');
        financeiroTipoLancamento.grid.setHeader( financeiroTipoLancamento.gridHeader );
        financeiroTipoLancamento.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter");
        financeiroTipoLancamento.grid.setInitWidths( objGrid.idWidth + ",200,*,320,320,100");
        financeiroTipoLancamento.grid.setColAlign("center,left,left,left,left,left");
        financeiroTipoLancamento.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        financeiroTipoLancamento.grid.setColSorting("str,str,str,str,str,str");
        financeiroTipoLancamento.grid.setSkin( objGrid.theme );
        financeiroTipoLancamento.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroTipoLancamentoGrid', true, 'gridFinanceiroTipoLancamento');
        financeiroTipoLancamento.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroTipoLancamento.grid.attachEvent( 'onRowDblClicked', financeiroTipoLancamento.edit );
        financeiroTipoLancamento.grid.init();
        financeiroTipoLancamento.grid.load( baseUrl + '/financeiro/tipo-lancamento/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/tipo-lancamento/form/', 
                objGeral.translate('Tipo de Lançamento')
            );
    },
    
    edit: function()
    {
        if ( !financeiroTipoLancamento.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/tipo-lancamento/edit/id/' + financeiroTipoLancamento.grid.getSelectedId(), 
                objGeral.translate('Tipo de Lançamento'),
                dojo.hitch( this, 'setReadOnly' )
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroTipoLancamento.grid] );
    },
    
    organizar: function()
    {
        var tipoLancamentoDialogTree = objGeral.createDialog( 
                                    '/financeiro/tipo-lancamento/organizar/', 
                                    objGeral.translate( 'Organizar' )
                                );
    },
    
    buscaCategoriasProjeto: function()
    {
	var id = dijit.byId( 'projeto_id' ).get( 'value' );
	
	objGeral.changeFilteringSelect( 'fn_categoria_id', baseUrl + '/financeiro/tipo-lancamento/categorias-por-projeto/id/', id );
    },
    
    carregarTiposLancamento: function()
    {
	var id = dijit.byId( 'fn_categoria_id' ).get( 'value' );
	
	if ( objGeral.empty( id ) )
	    return false;
	
	this._initTree( id );
	
	return true;
    },
    
    _initTree: function( id )
    {
	if ( !objGeral.empty( this.treeView ) ) {
	    this.treeView.destroyRecursive( true );
	}
	
	objGeral.loading( true );
	
	var store = new dojo.data.ItemFileWriteStore({
	    url: baseUrl + '/financeiro/tipo-lancamento/tree/id/' + id,
	    hierarchical: true
	});
	
	store.fetch({
	    onComplete:
		function()
		{
		    objGeral.loading( false );
		}
	});

	var treeModel = new modulo.custom.ForestStoreModel({
	    store: store,
	    query: { type: 'root' },
	    childrenAttrs: ['children']
	}, 'store' );
	
	this.treeView = new modulo.custom.TreeMenu({ 
	    model: treeModel, 
	    showRoot: false, 
	    persist: false,
	    dragThreshold: 8,
	    betweenThreshold: 5,
	    dndController: 'dijit.tree.dndSource',
	    customIcons: false
	});
	    
	dojo.connect( this.treeView, '_onItemChildrenChange', dojo.hitch( this, 'changeOrder' ) );
	
	dojo.byId( 'tree-tipo-lancamento' ).appendChild( this.treeView.domNode );
	
	this.treeView.startup();
    },
    
    
    changeOrder: function( parent, children )
    {	
	var childrenMenu = [];
	
	dojo.forEach( children,
	    function( item )
	    {
		childrenMenu.push( item.id[0] );
	    }
	);
	    
	var self = this;
	
	var obj = {
	    url: baseUrl + '/financeiro/tipo-lancamento/organizar-tipo-lancamento/',
	    data: {
		pai: parent.root ? null : parent.id,
		'filhos[]': childrenMenu
	    },
	    handle: 'json',
	    noload: true,
	    callback: function( response )
	    {
		if ( !response.status )
		    objGeral.msgErro( response.message );
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    setReadOnly: function(){
        
        this.agrupador = dijit.byId('fn_tipo_lanc_agrupador').attr('value');
        
        if( this.agrupador == 1 ){
            
            dijit.byId('fn_tipo_lanc_cod').attr( 'readOnly', true );
            dijit.byId('fn_tipo_lanc_cod').attr( 'value', '' );
        }else{
            
            dijit.byId('fn_tipo_lanc_cod').attr( 'readOnly', false );
        }
    }
});

var financeiroTipoLancamento = new modulo.financeiro.tipolancamento();