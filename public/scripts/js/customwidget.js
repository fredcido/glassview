dojo.ready(
    function()
    {
        // Arruma validacao da Filtering Select
        (function(){
                if ( dijit.form.FilteringSelect ) {
                    var fs = dijit.form.FilteringSelect.prototype;

                    var protOld = fs.isValid;

                    fs.isValid = function()
                    {
                        if ( !protOld.call(this, arguments) ) {
                            return false;
                        } else {

                            if ( this.required && '' === this.value ) {
                                return false;
                            }
                        }

                        return true;
                    };
                }
        })();

	// Arruma validacao da Select
        (function(){
                if ( dijit.form.Select ) {
                    var fs = dijit.form.Select.prototype;

                    var protOld = fs.isValid;

                    fs.isValid = function()
                    {
                        if ( !protOld.call(this, arguments) ) {
                            return false;
                        } else {

                            if ( this.required && '0' === this.value ) {
                                return false;
                            }
                        }

                        return true;
                    };
                }
        })();
	
	
	// Forca locale de todos os Currency
        (function(){
                if ( dijit.form.CurrencyTextBox ) {
                    var ct = dijit.form.CurrencyTextBox.prototype;

                    ct.lang = 'pt-br';
		    
		    ct.selectOnClick = true;
		    
		    ct.currency = 'R$';
                }
        })();

        // Arruma validacao da DateTextBox
        (function(){
                if ( dijit.form.DateTextBox ) {
                    var dt = dijit.form.DateTextBox.prototype;

                    var setValue = dt._setValueAttr;
                
                    dt._setValueAttr = function()
                    {
                        if ( !objGeral.empty( arguments[0] ) && typeof arguments[0] == 'string' ) {
                            if ( null != ( dateObj = dojo.date.stamp.fromISOString( arguments[0] ) ) )
                                arguments[0] = dateObj;
                            else
                                 arguments[0] = null;
                        }

                        setValue.apply(this, arguments);

                        return true;
                    };
                }
        })();

        // Arruma validacao da TimeTextBox
        (function(){
                if ( dijit.form.TimeTextBox ) {
                    var tt = dijit.form.TimeTextBox.prototype;

                    var setValue = tt._setValueAttr;

                    tt._setValueAttr = function()
                    {
                        if ( !objGeral.empty( arguments[0] ) && typeof arguments[0] == 'string' ) {
                            arguments[0] = 'T' + arguments[0];
                            if ( null != ( dateObj = dojo.date.stamp.fromISOString( arguments[0] ) ) )
                                arguments[0] = dateObj;
                            else
                                 arguments[0] = null;
                        }

                        setValue.apply(this, arguments);

                        return true;
                    };
                }
        })();

        // Arruma validacao da MultiSelect
        (function(){
                if ( dijit.form.MultiSelect ) {
                    var ms = dijit.form.MultiSelect.prototype;

                    ms.required = false;

                    ms.validate = function()
                    {
                        return this.isValid();
                    }

                    ms.isValid = function()
                    {
                        if ( this.required && objGeral.empty( this.getSelected() ) ) {
                            dojo.addClass( this.domNode, 'dijitTextBoxError');
                            return false;
                        } else {
                            dojo.removeClass( this.domNode, 'dijitTextBoxError');
                            return true;
                        }
                    };

                    ms.clear = function()
                    {
                        // Limpa todas as options da MultiSelect
                        dojo.query('option', this.domNode).forEach(
                            function( op )
                            {
                                dojo.destroy( op )
                            }
                        );
                    };

                    ms.populate = function( json )
                    {
                        this.clear();
                        var self = this;
                        dojo.forEach( json,
                            function ( item, index )
                            {
                                if ( item.id == undefined ) {
                                    console.error('Atributo ID não identificado.');
                                    return false;
                                }

                                item.value = item.id;

                                item.innerHTML = item.name == undefined ?
                                                   'Item ' + index :
                                                   item.name;

                                op = dojo.create( 'option', item, self.domNode );

                                return true;
                            }
                        );
                    }
                }
        })();

        // Arruma validacao da SimpleTextArea
        (function(){
                if ( dijit.form.SimpleTextarea ) {
                    var sta = dijit.form.SimpleTextarea.prototype;

                    sta.required = false;
                    sta.progressObserver = false;
                    sta.progressContainer = null;

                    sta.validate = function()
                    {
                        var state;
                        var retorno;
			
                        if ( !this.isValid() ) {
                            state = 'Error';
                            retorno = false;
                        } else {
                            state = null;
                            retorno = true;
                        }

                        this.set('state', state);

                        this.textbox.blur();

                        return retorno;
                    },
		    
		    sta.isValid = function()
		    {
			if ( this.required && objGeral.empty( this.value ) )
                            return false;
                        else
                            return true;
		    },

                    sta.buildProgressObserver = function()
                    {
                        if ( !this.progressObserver || !this.maxLength )
                            return false;

                        var idProgress = this.id + '-progress';

                        dojo.require('dijit.ProgressBar');
                        this.progressContainer = new dijit.ProgressBar(
                            {
                                 id: idProgress,
                                 maximum: this.maxLength,
                                 report: function()
                                 {
                                     return dojo.string.substitute("${0} do máximo de ${1} caracteres", [this.progress, this.maximum]);
                                 }
                            }
                        );

                        var selfText = this;
                        var updateProgress = function()
                                              {
                                                selfText.progressContainer.update(
                                                    {
                                                        progress: selfText.get('value').length
                                                    }
                                                )
                                              };

                        dojo.connect( this, 'onKeyDown', updateProgress);
                        dojo.connect( this, 'onChange', updateProgress);

                        selfText.domNode.parentNode.appendChild( this.progressContainer.domNode );

                        objGeral.centerDialog();

                        return true;
                    }

                    var pt = sta.postMixInProperties;
                    sta.postMixInProperties = function()
                    {
                        pt.call( this, arguments );

                        var oldOnBlur = this.onBlur;
                        this.onBlur = function()
                        {
                            oldOnBlur.call( this, arguments );
                            this.validate();
                        }
                    }

                    sta.startup = function()
                    {
                        this.buildProgressObserver();
                    }
                }
        })();

        // Editor
        (function(){
                if ( dijit.Editor ) {
                    var ed = dijit.Editor.prototype;

                    var protOld = ed.onNormalizedDisplayChanged;

                    ed.onNormalizedDisplayChanged = function()
                    {
                        var id = this.get('id').replace('-Editor', '' );
                        dojo.byId( id ).value = this.get('value');
                        protOld.call( this, arguments );
                    }
                }
        })();

        // form
        (function(){
                if ( dijit.form.Form ) {

                    var form = dijit.form.Form.prototype;

                    var vl = form.validate;
                    form.validate = function()
                    {
                        if ( vl.call(this, arguments)  ) {
                            return true;
                        } else {

                            var children = this.getDescendants();

                            dojo.some( children,
                                function( item )
                                {
                                    if ( item.declaredClass && 
                                         item.declaredClass == 'dijit.layout.TabContainer' ) {

                                         dojo.some( item.getChildren(),
                                            function( pane )
                                            {
                                                var retorno = dojo.some( pane.getDescendants(),
                                                                    function( dj )
                                                                    {
                                                                        if ( dj.isValid && !dj.isValid() ) {

                                                                            item.selectChild( pane );
                                                                            dj.validate();
                                                                            dj.focus();

                                                                            return true;
                                                                        }

                                                                        return false;
                                                                    }
                                                                );

                                                return retorno;
                                            }
                                        );
                                    }
                                }
                            );

                            return false;
                        }
                    }

                    var sv = form.setValues;
                    form.setValues = function( obj )
                    {
                        var map = [];
                        dojo.forEach( this.getDescendants(),
                        function( widget )
                        {
                                if ( !widget.name ){return;}
                                map.push( widget.name );
                        });

                        for ( i in obj )
                            if ( dojo.indexOf(map, i ) < 0 && dojo.byId( i ) )
                                dojo.byId( i ).value = obj[i];

                        this.set('value', obj);

                        sv.call( this, arguments );
                    }
                }
        })();
    }
);