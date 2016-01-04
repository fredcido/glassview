dojo.require( 'modulo.padrao.geral' );
dojo.provide( 'modulo.padrao.grid'  );

dojo.declare( 'modulo.padrao.grid', [modulo.padrao.geral],
{ 
    constructor: function(){},
    
    gridHeight: 60,
    
    // Quantidade de  registros por aula
    pagResults: 30,

    // Quantidade de paginas visiveis
    pagIndexes: 10,

    // label para Total do rodape
    tituloTotal: objGeral.translate('Total:'),

    // Largura da coluna de ID
    idWidth: 50,
    
    // Thema da GRID
    theme: "dhx_skyblue"
});

var objGrid  = new modulo.padrao.grid();