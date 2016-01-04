dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.menu' );

dojo.declare( 'modulo.master.menu', [modulo.padrao.geral, modulo.padrao.grid],
{
    treeView: null,
    
    menuChanged: false,
    
    constructor: function()
    {  
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        masterMenu.gridHeader = '#,'+
                                objGeral.translate('Label')+','+
                                objGeral.translate('Menu Pai')+','+
                                objGeral.translate('Tela')+','+
                                objGeral.translate('Tipo');
                            
        document.getElementById( 'gridMasterMenu' ).style.height= objGrid.gridHeight +"px";
        masterMenu.grid = new dhtmlXGridObject( 'gridMasterMenu' );
        masterMenu.grid.setHeader( masterMenu.gridHeader );
        masterMenu.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#select_filter");
        masterMenu.grid.setInitWidths( objGrid.idWidth + ",*,220,220,170");
        masterMenu.grid.setColAlign("center,left,left,left,left");
        masterMenu.grid.setColTypes("ro,ro,ro,ro,ro");
        masterMenu.grid.setColSorting("str,str,str,str,str");
        masterMenu.grid.setSkin( objGrid.theme );
        masterMenu.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterMenuGrid', true, 'divpagMasterMenu');
        masterMenu.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}" );
        masterMenu.grid.attachEvent( 'onRowDblClicked', masterMenu.edit );
        masterMenu.grid.init();
        masterMenu.grid.load( baseUrl + '/master/menu/list/', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/menu/form/', 
                objGeral.translate('Menu')
            );
    },
    
    edit: function()
    {
        if ( !masterMenu.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/master/menu/edit/id/' + masterMenu.grid.getSelectedId(), 
                    objGeral.translate('Menu')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterMenu.grid] );
    },
    
    organizar: function()
    {
        var menuDialogTree = objGeral.createDialog( 
                                    '/master/menu/organizar/', 
                                    objGeral.translate('Organizar'), 
                                    dojo.hitch( this, 'loadTreeMenu')
                                );
	
	dojo.connect( 
                menuDialogTree, 
                'onHide', 
                dojo.hitch( this, 'reloadMenu' )
            );
    },
    
    reloadMenu: function()
    {
	if ( !this.menuChanged || !confirm( objGeral.translate('Deseja recarregar o menu agora?') ) )
	    return false;
	
	history.go(0);
	
	return true;
    },
    
    loadTreeMenu: function()
    {
	objGeral.loading( true );
	
	var store = new dojo.data.ItemFileWriteStore({
	    url: baseUrl + '/master/menu/tree/',
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
	    dndController: 'dijit.tree.dndSource'
	});
	    
	dojo.connect( this.treeView, '_onItemChildrenChange', dojo.hitch( this, 'changeOrder' ) );
	
	dojo.byId( 'tree-menu' ).appendChild( this.treeView.domNode );
	
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
	    url: baseUrl + '/master/menu/organizar-menu/',
	    data: {
		pai: parent.root ? null : parent.id,
		'filhos[]': childrenMenu
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		if ( !response.status )
		    objGeral.msgErro( response.message );
		else
		    self.menuChanged = true;
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    }
});

var masterMenu = new modulo.master.menu();