/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojox.grid.enhanced._Layout"]){dojo._hasResource["dojox.grid.enhanced._Layout"]=true;dojo.provide("dojox.grid.enhanced._Layout");dojo.require("dojox.grid._Layout");dojo.declare("dojox.grid.enhanced._Layout",dojox.grid._Layout,{addCellDef:function(_1,_2,_3){var _4=this.inherited(arguments);if(_4 instanceof dojox.grid.cells._Base){_4.getEditNode=function(_5){return ((this.getNode(_5)||0).firstChild||0).firstChild||0;};}return _4;},addViewDef:function(_6){var _7=this.inherited(arguments);if(!_7["type"]){_7["type"]=this.grid._viewClassStr;}return _7;}});}