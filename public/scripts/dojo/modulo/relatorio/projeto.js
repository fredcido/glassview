/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.relatorio.projeto"]){dojo._hasResource["modulo.relatorio.projeto"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.relatorio.padrao");dojo.provide("modulo.relatorio.projeto");dojo.declare("modulo.relatorio.projeto",[modulo.padrao.geral,modulo.relatorio.padrao],{constructor:function(){this.urlTarget="/relatorio/projeto/";},setConstraint:function(_1){switch(_1.id){case "dt_start":dojo.mixin(dijit.byId("dt_end").constraints,{min:dijit.byId("dt_start").get("value")});break;case "dt_end":dojo.mixin(dijit.byId("dt_start").constraints,{max:dijit.byId("dt_end").get("value")});break;}}});var relatorioProjeto=new modulo.relatorio.projeto();}