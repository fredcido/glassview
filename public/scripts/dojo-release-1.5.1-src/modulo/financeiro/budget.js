dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.budget' );

dojo.declare( 'modulo.financeiro.budget', [modulo.padrao.geral, modulo.padrao.grid],
{
    projeto: null,
    
    budget: null,
    
    treeViewCategoria: null,
    
    gridBudgetLancamentos: null,
    
    mesesBudget: null,
    
    liberaEdicao: false,
    
    constructor: function()
    {
    },
    
    createCustomCellType: function()
    {
	var budgetClass = this;
	
	window.eXcell_currencyBudget = function( cell )
	{                                    
	    if ( cell ) {   
		
		this.cell = cell;
		this.grid = this.cell.parentNode.grid;
		
		this.cell.setAttribute( 'if-focused', 0 );
	    }
	    
	    this.setValue = function( val )
	    {		
		this.setCValue( val );                                     
	    }
	    
	    this.getValue = function()
	    {
		if ( !objGeral.empty( this.cell.getAttribute( 'if-focused' ) ) )
		    return 0;
		else
		    return this.cell.innerHTML;
	    }
	    
	    this.edit = function()
	    {
		if ( !budgetClass.liberaEdicao )
		    return false;
		
		var rowId = eval( '(' + this.cell.parentNode.idd + ')' );
		if ( !objGeral.empty( rowId.fn_tipo_lanc_agrupador ) )
		    return false;
		
		this.val = this.getValue();
		
		var self = this;
		var currencyField = new dijit.form.CurrencyTextBox( { 
					value: objGeral.toFloat( this.val ), 
					onClick: function( e )
					{
					    ( e || event ).cancelBubble = true;
					},
					onKeyPress: function( e )
					{
					    if ( e.keyCode == dojo.keys.ENTER )
						dijit.byId( 'projeto_id' ).focus();
					},
					onFocus: function()
					{
					    dijit.selectInputText( this.textbox );
					    
					    self.cell.setAttribute( 'if-focused', 1 );
					},
					onBlur: function()
					{
					    self.cell.setAttribute( 'if-focused', 0 );
					    self.detach();
					}
				    } );
		
		$( this.cell ).empty();
				
		this.cell.appendChild( currencyField.domNode );
		
		currencyField.focus();
		
		return true;
	    }
	    
	    this.detach = function()
	    {
		if ( !objGeral.empty( this.cell.getAttribute( 'if-focused' ) ) )
		    return false;
		
		var widgets = dijit.findWidgets( this.cell );
		if ( objGeral.empty( widgets ) )
		    return false;
		
		var currencyField = widgets[0];
				
		var value = currencyField.get( 'value' );
		var formattedValue = dojo.currency.format( parseFloat( value ), { currency: 'R$', locale: 'pt-br' } );
		
		currencyField.destroy();
		
		this.setValue( formattedValue );
				
		return this.val != value;
	    }
	}
	
	window.eXcell_currencyBudget.prototype = new eXcell;
    },
    
    initBudget: function()
    {
	// Cria botoes da tela
	financeiroBudget.initButtons();
	
	// Limpa dados
	financeiroBudget.clearData();
	
	// Cria o tipo customizado de celula da grid
	financeiroBudget.createCustomCellType();
    },
    
    clearData: function()
    {
	this.projeto = null;
	
	this.budget = null;
	
	this.mesesBudget = null;
	
	this.liberaEdicao = false;
	
	this.bloqueiaBotoesAno();
    },
    
    initButtons: function()
    {
	var btnBuscaCategoria = new dijit.form.Button(
	    {
		iconClass: 'icon-toolbar-zoom',
		showLabel: false,
		disabled: true,
		onClick: dojo.hitch( this, 'treeCategoria' ),
		id: 'btnBuscaCategoria',
		style: 'margin-top: 5px'
	    }
	);
	    
	dijit.byId( 'fn_categoria_descricao' ).domNode.parentNode.parentNode.appendChild( btnBuscaCategoria.domNode );
	
	var btnLastYear = new dijit.form.Button(
	    {
		iconClass: 'icon-toolbar-resultsetprevious',
		showLabel: false,
		disabled: true,
		id: 'btnLastYear',
		onClick: dojo.hitch( this, 'lastYearProject' ),
		style: 'margin-top: 5px'
	    }
	);
	
	var btnNextYear = new dijit.form.Button(
	    {
		iconClass: 'icon-toolbar-resultsetnext',
		showLabel: false,
		disabled: true,
		id: 'btnNextYear',
		onClick: dojo.hitch( this, 'nextYearProject' ),
		style: 'margin-top: 5px'
	    }
	);
	    
	dijit.byId( 'budget_ano' ).domNode.parentNode.parentNode.appendChild( btnLastYear.domNode );
	dijit.byId( 'budget_ano' ).domNode.parentNode.parentNode.appendChild( btnNextYear.domNode );
    },
    
    changeProjeto: function()
    {
	var projeto = dijit.byId( 'projeto_id' ).get( 'value' );
	
	if ( objGeral.empty( projeto ) ) {
	    
	    dijit.byId( 'fn_categoria_descricao' ).set( 'value', null );
	    dijit.byId( 'btnBuscaCategoria' ).setDisabled( true );
	    
	    this.clearData();
	    
	    return false;
	}
	
	var self = this;
	
	var obj = {
	    url: baseUrl + '/financeiro/budget/verifica-budget/id/' + projeto,
	    handle: 'json',
	    callback: function( response )
	    {
		dijit.byId( 'btnBuscaCategoria' ).setDisabled( false );
		
		dijit.byId( 'projeto_orcamento' ).set( 'value', parseFloat( response.projeto.projeto_orcamento ) );
		
		if ( !objGeral.empty( response.budget ) ) {
		 
		    dijit.byId( 'fn_budget_total' ).set( 'value', parseFloat( response.budget.fn_budget_total ) );
		    dojo.byId( 'fn_budget_id' ).value = response.budget.fn_budget_id;
		    self.budget = response.budget;
		}
		
		self.liberaEdicao = response.permissao;
		self.projeto = response.projeto;
				
		var dataInicial = dojo.date.stamp.fromISOString( self.projeto.projeto_inicio );
		
		dijit.byId( 'budget_ano' ).set( 'value', dataInicial.getFullYear() );
		
		self.liberaBotoesAno();
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    bloqueiaBotoesAno: function()
    {
	dijit.byId( 'btnLastYear' ).setDisabled( true );
	dijit.byId( 'btnNextYear' ).setDisabled( true );
    },
    
    liberaBotoesAno: function()
    {
	var dataInicial = dojo.date.stamp.fromISOString( this.projeto.projeto_inicio );
	var dataFinal = dojo.date.stamp.fromISOString( this.projeto.projeto_final );
	var ano	= dijit.byId( 'budget_ano' ).get( 'value' );
	
	dijit.byId( 'btnLastYear' ).setDisabled( !( dataInicial.getFullYear() < ano ) );
	dijit.byId( 'btnNextYear' ).setDisabled( !( dataFinal.getFullYear() > ano ) );
    },
    
    nextYearProject: function()
    {
	if ( objGeral.empty( this.projeto ) )
	    return false;
	
	var dataFinal = dojo.date.stamp.fromISOString( this.projeto.projeto_final );
	var ano	= dijit.byId( 'budget_ano' ).get( 'value' );
	
	if ( dataFinal.getFullYear() <= ano )
	    return false;
	
	dijit.byId( 'budget_ano' ).set( 'value', parseInt( ano ) + 1 );
	
	this.liberaBotoesAno();
	
	this.changeCategoria();
	
	return true;
    },
    
    lastYearProject: function()
    {
	if ( objGeral.empty( this.projeto ) )
	    return false;
	
	var dataInicio = dojo.date.stamp.fromISOString( this.projeto.projeto_inicio );
	var ano	= dijit.byId( 'budget_ano' ).get( 'value' );
	
	if ( dataInicio.getFullYear() >= ano )
	    return false;
	
	dijit.byId( 'budget_ano' ).set( 'value', parseInt( ano ) - 1 );
	
	this.liberaBotoesAno();
	
	this.changeCategoria();
	
	return true;
    },
    
    treeCategoria: function()
    {
	objGeral.createDialog( 
				'/financeiro/budget/categorias/', 
				objGeral.translate( 'Categorias' ),
				dojo.hitch( this, '_initTree' )
			    );
    },
    
    _initTree: function()
    {
	if ( !objGeral.empty( this.treeViewCategoria ) )
	    this.treeViewCategoria.destroyRecursive( true );
	
	var projeto = dijit.byId( 'projeto_id' ).get( 'value' );
	    
	objGeral.loading( true );
	
	var store = new dojo.data.ItemFileWriteStore({
	    url: baseUrl + '/financeiro/budget/tree-categorias/id/' + projeto,
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
	    query: {type: 'root'},
	    childrenAttrs: ['children']
	}, 'store' );
	
	this.treeViewCategoria = new modulo.custom.TreeMenu({ 
	    model: treeModel, 
	    showRoot: false, 
	    persist: false,
	    customIcons: false,
	    expandOnClick: false
	});
	    
	dojo.connect( this.treeViewCategoria, 'onClick', dojo.hitch( this, 'selectCategoria' ) );
	
	dojo.byId( 'tree-categoria' ).appendChild( this.treeViewCategoria.domNode );
	
	this.treeViewCategoria.startup();
    },
    
    selectCategoria: function( item )
    {
	var agrupador = this.treeViewCategoria.model.store.getValue( item, 'agrupador' );
	
	if ( !objGeral.empty( agrupador ) )
	    return false;
	
	var categoria_id = this.treeViewCategoria.model.store.getValue( item, 'id' );
	var path = this.treeViewCategoria.model.store.getValue( item, 'path' );
	
	dijit.byId( 'fn_categoria_descricao' ).set( 'value', path );
	dojo.byId( 'fn_categoria_id' ).value = categoria_id;
	
	objGeral.closeGenericDialog();
	
	return true;
    },
    
    changeCategoria: function()
    {
	var categoria = dojo.byId( 'fn_categoria_id' ).value;
	
	if ( objGeral.empty( categoria ) )
	    return false;
	
	var ano = dijit.byId( 'budget_ano' ).get( 'value' );
	    
	var self = this;
	
	var obj = {
	    url: baseUrl + '/financeiro/budget/lista-lancamentos/',
	    data: {
		projeto: self.projeto.projeto_id,
		ano: ano,
		projeto_inicio: self.projeto.projeto_inicio,
		projeto_final: self.projeto.projeto_final,
		categoria: categoria
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		self.initGridBudgetLancamentos( response );

		dijit.byId( 'fn_budget_total_categoria' ).set( 'value', parseFloat( response.total_categoria ) );
	    },
	    callbackError: function( response )
	    {
		console.log( response );
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    initGridBudgetLancamentos: function ( json )
    {	
	if ( !objGeral.empty( this.gridBudgetLancamentos ) )
	    this.gridBudgetLancamentos.destructor();
	
	this.mesesBudget = json.meses_keys;
	
	var header = [ objGeral.translate( 'Tipo de lançamento' ), objGeral.translate( 'Geral' ) ];
	var widthCols = [ 95 ];
	var colTypes = [ 'ro', 'currencyBudget' ];
	var alignCols = [ 'left', 'left' ];
	var sortingCols = [ 'str', 'currencyBudget' ];
	var footerCols = [ objGeral.translate( 'Total' ), '#cspan' ];
	
	var totalSize = 1010;
	
	for ( x in json.meses ) {
	    
	    header.push( objGeral.translate( json.meses[x] ) );
	    widthCols.push( 95 );
	    colTypes.push( 'currencyBudget' );
	    alignCols.push( 'left' );
	    sortingCols.push( 'na' );
	    footerCols.push ( '{#stat_total}' );
	    
	    totalSize -= 40;
	}
	
	widthCols.unshift( totalSize );
	               
        this.gridBudgetLancamentos = new dhtmlXGridObject( 'controle-budget' );
        this.gridBudgetLancamentos.setHeader( header.join( ',' ) );
        this.gridBudgetLancamentos.enableUndoRedo();
        this.gridBudgetLancamentos.enableEditTabOnly( true );
        this.gridBudgetLancamentos.setInitWidths( widthCols.join( ',' ) );
        this.gridBudgetLancamentos.setColAlign( alignCols.join( ',' ) );
        this.gridBudgetLancamentos.setColTypes( colTypes.join( ',' ) );
        this.gridBudgetLancamentos.setColSorting( sortingCols.join( ',' ) );
        this.gridBudgetLancamentos.setSkin( objGrid.theme );
        this.gridBudgetLancamentos.attachFooter( footerCols.join( ',' ) );
        this.gridBudgetLancamentos.init();
	
	this.gridBudgetLancamentos.parse( json.rows, 'json' );
	
	this.gridBudgetLancamentos.attachEvent( 'onCellChanged', dojo.hitch( this, 'changeCellValue' ) );
    },
    
    changeCellValue: function( id, index, newValue )
    {
	var rowId = eval( '(' + id + ')' );
	
	var actualCell = this.gridBudgetLancamentos.cells( id, index );
	if ( !objGeral.empty( actualCell.cell.getAttribute( 'handed-setted' ) ) ) {
	    
	    actualCell.cell.setAttribute( 'handed-setted', 1 );
	    return false;
	}
	
	// Se for a celula de replicacao
	if ( index == 1 ) {
	    
	    this.replicaValores( rowId, newValue );
	    
	} else {
	
	    var self = this;

	    var obj = {
		url: baseUrl + '/financeiro/budget/save/',
		data: {
		   mes: this.mesesBudget[ index - 2 ],
		   fn_tipo_lanc_id: rowId.fn_tipo_lanc_id,
		   valor_tipo_lanc: objGeral.toFloat( newValue )
		},
		handle: 'json',
		callback: function( response )
		{
		    if ( response.status ) {
			
			dijit.byId( 'formFinanceiroBudget' ).setValues( response );
			
			var totalGeral = dojo.currency.format( parseFloat( response.total_bugdet_geral ), { currency: 'R$', locale: 'pt-br' } );
			var objCell = self.gridBudgetLancamentos.cells( id, 1 );
			
			objCell.cell.setAttribute( 'handed-setted', 1 );
			objCell.setValue( totalGeral );
			objCell.cell.setAttribute( 'handed-setted', 0 );
			
			self.changeCategoria();
			
		    } else
			self.gridBudgetLancamentos.doUndo();

		},
		callbackError: function()
		{
		    self.gridBudgetLancamentos.doUndo();
		},
		form: dojo.byId( 'formFinanceiroBudget' )
	    }

	    objGeral.buscaAjax( obj );
	}
	
	return true;
    },
    
    replicaValores: function( rowId, value )
    {
	var self = this;

	var obj = {
	    url: baseUrl + '/financeiro/budget/replica-valores/',
	    data: {
		fn_tipo_lanc_id: rowId.fn_tipo_lanc_id,
		valor: objGeral.toFloat( value ),
		projeto_inicio: self.projeto.projeto_inicio,
		projeto_final: self.projeto.projeto_final
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		if ( response.status ) {
		  
		    dijit.byId( 'formFinanceiroBudget' ).setValues( response );
		    
		    self.changeCategoria();
		    
		} else
		    self.gridBudgetLancamentos.doUndo();

	    },
	    callbackError: function()
	    {
		self.gridBudgetLancamentos.doUndo();
	    },
	    form: dojo.byId( 'formFinanceiroBudget' )
	}

	objGeral.buscaAjax( obj );
    }
});

var financeiroBudget = new modulo.financeiro.budget();