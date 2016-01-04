/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.custom.TreeMenu"]){dojo._hasResource["modulo.custom.TreeMenu"]=true;dojo.provide("modulo.custom.TreeMenu");dojo.declare("modulo.custom.TreeMenu",[dijit.Tree],{customIcons:true,expandOnClick:true,onClick:function(_1,_2){if(this.expandOnClick&&(_1.root||!objGeral.empty(this.model.store.getValue(_1,"children")))){this._onExpandoClick({node:_2});}this.inherited(arguments);},getIconClass:function(_3){if(this.customIcons){return objGeral.empty(_3.icone)?"icon-toolbar-application":_3.icone;}else{return this.inherited(arguments);}}});}