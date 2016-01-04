dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid'  );

dojo.provide( 'modulo.gestao.timeline' );

dojo.declare( 'modulo.gestao.timeline' , [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function() { },

    load: 0,
    
    lightbox: 0,
    
    /**
     * 
     */
    initScheduler: function()
    {
        objGeral.loading( true );
        
        scheduler.config.icons_edit   = ['icon_cancel'];  // ['icon_save','icon_cancel'] 
        scheduler.config.icons_select = ['icon_details']; // ['icon_details','icon_edit','icon_delete']
        scheduler.config.multi_day    = true;
        scheduler.config.mark_now     = true;
        //scheduler.config.drag_resize  = true;
        scheduler.config.xml_date     = "%Y-%m-%d %H:%i";
        scheduler.config.details_on_dblclick = true;
        scheduler.config.details_on_create   = true;
        
        scheduler.attachEvent( 
        	'onClick', 
        	function ( ev_id, ev )
        	{
	            gestaoTimeline.idEventSel = ev_id;
	        }
		);

        scheduler.attachEvent( 
        	'onEventChanged', 
        	function ( ev_id, ev )
			{
				if ( confirm(objGeral.translate('Deseja salvar alterações do evento?')) )
                	gestaoTimeline.changeEventSave( ev_id , ev );
            	else
                	gestaoTimeline.atualizarGrid();
        	}
        );  

		/**
		 * @param start
		 * @param end
		 * @param event
		 */        
        scheduler.templates.tooltip_text = function( start, end, event ) 
        {
            return  event.text
                    + "<br/><b>" + objGeral.translate('Início') + ":</b> "
                    + scheduler.templates.tooltip_date_format( start )
                    + "<br/><b>" + objGeral.translate('Fim') + ":</b> " 
                    + scheduler.templates.tooltip_date_format( end );

        }
        
        scheduler.templates.tooltip_date_format = scheduler.date.date_to_str( '%d/%m/%Y %H:%i' );
        
        scheduler.init( 'scheduler_here', null, 'week' );
        
        /**
         * @param ev_id
         */
        scheduler.showLightbox = function ( ev_id )
        {
            gestaoTimeline.lightbox = ev_id;
            
            var ev = scheduler.getEvent( ev_id );

            gestaoTimeline.novo( ev );
            
        }
	
	gestaoTimeline.initFilterProjeto();
        
        gestaoTimeline.atualizarGrid();
        
        objGeral.loading( false );

    },
    
    /**
     * @param ev
     */
    novo: function ( ev ) 
    {
    	this.url   = '/gestao/timeline/';
    	this.title = objGeral.translate('Timeline');
        
    	if ( ev ) {
                
                this.url += ev.data ? ('edit/id/' + ev.data.timeline_id) : 'form';
                
                this.dialog = objGeral.createDialog( 
                    this.url, 
                    this.title, 
                    function() 
                    {
                        if ( !ev.data ) {
                            
                            dijit.byId('dt_inicio').set( 'value', ev.start_date );
                            dijit.byId('hr_inicio').set( 'value', ev.start_date );
                            dijit.byId('dt_fim').set( 'value', ev.end_date );
                            dijit.byId('hr_fim').set( 'value', ev.end_date );
                        }
                    }
				);
    		
            } else  {
                
                this.url   = '/gestao/timeline/form';
                this.dialog = objGeral.createDialog( '/gestao/timeline/form', this.title );
                
            }
            
            dojo.connect(
                gestaoTimeline.dialog,
                'hide',
                function ()
                {
                    gestaoTimeline.atualizarGrid();
                }
            );
    },
    
    /**
     * 
     */
    atualizarGrid: function()
    {
        if ( this.load == 0 ) {
	    
	    var projeto = dijit.byId( 'projeto_filtro_id' ).get( 'value' );
            
            if ( gestaoTimeline.lightbox != 0 ) {
                scheduler.startLightbox( gestaoTimeline.lightbox , null );
                scheduler.endLightbox( false , null);
                gestaoTimeline.lightbox = 0;
            }
                
            objGeral.loading( true );
            
            gestaoTimeline.load = 1;
            scheduler.clearAll();  
            scheduler.load( 
            	baseUrl + '/gestao/timeline/list/id/' + projeto, 
            	'json', 
            	dojo.hitch( gestaoTimeline, 'fimAtualizarGrid' )  
            );
            
        }
    },
    
    /**
     * 
     */
    edit: function()
    {
        if ( !this.idEventSel ) {
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
        
    	this.title = objGeral.translate('Timeline');
    	this.url   = '/gestao/timeline/edit/id/' + this.idEventSel;
    	
        var dialog = objGeral.createDialog( this.url , this.title );
        
        dojo.connect(
            dialog,
            'hide',
            function ()
            {
                gestaoTimeline.atualizarGrid();
            }
        );
            
        return false;

    },

	/**
	 * 
	 */    
    fimAtualizarGrid: function()
    {
        gestaoTimeline.load = 0;
        objGeral.loading( false );
    },
    
    /**
     * @param id
     */
    getCargaHoraria: function( id )
    {
    	if ( id ) {
    	
	    	var obj = {
	    		url: baseUrl + '/gestao/timeline/carga-horaria',
	    		data: {id: id},
	    		handle: 'json',
	    		callback: function( response )
	    		{
	    			dijit.byId('timeline_carga_horaria').set('value', response.funcionario_carga_diaria);
	    		}
	    	};
	    	
	    	objGeral.buscaAjax( obj );
	    	
	    }
    },
    
    /**
     * 
     */
    changeFuncionario: function()
    {
    	var campos = ['dt_inicio', 'dt_fim'];
    	
    	var projeto_id = parseInt( dijit.byId('projeto_id').get('value') );
    	var funcionario_id = parseInt( dijit.byId('funcionario_id').get('value') );
    	
    	if ( projeto_id && funcionario_id ) {
    	
			var obj = {
				url: baseUrl + '/gestao/timeline/periodo',
				data: {
					funcionario_id: funcionario_id,
					projeto_id: projeto_id
				},
				handle: 'json',
				callback: function( response )
				{
					for ( i in campos )
						dijit.byId( campos[i] ).set( 'readOnly', false );
						
					dojo.mixin(
						dijit.byId('dt_inicio').constraints, 
						{
							min: dojo.date.stamp.fromISOString( response.dt_inicio ),
							max: dojo.date.stamp.fromISOString( response.dt_fim )
						}
					);
					
					dojo.mixin(
						dijit.byId('dt_fim').constraints, 
						{
							min: dojo.date.stamp.fromISOString( response.dt_inicio ),
							max: dojo.date.stamp.fromISOString( response.dt_fim )
						}
					);
				}
			};
			
			objGeral.buscaAjax( obj );
			
			dojo.connect(
				dijit.byId('dt_inicio'),
				'onChange',
				function( value )
				{
					dojo.mixin(
					    dijit.byId('dt_fim').constraints, 
					    {
					    	min: value
				    	}
					);
				}
			);
			
			dojo.connect(
				dijit.byId('dt_fim'),
				'onChange',
				function( value )
				{
					dojo.mixin(
						dijit.byId('dt_inicio').constraints,
						{
							max: value
						}
					)
				}
			);
			
		} else {
			
			for ( i in campos ) {
				dijit.byId( campos[i] ).set( 'readOnly', true );
				dijit.byId( campos[i] ).constraints = {};
			}
				
		}
    },
    
    /**
     * Exibe dialog para calcular custo do funcionario no projeto
     */
    lancamento: function()
    {
    	var dialogCustoLancamento = objGeral.createDialog( 
			'/gestao/timeline/lancamento/', 
			objGeral.translate('Lançamento'),
			gestaoTimeline.initGridLancamento
		);
    },
    
    /**
     * 
     */
    setConstraintDataIniFimLanc: function()
    {
        var func_id = dijit.byId('funcionario_id').get('value'); 
        
        dijit.byId('lanc_dt_inicio').set( 'value', '' );
        dijit.byId('lanc_dt_fim').set( 'value', '' );
        
        if ( objGeral.empty(func_id) ) {
            
            dijit.byId('lanc_dt_inicio').set( 'readOnly', true );
            dijit.byId('lanc_dt_fim').set( 'readOnly', true );
            
        } else {

        	dijit.byId('lanc_dt_inicio').set( 'readOnly', false );
            dijit.byId('lanc_dt_fim').set( 'readOnly', false );
        	
        	return false;
        	
            var obj = {
                    url: baseUrl + '/gestao/timeline/maxmimatividades',
                    data: {funcionario_id: func_id},
                    handle: 'json',
                    error: function()
                    {
                        objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                    },
                    callback: function( response )
                    {
                        if ( response.result ) {
                            
                            dijit.byId('lanc_dt_inicio').attr('readOnly',false);
                            dijit.byId('lanc_dt_fim').attr('readOnly',false);
                            dojo.mixin(
                                dijit.byId('lanc_dt_inicio').constraints, 
                                {
                                        min: dojo.date.stamp.fromISOString(response.min),
                                        max: dojo.date.stamp.fromISOString(response.max)
                                }
							);
                            dojo.mixin(
                                dijit.byId('lanc_dt_fim').constraints, 
                                {
                                        min: dojo.date.stamp.fromISOString(response.min),
                                        max: dojo.date.stamp.fromISOString(response.max)
                                }
							);

                        } else {
                            
                            dijit.byId('lanc_dt_inicio').set( 'readOnly', false );
                            dijit.byId('lanc_dt_fim').set( 'readOnly', false );
                            
                            objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                            
                        }
                    }
            };
            
            objGeral.buscaAjax( obj );

        }
        
    },
    
    /**
     * 
     */
    setConstraintMinLanc: function()
    {
        var data = dijit.byId('lanc_dt_inicio').get('value');
        
        if ( data ) {
            dojo.mixin(
                dijit.byId('lanc_dt_fim').constraints, 
                {
                    min: data
                }
            );
        }
    },
    
    /**
     * 
     */
    setConstraintMaxLanc: function()
    {
        var data = dijit.byId('lanc_dt_fim').get('value');
        
        if ( data ) {
            dojo.mixin(
                dijit.byId('lanc_dt_inicio').constraints, 
                {
                    max: data
                }
            );
        }
    },
    
    /**
     * 
     */
    listaLancamentos: function()
    {
    	var form = dijit.byId('form-gestao-timeline-lancamento'); 
    	
    	if ( form.isValid() ) {
         
    		gestaoAtividade.grid.clearAll();
    		
            var dataPost = {
                funcionario_id: dijit.byId('funcionario_id').get('value'),
                dt_inicio:      objGeral.formateDate( dijit.byId('lanc_dt_inicio').get('value') ),
                dt_fim:         objGeral.formateDate( dijit.byId('lanc_dt_fim').get('value') )
            };

            var obj = {
                url: baseUrl + '/gestao/timeline/lista-lancamentos',
                data:  dataPost,
                handle: 'json',
                error: function()
                {
                    objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                },
                callback: function( response )
                {
                	gestaoAtividade.grid.parse( response , "json");
                }
            };
            
            objGeral.buscaAjax( obj );

        }
        
    },

	/**
	 * 
	 */    
    initGridLancamento: function()
    {
        objGeral.loading( true );
        
        gestaoAtividade.gridHeader = objGeral.translate('Projeto') + ',' +
                                     objGeral.translate('Atividade') + ',' +
                                     objGeral.translate('Horas Trabalhadas') + ',#cspan,' +
                                     objGeral.translate('Salário');
                                 
        document.getElementById( 'gridGestaoTimelineLancamento' ).style.height= objGrid.gridHeight +"px";
        gestaoAtividade.grid = new dhtmlXGridObject('gridGestaoTimelineLancamento');
        gestaoAtividade.grid.setHeader( gestaoAtividade.gridHeader );
        gestaoAtividade.grid.attachHeader("#text_filter,#text_filter,#text_filter,,#text_filter");
        gestaoAtividade.grid.setInitWidths( "*,100,100,100,100");
        gestaoAtividade.grid.setColAlign("left,left,left,left,left");
        gestaoAtividade.grid.setColTypes("ro,ro,ro,ro,ro");
        gestaoAtividade.grid.setColSorting("str,str,str,str,str");
        gestaoAtividade.grid.setSkin( objGrid.theme );
        gestaoAtividade.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagGestaoTimelineLancamentoGrid', true, 'divpagGestaoTimelineLancamentoGrid');
        gestaoAtividade.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}");
        gestaoAtividade.grid.init();
        
        objGeral.loading( false );
    },
    
    /**
     * Exibe dialog para calcular custo do funcionario no projeto
     */
    custo: function()
    {
    	objGeral.createDialog( 
			'/gestao/timeline/custo/', 
			objGeral.translate('Custo') 
		);
    },
    
    /**
     * 
     */
    calcularCusto: function()
    {
    	var form = dijit.byId('form-gestao-timeline-custo'); 
    	
    	if ( form.isValid() ) {
    		
    		var obj = {
    			url: baseUrl + '/gestao/timeline/calcular-custo',
    			data: {
    				funcionario_id: dijit.byId('funcionario_id').get('value'),
    				qtd_dias: dojo.date.difference(
						dijit.byId('dt_inicio').get('value'),
						dijit.byId('dt_fim').get('value'),
						'day'
					)
    			},
    			handle: 'json',
    			callback: function( response )
    			{
    				dijit.byId('custo').set('value', response.custo);
    				dijit.byId('carga_horaria').set('value', response.carga_horaria);
    			} 
    		};
    		
    		objGeral.buscaAjax( obj );
    		
    	} else {
            
            objGeral.msgErro( objGeral.translate('Informe os dados corretamente!') );
            
        }
    },
    
    /**
     * Carrega os funcionarios de acordo com o projeto
     * 
     * @param projeto_id
     */
    loadFuncionario: function( projeto_id )
    {
    	if ( projeto_id ) {
    		
    		objGeral.changeFilteringSelect( 
				'funcionario_id', 
				baseUrl + '/gestao/timeline/funcionario/projeto_id/', 
				projeto_id 
			);
    		
    	} else {
    		
    		dijit.byId('funcionario_id').set('value', '0');
    		dijit.byId('funcionario_id').set('disabled', true);
    		
    	}
    	
    },
    
    /**
     * 
 	 * @param {Object} ev_id
 	 * @param {Object} ev
     */
    changeEventSave: function ( ev_id, ev )
    {
    	if ( ev_id ) {

            var dataPost = {
                timeline_id: ev_id,
                dt_inicio:   objGeral.formateDate( ev.start_date ),
                hr_inicio:   objGeral.formateDate( ev.start_date , 'h:m:s' ),
                dt_fim:      objGeral.formateDate( ev.end_date ),
                hr_fim:      objGeral.formateDate( ev.end_date , 'h:m:s' )
            };
    	
	    	var obj = {
	    		url: baseUrl + '/gestao/timeline/change-event-save',
	    		data: dataPost,
	    		handle: 'json',
                error: function()
                {
                    objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                },
	    		callback: function( response )
	    		{
                    if ( response.result ) {
                        objGeral.msgSucesso( objGeral.translate('Operação realizada com sucesso.') );
                    } else {
                        objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                    }
	    		}
	    	};
	    	
	    	objGeral.buscaAjax( obj );
	    	
	    }
    },
    
    initFilterProjeto: function()
    {
	objGeral.loading( true );
	
	var newRead = new dojo.data.ItemFileReadStore( { url: baseUrl + '/gestao/timeline/projetos/' } );
	newRead.fetch( { onComplete: function() { objGeral.loading( false ); } } );
	
	var filtering = new dijit.form.FilteringSelect(
				{
				    store: newRead,
				    id:	'projeto_filtro_id',
				    onChange: function()
				    {
					gestaoTimeline.atualizarGrid();
				    }
				}
			    );
	
	$( dijit.byId( 'buttonlancamentotimeline' ).domNode ).after( $( filtering.domNode ) );
    }
    
});

var gestaoTimeline = new modulo.gestao.timeline();