dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.categoria' );

dojo.declare( 'modulo.financeiro.categoria', [modulo.padrao.geral, modulo.padrao.grid],
{
    treeView: null,
    
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroCategoria.gridHeader = '#,'+
                                                objGeral.translate('Descrição')+','+
                                                objGeral.translate('Projeto')+','+
                                                objGeral.translate('Status');
	
        document.getElementById('gridFinanceiroCategoria').style.height= objGrid.gridHeight +"px";
        financeiroCategoria.grid = new dhtmlXGridObject('gridFinanceiroCategoria');
        financeiroCategoria.grid.setHeader( financeiroCategoria.gridHeader );
        financeiroCategoria.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter");
        financeiroCategoria.grid.setInitWidths( objGrid.idWidth + ",*,350,100");
        financeiroCategoria.grid.setColAlign("center,left,left,left");
        financeiroCategoria.grid.setColTypes("ro,ro,ro,ro");
        financeiroCategoria.grid.setColSorting("str,str,str,str");
        financeiroCategoria.grid.setSkin( objGrid.theme );
        financeiroCategoria.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroCategoriaGrid', true, 'divpagfinanceiroCategoria');
        financeiroCategoria.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}");
        financeiroCategoria.grid.attachEvent( 'onRowDblClicked', financeiroCategoria.edit );
        financeiroCategoria.grid.init();
        financeiroCategoria.grid.load( baseUrl + '/financeiro/categoria/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/categoria/form/', 
                objGeral.translate('Categoria')
            );
    },
    
    edit: function()
    {
        if ( !financeiroCategoria.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/categoria/edit/id/' + financeiroCategoria.grid.getSelectedId(), 
                objGeral.translate('Categoria')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroCategoria.grid] );
    },
    
    organizar: function()
    {
        var categoriaDialogTree = objGeral.createDialog( 
                                    '/financeiro/categoria/organizar/', 
                                    objGeral.translate( 'Organizar' )
                                );
    },
    
    carregarCategoriasProjeto: function()
    {
	var id = dijit.byId( 'projeto_id' ).get( 'value' );
	
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
	    url: baseUrl + '/financeiro/categoria/tree/id/' + id,
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
	
	dojo.byId( 'tree-categoria' ).appendChild( this.treeView.domNode );
	
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
	    url: baseUrl + '/financeiro/categoria/organizar-categoria/',
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
    }
});

var financeiroCategoria = new modulo.financeiro.categoria();