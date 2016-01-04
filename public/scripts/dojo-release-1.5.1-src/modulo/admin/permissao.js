dojo.require( 'modulo.padrao.geral');
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.permissao' );

dojo.declare( 'modulo.admin.permissao', [modulo.padrao.geral, modulo.padrao.grid],
{
    treeView: null,
    
    constructor: function()
    {  
    },

    carregaPermissoes: function()
    {
	var perfil_id = dijit.byId( 'perfil_id' ).get( 'value' );
	
	if ( objGeral.empty( perfil_id ) )
	    return false;
	
	this._initTree( perfil_id );
	
	return true;
    },
    
    
    _initTree: function( perfil_id )
    {    
	    if ( !objGeral.empty( this.treeView ) ) {
		this.treeView.destroyRecursive( true );
	    }

	    objGeral.loading( true );

	    var store = new dojo.data.ItemFileReadStore({
		url: baseUrl + '/admin/permissao/itens/id/' + perfil_id,
		hierarchical: true
	    });

	    store.fetch({
		onComplete: function()
		{
		    objGeral.loading( false );
		}
	    });

	    var treeModel = new dijit.tree.ForestStoreModel({
		store: store,
		query: { type: 'root' },
		childrenAttrs: ['children']
	    }, 'store' );

	    this.treeView = new modulo.custom.ChkTree({ model: treeModel, showRoot: false, persist: false });

	    dojo.byId( 'tree-permissoes' ).appendChild( this.treeView.domNode );

	    dojo.connect( this.treeView, 'onNodeUnchecked', dojo.hitch( this, 'removePermissao' ) );
	    dojo.connect( this.treeView, 'onNodeChecked', dojo.hitch( this, 'addPermissao' ) );

	    this.treeView.startup();
    },
    
    removePermissao: function( node )
    {
	var id = node.getId();
	var perfil_id = dijit.byId( 'perfil_id' ).get( 'value' );
	
	var obj = {
	    url: baseUrl + '/admin/permissao/delete/',
	    handle: 'json',
	    data: {
		acao_id: id,
		perfil_id: perfil_id
	    },
	    noload: true,
	    callback: function( response )
	    {
		if ( !response.status ) {
		    
		    node.markCheck();
		    objGeral.msgErro( response.message );
		}
	    }
	}
	
	objGeral.buscaAjax( obj );
    },
    
    addPermissao: function( node )
    {
	var id = node.getId();
	var perfil_id = dijit.byId( 'perfil_id' ).get( 'value' );
	
	var obj = {
	    url: baseUrl + '/admin/permissao/adicionar/',
	    data: {
		acao_id: id,
		perfil_id: perfil_id
	    },
	    handle: 'json',
	    noload: true,
	    callback: function( response )
	    {
		if ( !response.status ) {
		    
		    node.markCheck();
		    objGeral.msgErro( response.message );
		}
	    }
	}
	
	objGeral.buscaAjax( obj );
    }
});

var adminPermissao = new modulo.admin.permissao();