/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojox.grid.enhanced.plugins._Mixin"]){dojo._hasResource["dojox.grid.enhanced.plugins._Mixin"]=true;dojo.provide("dojox.grid.enhanced.plugins._Mixin");dojo.declare("dojox.grid.enhanced.plugins._Mixin",null,{_connects:[],_subscribes:[],privates:{},constructor:function(){this._connects=[],this._subscribes=[];this.privates=dojo.mixin({},dojox.grid.enhanced.plugins._Mixin.prototype);},connect:function(_1,_2,_3){var _4=dojo.connect(_1,_2,this,_3);this._connects.push(_4);return _4;},disconnect:function(_5){dojo.some(this._connects,function(_6,i,_7){if(_6==_5){dojo.disconnect(_5);_7.splice(i,1);return true;}});},subscribe:function(_8,_9){var _a=dojo.subscribe(_8,this,_9);this._subscribes.push(_a);return _a;},unsubscribe:function(_b){dojo.some(this._subscribes,function(_c,i,_d){if(_c==_b){dojo.unsubscribe(_b);_d.splice(i,1);return true;}});},destroy:function(){dojo.forEach(this._connects,dojo.disconnect);dojo.forEach(this._subscribes,dojo.unsubscribe);delete this._connects;delete this._subscribes;delete this.privates;}});}