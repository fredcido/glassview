dojo.provide( 'modulo.custom.ForestStoreModel' );

dojo.declare( 'modulo.custom.ForestStoreModel', [dijit.tree.ForestStoreModel], 
{
    onAddToRoot: function( item ) 
    {
	this.store.setValue( item, 'type', 'root' );
	this._requeryTop();
    },
    
    onLeaveRoot: function( item ) 
    {
	this.store.setValue( item, 'type', 'child' );
	this._requeryTop();
    },
    
    pasteItem: function(/*Item*/ childItem, /*Item*/ oldParentItem, /*Item*/ newParentItem, /*Boolean*/ bCopy, /*int?*/ insertIndex)
    {
	if ( oldParentItem == this.root && newParentItem == this.root ) {
	    
	    var root = this.root;
	    
	    dojo.forEach( this.childrenAttrs, function( attr ) {
		    if ( !bCopy ) {
			    var values = dojo.filter( root.children, function( x ) {
				    return x != childItem;
			    });
			    
			root.children = values;
		    }
		    parentAttr = attr;
	    });
	    
	    if ( typeof insertIndex == "number" )	
		root.children.splice( insertIndex, 0, childItem );
	    else
		root.children.push( childItem );
	    
	    this.onChildrenChange( this.root, root.children );
	    
	} else {
	    // call super
	    this.inherited( arguments );
	}
    }
});