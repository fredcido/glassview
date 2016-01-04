dojo.provide( 'modulo.custom.TreeCheck' );

dojo.require( 'dojo.data.ItemFileReadStore' );
dojo.require('dijit.Tree');

dojo.declare( 'modulo.custom.ChkTree', dijit.Tree, 
{
	_clickTarget: null,

	_createTreeNode: function( args ) 
	{
	    return new modulo.custom._ChkTreeNode( args );
	},

	getIconClass: function( item, opened ) 
	{
	    if ( item.root || this.model.store.getValue( item, 'type' ) != 'child' )
		return this.inherited( arguments );

	    return 'icon-toolbar-tablelightning';
	},

	getLabelClass: function( item, opened ) 
	{
	    if ( item.root || this.model.store.getValue( item, 'type' ) != 'child' )
		return this.inherited( arguments );

	    return 'class';
	},

	onNodeChecked: function( node ) 
	{

	},

	onNodeUnchecked: function( node ) 
	{

	},

	onClick: function( item, node ) 
	{
	    if ( item.root || this.model.store.getValue( item, 'type' ) != 'child' ) {
		    this._onExpandoClick({ node: node });
	    } else {
		if ( !node._checkbox )
		    return;

		if ( this._clickTarget && this._clickTarget.nodeName != 'INPUT' )
		    node._checkbox.checked = !node._checkbox.checked;

		if ( node._checkbox.checked ) {
		    this.onNodeChecked( node );
		} else {
		    this.onNodeUnchecked( node );
		}
	    }
	},

	_onClick: function( node, evt ) 
	{
	    this._clickTarget = evt.target;

	    //If the target was a checkbox, ignore focusing the widget
	    if ( this._clickTarget && this._clickTarget.nodeName == 'INPUT' ) {

		var nodeWidget = dijit.getEnclosingWidget( this._clickTarget );	
		this.onClick( nodeWidget.item, nodeWidget );
		return;

	    }

	    return this.inherited( arguments );
	},
	
	refresh: function()
	{
	    this._itemNodeMap = {};
	    this.model.root = null;
	    if ( this.rootNode ) {
		this.rootNode.destroyRecursive();
	    }

	    this._load();
	}
	
});

dojo.declare('modulo.custom._ChkTreeNode', dijit._TreeNode, 
{
    postCreate: function() 
    {
        this._createCheckbox();
        this.inherited( arguments );
    },

    _createCheckbox: function() 
    {
	if ( this.item.root || this.tree.model.store.getValue( this.item, 'type' ) != 'child' )
            return true;
		
	this._checkbox = dojo.doc.createElement('input');
        this._checkbox.type = 'checkbox';
        this._checkbox.checked = this.tree.model.store.getValue( this.item, 'checked' );
        this._checkbox.id = this.tree.model.store.getValue( this.item, 'id' );
	
        dojo.place( this._checkbox, this.expandoNode, 'after' );
    },
    
    getId: function()
    {
	return this.tree.model.store.getValue( this.item, 'id' );
    },
    
    markCheck: function()
    {
	this._checkbox.checked = !this._checkbox.checked;
    }
});
