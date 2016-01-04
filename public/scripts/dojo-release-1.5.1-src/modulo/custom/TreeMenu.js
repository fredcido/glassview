dojo.provide( 'modulo.custom.TreeMenu' );

dojo.declare( 'modulo.custom.TreeMenu', [dijit.Tree], 
{
    customIcons: true,
    
    expandOnClick: true,
    
    onClick: function( item, node ) 
    {
	if ( this.expandOnClick && ( item.root || !objGeral.empty( this.model.store.getValue( item, 'children' ) ) ) )
	    this._onExpandoClick({ node: node });
	
	this.inherited( arguments );
    },
    
    getIconClass: function( item )
    {
	if ( this.customIcons )
	    return objGeral.empty( item.icone ) ? 'icon-toolbar-application' : item.icone;
	else
	    return this.inherited( arguments );
    }
});