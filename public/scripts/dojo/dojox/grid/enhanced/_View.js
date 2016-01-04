/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojox.grid.enhanced._View"]){dojo._hasResource["dojox.grid.enhanced._View"]=true;dojo.provide("dojox.grid.enhanced._View");dojo.require("dojox.grid._View");dojo.declare("dojox.grid.enhanced._View",dojox.grid._View,{_contentBuilderClass:dojox.grid.enhanced._ContentBuilder,postCreate:function(){if(this.grid.nestedSorting){this._headerBuilderClass=dojox.grid.enhanced._HeaderBuilder;}this.inherited(arguments);},setColumnsWidth:function(_1){if(dojo.isIE&&!dojo._isBodyLtr()){this.headerContentNode.style.width=_1+"px";this.headerContentNode.parentNode.style.width=_1+"px";}this.inherited(arguments);}});}