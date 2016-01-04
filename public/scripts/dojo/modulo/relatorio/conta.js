/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.relatorio.conta"]){dojo._hasResource["modulo.relatorio.conta"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.relatorio.padrao");dojo.provide("modulo.relatorio.conta");dojo.declare("modulo.relatorio.conta",[modulo.padrao.geral,modulo.relatorio.padrao],{constructor:function(){this.urlTarget="/relatorio/conta/";},buscaTiposLancamento:function(){var _1=dijit.byId("projeto_id").get("value");objGeral.changeFilteringSelect("fn_tipo_lanc_id",baseUrl+"/relatorio/conta/tipo-lancamento/id/",_1);}});var relatorioConta=new modulo.relatorio.conta();}