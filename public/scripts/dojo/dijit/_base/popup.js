/*
	Copyright (c) 2004-2010, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dijit._base.popup"]){dojo._hasResource["dijit._base.popup"]=true;dojo.provide("dijit._base.popup");dojo.require("dijit._base.focus");dojo.require("dijit._base.place");dojo.require("dijit._base.window");dijit.popup={_stack:[],_beginZIndex:1000,_idGen:1,moveOffScreen:function(_1){var _2=_1.declaredClass?_1._popupWrapper:(dojo.hasClass(_1.parentNode,"dijitPopup")&&_1.parentNode),_3=_1.domNode||_1;if(_2){dojo.style(_2,{visibility:"hidden",top:"-9999px"});}else{_2=dojo.create("div",{"class":"dijitPopup",style:{visibility:"hidden",top:"-9999px"},role:"presentation"},dojo.body());_2.appendChild(_3);var s=_3.style;s.display="";s.visibility="";s.position="";s.top="0px";if(_1.declaredClass){_1._popupWrapper=_2;dojo.connect(_1,"destroy",function(){dojo.destroy(_2);delete _1._popupWrapper;});}}},getTopPopup:function(){var _4=this._stack;for(var pi=_4.length-1;pi>0&&_4[pi].parent===_4[pi-1].widget;pi--){}return _4[pi];},open:function(_5){var _6=this._stack,_7=_5.popup,_8=_5.orient||((_5.parent?_5.parent.isLeftToRight():dojo._isBodyLtr())?{"BL":"TL","BR":"TR","TL":"BL","TR":"BR"}:{"BR":"TR","BL":"TL","TR":"BR","TL":"BL"}),_9=_5.around,id=(_5.around&&_5.around.id)?(_5.around.id+"_dropdown"):("popup_"+this._idGen++);if(!_7._popupWrapper){this.moveOffScreen(_7);}var _a=_7._popupWrapper;dojo.attr(_a,{id:id,style:{zIndex:this._beginZIndex+_6.length},"class":"dijitPopup "+(_7.baseClass||_7["class"]||"").split(" ")[0]+"Popup",dijitPopupParent:_5.parent?_5.parent.id:""});if(dojo.isIE||dojo.isMoz){if(!_7.bgIframe){_7.bgIframe=new dijit.BackgroundIframe(_a);}}var _b=_9?dijit.placeOnScreenAroundElement(_a,_9,_8,_7.orient?dojo.hitch(_7,"orient"):null):dijit.placeOnScreen(_a,_5,_8=="R"?["TR","BR","TL","BL"]:["TL","BL","TR","BR"],_5.padding);_a.style.visibility="visible";_7.domNode.style.visibility="visible";var _c=[];_c.push(dojo.connect(_a,"onkeypress",this,function(_d){if(_d.charOrCode==dojo.keys.ESCAPE&&_5.onCancel){dojo.stopEvent(_d);_5.onCancel();}else{if(_d.charOrCode===dojo.keys.TAB){dojo.stopEvent(_d);var _e=this.getTopPopup();if(_e&&_e.onCancel){_e.onCancel();}}}}));if(_7.onCancel){_c.push(dojo.connect(_7,"onCancel",_5.onCancel));}_c.push(dojo.connect(_7,_7.onExecute?"onExecute":"onChange",this,function(){var _f=this.getTopPopup();if(_f&&_f.onExecute){_f.onExecute();}}));_6.push({widget:_7,parent:_5.parent,onExecute:_5.onExecute,onCancel:_5.onCancel,onClose:_5.onClose,handlers:_c});if(_7.onOpen){_7.onOpen(_b);}return _b;},close:function(_10){var _11=this._stack;while(dojo.some(_11,function(_12){return _12.widget==_10;})){var top=_11.pop(),_13=top.widget,_14=top.onClose;if(_13.onClose){_13.onClose();}dojo.forEach(top.handlers,dojo.disconnect);if(_13&&_13.domNode){this.moveOffScreen(_13);}if(_14){_14();}}}};dijit._frames=new function(){var _15=[];this.pop=function(){var _16;if(_15.length){_16=_15.pop();_16.style.display="";}else{if(dojo.isIE){var _17=dojo.config["dojoBlankHtmlUrl"]||(dojo.moduleUrl("dojo","resources/blank.html")+"")||"javascript:\"\"";var _18="<iframe src='"+_17+"'"+" style='position: absolute; left: 0px; top: 0px;"+"z-index: -1; filter:Alpha(Opacity=\"0\");'>";_16=dojo.doc.createElement(_18);}else{_16=dojo.create("iframe");_16.src="javascript:\"\"";_16.className="dijitBackgroundIframe";dojo.style(_16,"opacity",0.1);}_16.tabIndex=-1;dijit.setWaiRole(_16,"presentation");}return _16;};this.push=function(_19){_19.style.display="none";_15.push(_19);};}();dijit.BackgroundIframe=function(_1a){if(!_1a.id){throw new Error("no id");}if(dojo.isIE||dojo.isMoz){var _1b=dijit._frames.pop();_1a.appendChild(_1b);if(dojo.isIE<7){this.resize(_1a);this._conn=dojo.connect(_1a,"onresize",this,function(){this.resize(_1a);});}else{dojo.style(_1b,{width:"100%",height:"100%"});}this.iframe=_1b;}};dojo.extend(dijit.BackgroundIframe,{resize:function(_1c){if(this.iframe&&dojo.isIE<7){dojo.style(this.iframe,{width:_1c.offsetWidth+"px",height:_1c.offsetHeight+"px"});}},destroy:function(){if(this._conn){dojo.disconnect(this._conn);this._conn=null;}if(this.iframe){dijit._frames.push(this.iframe);delete this.iframe;}}});}