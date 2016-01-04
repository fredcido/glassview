/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.relatorio.estoque"]){dojo._hasResource["modulo.relatorio.estoque"]=true;dojo.require("modulo.padrao.geral");dojo.require("modulo.relatorio.padrao");dojo.provide("modulo.relatorio.estoque");dojo.declare("modulo.relatorio.estoque",[modulo.padrao.geral,modulo.relatorio.padrao],{constructor:function(){this.urlTarget="/relatorio/estoque/";}});var relatorioEstoque=new modulo.relatorio.estoque();}