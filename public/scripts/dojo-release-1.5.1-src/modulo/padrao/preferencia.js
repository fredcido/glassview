dojo.require( 'modulo.padrao.geral' );

dojo.provide( 'modulo.padrao.preferencia' );

dojo.declare( 'modulo.padrao.preferencia' , [modulo.padrao.geral], 
{
    constructor: function(){},
    
    edit: function()
    {
        objGeral.createDialog( 
                '/preferencia/edit/', 
                objGeral.translate('Preferências')
            );
        return true;
    },
    
    atualizarGrid: function() 
    { 
        return true; 
    }
});

var defaultPreferencia = new modulo.padrao.preferencia();