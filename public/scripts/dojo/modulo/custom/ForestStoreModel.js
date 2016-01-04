/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["modulo.custom.ForestStoreModel"]){dojo._hasResource["modulo.custom.ForestStoreModel"]=true;dojo.provide("modulo.custom.ForestStoreModel");dojo.declare("modulo.custom.ForestStoreModel",[dijit.tree.ForestStoreModel],{onAddToRoot:function(_1){this.store.setValue(_1,"type","root");this._requeryTop();},onLeaveRoot:function(_2){this.store.setValue(_2,"type","child");this._requeryTop();},pasteItem:function(_3,_4,_5,_6,_7){if(_4==this.root&&_5==this.root){var _8=this.root;dojo.forEach(this.childrenAttrs,function(_9){if(!_6){var _a=dojo.filter(_8.children,function(x){return x!=_3;});_8.children=_a;}parentAttr=_9;});if(typeof _7=="number"){_8.children.splice(_7,0,_3);}else{_8.children.push(_3);}this.onChildrenChange(this.root,_8.children);}else{this.inherited(arguments);}}});}