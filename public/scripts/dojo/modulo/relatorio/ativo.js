/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.relatorio.ativo"]){dojo._hasResource["modulo.relatorio.ativo"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.relatorio.padrao");dojo.provide("modulo.relatorio.ativo");dojo.declare("modulo.relatorio.ativo",[modulo.padrao.geral,modulo.relatorio.padrao],{constructor:function(){this.urlTarget="/relatorio/ativo/";}});var relatorioAtivo=new modulo.relatorio.ativo();}