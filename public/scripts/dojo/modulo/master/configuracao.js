/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.master.configuracao"]){dojo._hasResource["modulo.master.configuracao"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.padrao.grid");dojo.provide("modulo.master.configuracao");dojo.declare("modulo.master.configuracao",[modulo.padrao.geral],{constructor:function(){},afterSubmit:function(_1){if(_1.status){history.go(0);}}});var masterConfiguracao=new modulo.master.configuracao();}