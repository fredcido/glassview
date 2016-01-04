/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.financeiro.budget"]){dojo._hasResource["modulo.financeiro.budget"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.padrao.grid");dojo.provide("modulo.financeiro.budget");dojo.declare("modulo.financeiro.budget",[modulo.padrao.geral,modulo.padrao.grid],{projeto:null,budget:null,treeViewCategoria:null,gridBudgetLancamentos:null,mesesBudget:null,liberaEdicao:false,constructor:function(){},createCustomCellType:function(){var _1=this;window.eXcell_currencyBudget=function(_2){if(_2){this.cell=_2;this.grid=this.cell.parentNode.grid;this.cell.setAttribute("if-focused",0);}this.setValue=function(_3){this.setCValue(_3);};this.getValue=function(){if(!objGeral.empty(this.cell.getAttribute("if-focused"))){return 0;}else{return this.cell.innerHTML;}};this.edit=function(){if(!_1.liberaEdicao){return false;}var _4=eval("("+this.cell.parentNode.idd+")");if(!objGeral.empty(_4.fn_tipo_lanc_agrupador)){return false;}this.val=this.getValue();var _5=this;var _6=new dijit.form.CurrencyTextBox({value:objGeral.toFloat(this.val),onClick:function(e){(e||event).cancelBubble=true;},onKeyPress:function(e){if(e.keyCode==dojo.keys.ENTER){dijit.byId("projeto_id").focus();}},onFocus:function(){dijit.selectInputText(this.textbox);_5.cell.setAttribute("if-focused",1);},onBlur:function(){_5.cell.setAttribute("if-focused",0);_5.detach();}});$(this.cell).empty();this.cell.appendChild(_6.domNode);_6.focus();return true;};this.detach=function(){if(!objGeral.empty(this.cell.getAttribute("if-focused"))){return false;}var _7=dijit.findWidgets(this.cell);if(objGeral.empty(_7)){return false;}var _8=_7[0];var _9=_8.get("value");var _a=dojo.currency.format(parseFloat(_9),{currency:"R$",locale:"pt-br"});_8.destroy();this.setValue(_a);return this.val!=_9;};};window.eXcell_currencyBudget.prototype=new eXcell;},initBudget:function(){financeiroBudget.initButtons();financeiroBudget.clearData();financeiroBudget.createCustomCellType();},clearData:function(){this.projeto=null;this.budget=null;this.mesesBudget=null;this.liberaEdicao=false;this.bloqueiaBotoesAno();},initButtons:function(){var _b=new dijit.form.Button({iconClass:"icon-toolbar-zoom",showLabel:false,disabled:true,onClick:dojo.hitch(this,"treeCategoria"),id:"btnBuscaCategoria",style:"margin-top: 5px"});dijit.byId("fn_categoria_descricao").domNode.parentNode.parentNode.appendChild(_b.domNode);var _c=new dijit.form.Button({iconClass:"icon-toolbar-resultsetprevious",showLabel:false,disabled:true,id:"btnLastYear",onClick:dojo.hitch(this,"lastYearProject"),style:"margin-top: 5px"});var _d=new dijit.form.Button({iconClass:"icon-toolbar-resultsetnext",showLabel:false,disabled:true,id:"btnNextYear",onClick:dojo.hitch(this,"nextYearProject"),style:"margin-top: 5px"});dijit.byId("budget_ano").domNode.parentNode.parentNode.appendChild(_c.domNode);dijit.byId("budget_ano").domNode.parentNode.parentNode.appendChild(_d.domNode);},changeProjeto:function(){var _e=dijit.byId("projeto_id").get("value");if(objGeral.empty(_e)){dijit.byId("fn_categoria_descricao").set("value",null);dijit.byId("btnBuscaCategoria").setDisabled(true);this.clearData();return false;}var _f=this;var obj={url:baseUrl+"/financeiro/budget/verifica-budget/id/"+_e,handle:"json",callback:function(_10){dijit.byId("btnBuscaCategoria").setDisabled(false);dijit.byId("projeto_orcamento").set("value",parseFloat(_10.projeto.projeto_orcamento));if(!objGeral.empty(_10.budget)){dijit.byId("fn_budget_total").set("value",parseFloat(_10.budget.fn_budget_total));dojo.byId("fn_budget_id").value=_10.budget.fn_budget_id;_f.budget=_10.budget;}_f.liberaEdicao=_10.permissao;_f.projeto=_10.projeto;var _11=dojo.date.stamp.fromISOString(_f.projeto.projeto_inicio);dijit.byId("budget_ano").set("value",_11.getFullYear());_f.liberaBotoesAno();}};objGeral.buscaAjax(obj);return true;},bloqueiaBotoesAno:function(){dijit.byId("btnLastYear").setDisabled(true);dijit.byId("btnNextYear").setDisabled(true);},liberaBotoesAno:function(){var _12=dojo.date.stamp.fromISOString(this.projeto.projeto_inicio);var _13=dojo.date.stamp.fromISOString(this.projeto.projeto_final);var ano=dijit.byId("budget_ano").get("value");dijit.byId("btnLastYear").setDisabled(!(_12.getFullYear()<ano));dijit.byId("btnNextYear").setDisabled(!(_13.getFullYear()>ano));},nextYearProject:function(){if(objGeral.empty(this.projeto)){return false;}var _14=dojo.date.stamp.fromISOString(this.projeto.projeto_final);var ano=dijit.byId("budget_ano").get("value");if(_14.getFullYear()<=ano){return false;}dijit.byId("budget_ano").set("value",parseInt(ano)+1);this.liberaBotoesAno();this.changeCategoria();return true;},lastYearProject:function(){if(objGeral.empty(this.projeto)){return false;}var _15=dojo.date.stamp.fromISOString(this.projeto.projeto_inicio);var ano=dijit.byId("budget_ano").get("value");if(_15.getFullYear()>=ano){return false;}dijit.byId("budget_ano").set("value",parseInt(ano)-1);this.liberaBotoesAno();this.changeCategoria();return true;},treeCategoria:function(){objGeral.createDialog("/financeiro/budget/categorias/",objGeral.translate("Categorias"),dojo.hitch(this,"_initTree"));},_initTree:function(){if(!objGeral.empty(this.treeViewCategoria)){this.treeViewCategoria.destroyRecursive(true);}var _16=dijit.byId("projeto_id").get("value");objGeral.loading(true);var _17=new dojo.data.ItemFileWriteStore({url:baseUrl+"/financeiro/budget/tree-categorias/id/"+_16,hierarchical:true});_17.fetch({onComplete:function(){objGeral.loading(false);}});var _18=new modulo.custom.ForestStoreModel({store:_17,query:{type:"root"},childrenAttrs:["children"]},"store");this.treeViewCategoria=new modulo.custom.TreeMenu({model:_18,showRoot:false,persist:false,customIcons:false,expandOnClick:false});dojo.connect(this.treeViewCategoria,"onClick",dojo.hitch(this,"selectCategoria"));dojo.byId("tree-categoria").appendChild(this.treeViewCategoria.domNode);this.treeViewCategoria.startup();},selectCategoria:function(_19){var _1a=this.treeViewCategoria.model.store.getValue(_19,"agrupador");if(!objGeral.empty(_1a)){return false;}var _1b=this.treeViewCategoria.model.store.getValue(_19,"id");var _1c=this.treeViewCategoria.model.store.getValue(_19,"path");dijit.byId("fn_categoria_descricao").set("value",_1c);dojo.byId("fn_categoria_id").value=_1b;objGeral.closeGenericDialog();return true;},changeCategoria:function(){var _1d=dojo.byId("fn_categoria_id").value;if(objGeral.empty(_1d)){return false;}var ano=dijit.byId("budget_ano").get("value");var _1e=this;var obj={url:baseUrl+"/financeiro/budget/lista-lancamentos/",data:{projeto:_1e.projeto.projeto_id,ano:ano,projeto_inicio:_1e.projeto.projeto_inicio,projeto_final:_1e.projeto.projeto_final,categoria:_1d},handle:"json",callback:function(_1f){_1e.initGridBudgetLancamentos(_1f);dijit.byId("fn_budget_total_categoria").set("value",parseFloat(_1f.total_categoria));},callbackError:function(_20){}};objGeral.buscaAjax(obj);return true;},initGridBudgetLancamentos:function(_21){if(!objGeral.empty(this.gridBudgetLancamentos)){this.gridBudgetLancamentos.destructor();}this.mesesBudget=_21.meses_keys;var _22=[objGeral.translate("Tipo de lançamento"),objGeral.translate("Geral")];var _23=[95];var _24=["ro","currencyBudget"];var _25=["left","left"];var _26=["str","currencyBudget"];var _27=[objGeral.translate("Total"),"#cspan"];var _28=1010;for(x in _21.meses){_22.push(objGeral.translate(_21.meses[x]));_23.push(95);_24.push("currencyBudget");_25.push("left");_26.push("na");_27.push("{#stat_total}");_28-=40;}_23.unshift(_28);this.gridBudgetLancamentos=new dhtmlXGridObject("controle-budget");this.gridBudgetLancamentos.setHeader(_22.join(","));this.gridBudgetLancamentos.enableUndoRedo();this.gridBudgetLancamentos.enableEditTabOnly(true);this.gridBudgetLancamentos.setInitWidths(_23.join(","));this.gridBudgetLancamentos.setColAlign(_25.join(","));this.gridBudgetLancamentos.setColTypes(_24.join(","));this.gridBudgetLancamentos.setColSorting(_26.join(","));this.gridBudgetLancamentos.setSkin(objGrid.theme);this.gridBudgetLancamentos.attachFooter(_27.join(","));this.gridBudgetLancamentos.init();this.gridBudgetLancamentos.parse(_21.rows,"json");this.gridBudgetLancamentos.attachEvent("onCellChanged",dojo.hitch(this,"changeCellValue"));},changeCellValue:function(id,_29,_2a){var _2b=eval("("+id+")");var _2c=this.gridBudgetLancamentos.cells(id,_29);if(!objGeral.empty(_2c.cell.getAttribute("handed-setted"))){_2c.cell.setAttribute("handed-setted",1);return false;}if(_29==1){this.replicaValores(_2b,_2a);}else{var _2d=this;var obj={url:baseUrl+"/financeiro/budget/save/",data:{mes:this.mesesBudget[_29-2],fn_tipo_lanc_id:_2b.fn_tipo_lanc_id,valor_tipo_lanc:objGeral.toFloat(_2a)},handle:"json",callback:function(_2e){if(_2e.status){dijit.byId("formFinanceiroBudget").setValues(_2e);var _2f=dojo.currency.format(parseFloat(_2e.total_bugdet_geral),{currency:"R$",locale:"pt-br"});var _30=_2d.gridBudgetLancamentos.cells(id,1);_30.cell.setAttribute("handed-setted",1);_30.setValue(_2f);_30.cell.setAttribute("handed-setted",0);_2d.changeCategoria();}else{_2d.gridBudgetLancamentos.doUndo();}},callbackError:function(){_2d.gridBudgetLancamentos.doUndo();},form:dojo.byId("formFinanceiroBudget")};objGeral.buscaAjax(obj);}return true;},replicaValores:function(_31,_32){var _33=this;var obj={url:baseUrl+"/financeiro/budget/replica-valores/",data:{fn_tipo_lanc_id:_31.fn_tipo_lanc_id,valor:objGeral.toFloat(_32),projeto_inicio:_33.projeto.projeto_inicio,projeto_final:_33.projeto.projeto_final},handle:"json",callback:function(_34){if(_34.status){dijit.byId("formFinanceiroBudget").setValues(_34);_33.changeCategoria();}else{_33.gridBudgetLancamentos.doUndo();}},callbackError:function(){_33.gridBudgetLancamentos.doUndo();},form:dojo.byId("formFinanceiroBudget")};objGeral.buscaAjax(obj);}});var financeiroBudget=new modulo.financeiro.budget();}