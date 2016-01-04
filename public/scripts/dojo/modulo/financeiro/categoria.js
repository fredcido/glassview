/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.financeiro.categoria"]){dojo._hasResource["modulo.financeiro.categoria"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.padrao.grid");dojo.provide("modulo.financeiro.categoria");dojo.declare("modulo.financeiro.categoria",[modulo.padrao.geral,modulo.padrao.grid],{treeView:null,constructor:function(){},initGrid:function(){objGeral.loading(true);financeiroCategoria.gridHeader="#,"+objGeral.translate("Descrição")+","+objGeral.translate("Projeto")+","+objGeral.translate("Status");document.getElementById("gridFinanceiroCategoria").style.height=objGrid.gridHeight+"px";financeiroCategoria.grid=new dhtmlXGridObject("gridFinanceiroCategoria");financeiroCategoria.grid.setHeader(financeiroCategoria.gridHeader);financeiroCategoria.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter");financeiroCategoria.grid.setInitWidths(objGrid.idWidth+",*,350,100");financeiroCategoria.grid.setColAlign("center,left,left,left");financeiroCategoria.grid.setColTypes("ro,ro,ro,ro");financeiroCategoria.grid.setColSorting("str,str,str,str");financeiroCategoria.grid.setSkin(objGrid.theme);financeiroCategoria.grid.enablePaging(true,objGrid.pagResults,objGrid.pagIndexes,"divpagFinanceiroCategoriaGrid",true,"divpagfinanceiroCategoria");financeiroCategoria.grid.attachFooter(objGrid.tituloTotal+",#cspan,#cspan,{#stat_count}");financeiroCategoria.grid.attachEvent("onRowDblClicked",financeiroCategoria.edit);financeiroCategoria.grid.init();financeiroCategoria.grid.load(baseUrl+"/financeiro/categoria/list",dojo.hitch(objGeral,"loading",false),"json");},novo:function(){objGeral.createDialog("/financeiro/categoria/form/",objGeral.translate("Categoria"));},edit:function(){if(!financeiroCategoria.grid.getSelectedId()){objGeral.msgAlerta(objGeral.translate("Selecione o item para edição."));return false;}objGeral.createDialog("/financeiro/categoria/edit/id/"+financeiroCategoria.grid.getSelectedId(),objGeral.translate("Categoria"));return true;},atualizarGrid:function(){objGeral.atualizarGrids([financeiroCategoria.grid]);},organizar:function(){var _1=objGeral.createDialog("/financeiro/categoria/organizar/",objGeral.translate("Organizar"));},carregarCategoriasProjeto:function(){var id=dijit.byId("projeto_id").get("value");if(objGeral.empty(id)){return false;}this._initTree(id);return true;},_initTree:function(id){if(!objGeral.empty(this.treeView)){this.treeView.destroyRecursive(true);}objGeral.loading(true);var _2=new dojo.data.ItemFileWriteStore({url:baseUrl+"/financeiro/categoria/tree/id/"+id,hierarchical:true});_2.fetch({onComplete:function(){objGeral.loading(false);}});var _3=new modulo.custom.ForestStoreModel({store:_2,query:{type:"root"},childrenAttrs:["children"]},"store");this.treeView=new modulo.custom.TreeMenu({model:_3,showRoot:false,persist:false,dragThreshold:8,betweenThreshold:5,dndController:"dijit.tree.dndSource",customIcons:false});dojo.connect(this.treeView,"_onItemChildrenChange",dojo.hitch(this,"changeOrder"));dojo.byId("tree-categoria").appendChild(this.treeView.domNode);this.treeView.startup();},changeOrder:function(_4,_5){var _6=[];dojo.forEach(_5,function(_7){_6.push(_7.id[0]);});var _8=this;var _9={url:baseUrl+"/financeiro/categoria/organizar-categoria/",data:{pai:_4.root?null:_4.id,"filhos[]":_6},handle:"json",noload:true,callback:function(_a){if(!_a.status){objGeral.msgErro(_a.message);}}};objGeral.buscaAjax(_9);return true;}});var financeiroCategoria=new modulo.financeiro.categoria();}