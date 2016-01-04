/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.relatorio.contaprojeto"]){dojo._hasResource["modulo.relatorio.contaprojeto"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.relatorio.padrao");dojo.provide("modulo.relatorio.contaprojeto");dojo.declare("modulo.relatorio.contaprojeto",[modulo.padrao.geral,modulo.relatorio.padrao],{constructor:function(){this.urlTarget="/relatorio/conta-projeto/";},buscaTiposLancamento:function(){var _1=dijit.byId("projeto_id").get("value");objGeral.changeFilteringSelect("fn_tipo_lanc_id",baseUrl+"/relatorio/conta/tipo-lancamento/id/",_1);}});var relatorioContaProjeto=new modulo.relatorio.contaprojeto();}