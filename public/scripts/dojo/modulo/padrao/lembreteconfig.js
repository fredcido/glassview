/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.padrao.lembreteconfig"]){dojo._hasResource["modulo.padrao.lembreteconfig"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.padrao.grid");dojo.provide("modulo.padrao.lembreteconfig");dojo.declare("modulo.padrao.lembreteconfig",[modulo.padrao.geral,modulo.padrao.grid],{container:null,constructor:function(){},buscaConfigLembretes:function(){var _1=dijit.byId("lembrete_config_tipo").get("value");if(objGeral.empty(_1)){dijit.byId("perfis").setValue([]);return false;}var _2={url:baseUrl+"/default/lembrete-config/list-perfis/",data:{tipo:_1},handle:"json",callback:function(_3){dijit.byId("perfis").setValue(_3);}};objGeral.buscaAjax(_2);return true;},afterSubmit:function(){}});var defaultLembreteConfig=new modulo.padrao.lembreteconfig();}