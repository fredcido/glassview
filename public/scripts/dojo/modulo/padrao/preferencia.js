/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.padrao.preferencia"]){dojo._hasResource["modulo.padrao.preferencia"]=true;dojo.require("modulo.padrao.geral");dojo.provide("modulo.padrao.preferencia");dojo.declare("modulo.padrao.preferencia",[modulo.padrao.geral],{constructor:function(){},edit:function(){objGeral.createDialog("/preferencia/edit/",objGeral.translate("Preferências"));return true;},atualizarGrid:function(){return true;}});var defaultPreferencia=new modulo.padrao.preferencia();}