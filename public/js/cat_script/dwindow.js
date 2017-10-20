/**
 * v 2.7 [19.03.2010] by ELLO
 * 
 * dWindow
 *
 * request: mootools.js, mootools-more.js
 * home http://code.google.com/p/dwindows/
 *
 **/


var dWindowZIndex = 100;

var dWindow = new Class({
	
	// Р·Р°РёРјСЃС‚РІСѓРµРј РјРµС‚РѕРґС‹ РєР»Р°СЃСЃРѕРІ Options Рё Events
	Implements : [Options, Events],
	
	
	'options' : {
		'mode' 		: 'auto',	//html|frame|alert|choise|element
		'minWidth' 	: 300,
		'minHeight' : 150,
		'width' 	: 300,
		'height' 	: 150,
		'top' 		: '50%',
		'left' 		: '50%',
		'resizable' : true,
		'statusBar' : true,
		'content' 	: '',
		'id' 		: '',
		'icon' 		: null,
		'shadow' 	: true,
		'title' 	: '&nbsp;',
		'events' 	: {},
		'urlExt' 	: '(^http|[a-z0-9./]+.html)', // СЂРµРіСѓР»СЏСЂРЅРѕРµ РІС‹СЂР°Р¶РµРЅРёРµ РґР»СЏ РѕРїСЂРµРґРµР»РµРЅРёСЏ url,
		'addClass'	: null
	},
	
	
	'needClose' : false, // РёР·Р±Р°РІРёС‚СЊСЃСЏ
	
	'handle' : null,	//С…Р·
	'content' : null,
	'drag' : null,	
	'title' : null, 
	'content' : null,	
	'dragParams' : {},

	// РєРѕРЅСЃС‚СЂСѓРєС‚РѕСЂ РєР»Р°СЃСЃР°
	'initialize': function(options){
		this.setOptions(options);
		
		this.title = this.options.title;
		
		this.addEvents(this.options.events);
		
	},
	/**
	 *
	 **/
	'load' : function(data){
		
		// Р°РІС‚Рѕ РѕРїСЂРµРґРµР»РµРЅРёРµ С‚РёРїР° РґР°РЅРЅС‹С…
		if (this.options.mode == 'auto'){
			
			switch($type(data)){
				case 'string':
					
					// СЌР»РµРјРµРЅС‚
					if ($(data) !== null){
						
						this.options.mode    = 'element';
						this.options.content = $(data).clone();
						
					// СЃСЃС‹Р»РєР°
					}else if(data.test(this.options.urlExt, 'i')){
						
						this.options.mode    = 'url';
						this.options.content = data;
					
					// СЃС‚СЂРѕРєР°
					}else{
						
						this.options.mode    = 'html';
						this.options.content = data;
						
					}
					
					break;
				case 'element':
					this.options.mode    = 'element';
					this.options.content = data;
					break;
				
			}
				
		}else{
			this.options.content = data;
		}
		
		if(this.handle) this.useMode();
		
		return this;
	},
	
	/**
	 * РџРѕРєР°Р·Р°С‚СЊ РѕРєРЅРѕ
	 **/
	'show' : function(){
		
		this.fireEvent('beforeshow');
		
		// Р·Р°С‚РµРЅРµРЅРёРµ СЌРєСЂР°РЅР°
		this.lockBg();
		
		//this.calcLeftRight();
		this.precent2px();
		
		// СЃРѕР·РґР°С‚СЊ РѕРєРЅРѕ РµСЃР»Рё РЅРµ СЃРѕР·РґР°РЅРѕ
		if (!this.handle) this._create();
		
		this.content.addClass('loading');
		
		this.useMode();
		
		
		// РїРѕРєР°Р·Р°С‚СЊ РѕРєРЅРѕ
		this.handle.setStyle('display','');
		
		this.fireEvent('onshow');
		
		return this;
	},
	
	
	'useMode': function(){
		
		this.content.empty();
		
		switch(this.options.mode){
			case 'url':
				
				this.content.set('load', {'onComplete': function(){
					this.content.removeClass('loading');
				}.bind(this)});
			
				this.content.load(this.options.content);
				
				break;
			case 'element':
				
				this.content.removeClass('loading');
				
				this.options.content.inject(this.content);
				
				break;
			case 'frame':
				
				new IFrame({
					'src': this.options.content,
					'width':'100%',
					'height':'100%',
					'border': '0',
					'styles' : {
						'border' : '0px'
					}
				}).inject(this.content);
				this.content.setStyle('overflow', 'hidden');
				this.content.removeClass('loading');
				
				break;
			case 'alert':
				
				this._alert(this.options.content);
				
				
				break;
			default:
				
				if ($type(this.options.content) == 'element'){
					this.content.grab(this.options.content);
				}else{
					this.content.set('html', this.options.content);
				}
				
				if(this.options.mode != 'auto' || this.options.content != '') this.content.removeClass('loading');
				
		}
		
	},
	
	/**
	 * РљРѕРЅРІРµСЂС‚Р°С†РёСЏ РїРѕР·РёС†РёРё РёР· РїСЂРѕС†РµРЅС‚РѕРІ РІ РїРёРєСЃРµР»Рё
	 **/
	'precent2px' : function(){
		
		var ws = document.getSize(), scroll = document.getScroll();
		
		//var ws  = window.getSize();
		

		var precent2pxX, precent2pxY;
		
		
		if (this.options.width.toString().test('%$')){
			this.options.width = (ws.x / 100) * this.options.width.toInt();
		}
		
		if (this.options.height.toString().test('%$')){
			this.options.height = (ws.y / 100) * this.options.height.toInt();
		}
		
		
		if (this.options.left.toString().test('%$')){
			precent2pxX = (ws.x / 100) * this.options.left.toInt();
			var elementHalfWidth = this.options.width / 2;
			this.left = (scroll.x + (precent2pxX - elementHalfWidth )).max(0);
		}else{
			this.left = (scroll.x + this.options.left).toInt();
		}
		
		if (this.options.top.toString().test('%$')){
			precent2pxY = (ws.y / 100) * this.options.top.toInt();
			var elementHalfHeight = (this.options.height / 2) + 20;
			this.top  = (scroll.y + (precent2pxY - elementHalfHeight)).max(0);
		}else{
			this.top = (scroll.y + this.options.top).toInt();
		}
		
	},
	/**
	 * Р—Р°С‚РµРЅРµРЅРёРµ Рё Р±Р»РѕРєРёСЂРѕРІР°РЅРёРµ С„РѕРЅР°
	 **/
	'lockBg' : function (){
		
		/* Р·Р°С‚РµРЅРµРЅРёРµ СЌРєСЂР°РЅР° */
		if (this.options.shadow){
			
			var ws = window.getScrollSize();
			
			this.shadow = new Element('div', {
				'class' : 'dWindow-shadow',
				'styles':{
					'position' :'absolute',
					'left': '0px',
					'top': '0px',
					'width': ws.x + 'px',
					'height': ws.y + 'px',
					'z-index': ++dWindowZIndex
				}
			}).inject($(document.body));
			
			this.shadow.fade('hide');
			this.shadow.fade(0.7);
			//var myFx = new Fx.Tween(this.shadow);
			
			$(document.body).setStyle('overflow', 'hidden');
		}
	},
	
	'_create' : function(id){
		
		var target = $(document.body);
		
		if (!target)
			return;
		
		/* РѕСЃРЅРѕРІРЅРѕР№ СЌР»РµРјРµРЅС‚ РѕРєРЅР° */
		this.handle = new Element('div', {
			'class':'dWindow',
			'id' : this.options.id,
			'styles' : {
				'width': this.options.width.toInt() + 4,
				'left':this.left.toInt() + 'px',
				'top':this.top.toInt() + 'px',
				'z-index':(dWindowZIndex+1),
				'display' : 'none'
			}
		});
		
		if (this.options.addClass != '')this.handle.addClass(this.options.addClass);
		
		dWindowZIndex++;
		this.handle.addEvent('mousedown', this.up.bind(this));
		
		var styles = {};
		if (this.options.icon != null) {
			styles = { 'background-image' : 'url("' + this.options.icon + '")' }
		}
		
		var bar = new Element('div', {
			'class':'topBar',
			'html': this.title,
			'styles': styles
		});
		
		
		var closeBtn = new Element('a', {'class':'closeBtn'});
		closeBtn.addEvents({
			'click': this.close.bind(this)
		});
		closeBtn.injectInside(bar);

		var table = new Element('table',{
			'class':'dContainer',
			'cellpadding':0,
			'cellspacing':0,
			'border':0
		});

		var tbody = new Element('tbody');

		// РїРµСЂРІС‹Р№ СЂСЏРґ
		var row = new Element('tr');
		var leftBorder = new Element('td', {'rowspan':2,'class':'leftBorder'});
		var rightBorder = new Element('td', {'rowspan':2,'class':'rightBorder'});
		var center = new Element('td');
		this.content = content = new Element('div',{'class':'centralArea', styles : {'width':this.options.width.toInt(), 'height':this.options.height}});
		

		center.adopt(content);
		
		row.adopt([leftBorder, center, rightBorder]);

		var row2 = new Element('tr');
		var statusBar = new Element('td',{'class':'statusBar'});
		var brResize = new Element('div', {'class':'resize'});
		brResize.injectInside(statusBar);
		statusBar.injectInside(row2);
		
		var row3 = new Element('tr');
		var bottomBorder = new Element('td', {'colspan':3,'class':'bottomBorder'});
		bottomBorder.injectInside(row3);
		
		row.injectInside(tbody);
		row2.injectInside(tbody);
		row3.injectInside(tbody);
		
		tbody.injectInside(table);
		
		this.handle.adopt([bar, table]);

		
		this.handle.injectInside(target);
		
		
		if (typeof(Drag) != 'undefined'){
			
			this.drag = new Drag.Move(this.handle, {
				snap: 0,
				handle: bar,
				//container: target,
				onSnap: function(el){
					el.addClass('dragging');
				},
				onComplete: function(el){
					el.removeClass('dragging');
				}
			});
			
			
			if (this.options.resizable){
				
			
				var that = this;
				this.rbDrag = new Drag(rightBorder, {
					onSnap : function(elem){
						that.dragParams.w = content.getSize().x;
						that.dragParams.W = that.handle.getSize().x;
						that.dragParams.target = content;
					},
					onDrag : function(elem){
						var delta = this.mouse.now.x - this.mouse.start.x;
						var w = that.dragParams.w + delta;
						var targetPos = that.dragParams.target.getPosition();
						
						if (w < that.options.minWidth || (W + targetPos.x) > (window.getScrollLeft() + window.getWidth())) {
							return;
						}

						var W = that.dragParams.W + delta;
						that.dragParams.target.setStyle('width', w);
						that.handle.setStyle('width', W);
					}
				});
				
				this.lbDrag = new Drag(leftBorder, {
					onSnap : function(elem){
						that.dragParams.w = content.getSize().x;
						that.dragParams.W = that.handle.getSize().x;
						that.dragParams.pos = content.getPosition();
						that.dragParams.target = content;
					},
					
					onDrag : function(elem){
						var delta = this.mouse.now.x - this.mouse.start.x;
						var w = that.dragParams.w - delta;
						var W = that.dragParams.W - delta;
						var L = this.mouse.start.x + delta;
						if (w < that.options.minWidth || L < target.getPosition().x) {
							return;
						}
						that.dragParams.target.setStyle('width', w);
						that.handle.setStyle('width', W);
						that.handle.setStyle('left', L);
					}
				});
				
				this.bDrag = new Drag(bottomBorder, {
					onSnap : function(elem){
						that.dragParams.h = content.getSize().y;
						that.dragParams.H = that.handle.getSize().y;
						that.dragParams.target = content;
					},
					
					onDrag : function(elem){
						var delta = this.mouse.now.y - this.mouse.start.y;
						var h = that.dragParams.h + delta;
						var H = that.dragParams.H + delta;
						var targetPos = that.dragParams.target.getPosition();

						if (h < that.options.minHeight || (H + targetPos.y) > (window.getScrollTop() + window.getHeight())) {
							return;
						}
						that.dragParams.target.setStyle('height', h);
						that.handle.setStyle('height', H);
					}
				});
				
				
				this.resizeDrag = new Drag(brResize, {
					preventDefault: true,
					style: false,
					onSnap : function(elem){
						that.dragParams.h = content.getSize().y;
						that.dragParams.H = that.handle.getSize().y;
						that.dragParams.w = content.getSize().x;
						that.dragParams.W = that.handle.getSize().x;
						that.dragParams.target = content;
					},
					
					onDrag : function(){
						var deltay = this.mouse.now.y - this.mouse.start.y;
						var deltax = this.mouse.now.x - this.mouse.start.x;
						var h = that.dragParams.h + deltay;
						var H = that.dragParams.H + deltay;
						var w = that.dragParams.w + deltax;
						var W = that.dragParams.W + deltax;
						var targetPos = that.dragParams.target.getPosition();
						
						if (h < that.options.minHeight || (H + targetPos.y) > (window.getScrollTop() + window.getHeight())
							|| w < that.options.minWidth || (W + targetPos.x) > (window.getScrollLeft() + window.getWidth())) {
							return;
						}

						that.dragParams.target.setStyle('height', h);
						that.handle.setStyle('height', H);
						that.dragParams.target.setStyle('width', w);
						that.handle.setStyle('width', W);
					}
				});
				
			}
		}
		
		this.fireEvent('oncreate');
	},
	/**
	 * РЈРЅРёС‡С‚РѕР¶Р°РµС‚ РѕРєРЅРѕ
	 **/
	'_destroy' : function(){
		
		if ($type(this.handle) == 'element'){
			this.handle.destroy();
			if (this.shadow){
				
				//this.shadow.fade(0);
				this.shadow.destroy();
				
				
			}
			$(document.body).setStyle('overflow', 'auto');
		}
		this.handle = null;
		
	},
	
	/**
	 * Р—Р°РєСЂС‹С‚РёРµ РѕРєРЅР° 
	 **/
	'close' : function(){
		this.fireEvent('beforeclose');
		
		this._destroy();
		
		this.fireEvent('onclose');
	},
	
	'hide' : function(){
		this.handle.setStyle('display','none');
	},
	
	'up' : function(event){

		if (this.handle.getStyle('z-index') != dWindowZIndex)
		this.handle.setStyle('z-index', ++dWindowZIndex);
		
	},
	
	'alert' : function(text){
		
		this.options.width   = 300;
		this.options.height  = 100;
		this.options.mode    = 'alert';
		this.options.content = text;
		
		this.options.addClass   = 'dWindow-Alert';
		
		this.show();
	},
	
	'_alert' : function(data){
		
		var alertDiv = new Element('div', {
			'html':'<div class="greenBorder" style="display: table; height: 400px; #position: relative; overflow: hidden;">    <div style=" #position:absolute; #top: 50%;display: table-cell; vertical-align: middle;">      <div class="greenBorder" style=" #position: relative; #top:-50%">        any text<br>        any height<br>        any content, for example generated from DB<br>        everything is verticallycentered      </div>    </div>  </div>'/*,
			'styles':{
				'text-align':'center',
				'height'	: '99%',
				'width'		: '100%',
				'display': 'inline-block',
				'vertical-align': 'middle',
				'border':'1px solid #fff000'
			}*/
		});
		/*
		new Element('span',{
			'html'  : data
		}).inject(alertDiv);
		
		var control = new Element('div',{
			
		}).inject(alertDiv);
		
		new Element('button',{
			'html' : 'Ok'
		}).addEvent('click', function(){
			this.close();
		}.bind(this)).inject(control);
		
		this.content.removeClass('loading');
		*/
		alertDiv.inject(this.content);
		
	},
	
	'choise' : function(text){
		
		this.options.content = new Element('table',{
			'styles':{
				'text-align' : 'center',
				'height': this.options.height,
				'width' : '100%'
			},
			'border' : '1'
		});
		var tbody = new Element('tbody').inject(this.options.content);
		var row1 = new Element('tr').inject(tbody);
		
		var td = new Element('td').inject(row1);
		new Element('div', {'html':text}).inject(td);
		
		
		var control = new Element('div',{
			'styles':{
				'display':'block'
			}
		}).inject(td);
		
		new Element('button', {
			'html' : ' Ok '
		}).addEvent('click', function(){
			
			this.fireEvent('onsuccess');
			this.close();
			
		}.bind(this)).inject(control);
		
		new Element('button', {
			'html' : ' РћС‚РјРµРЅР° '
		}).addEvent('click', function(){
			
			this.fireEvent('oncancel');
			this.close();
			
		}.bind(this)).inject(control);
		
		
		this.show();
	}
	
	
});