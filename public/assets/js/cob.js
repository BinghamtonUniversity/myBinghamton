//		CoblerJS 0.2.0
//		(c) 2011-2016 Adam Smallcomb
//		Licensed under the MIT license.
//		For all details and documentation:
//		https://github.com/Cloverstone/Cobler

function Cobler(options) {
  var topics = {};
	this.options = options
	this.options.active = this.options.active || 'widget_active';
	this.options.itemContainer = this.options.itemContainer || 'itemContainer';
	this.options.itemTarget = this.options.itemTarget || 'cobler-li-content';

  options.removed = false;
	//simple event bus with the topics object bound
  this.subscribe = function(topic, listener) {
    if(!topics[topic]) topics[topic] = [];
    topics[topic].push(listener);
  }.bind({topics: topics})
  this.publish = function(topic, data) {
    if(!topics[topic] || topics[topic].length < 1) return;
    topics[topic].forEach(function(listener) {
      listener(data || {});
    });
  }.bind({topics: topics})

  //initialize collections array and then create a collection for each target
	var collections = [];
	for(var i=0; i<options.targets.length; i++){
		addCollection.call(this, options.targets[i], options.items[i]);
	}

	function collection(target, items, cob){
		var sortable;
		function init() {
			target.addEventListener('click', eventManager.bind(this));
			if(!cob.options.disabled) {
				target.addEventListener('click', instanceManager.bind(this));
				target.className += ' cobler_container';
				sortable = Sortable.create(target, {
					forceFallback: !!cob.options.fallback,
					group: cob.options.group || 'cb',
					animation: 50,
					onSort: function (/**Event*/evt) {
						if(cob.options.remove) {
								cob.options.removed = items.splice(parseInt(evt.item.dataset.start, 10), 1)[0];
								cob.options.remove = false;
						}else if(evt.from !== evt.to && typeof evt.item.dataset.type === 'undefined'){
							cob.options.remove = true;
						}						
			    },
					onAdd: addOnDrop.bind(this), 
					onUpdate: function (evt) {
						var item = items.splice(parseInt(evt.item.dataset.start, 10), 1)[0];
						items.splice(getNodeIndex(evt.item), 0 , item);
						evt.item.removeAttribute('data-start');
						cob.publish('change', item);
						cob.publish('reorder', item);
					},
					onStart: function (evt) {
		        evt.item.dataset.start = getNodeIndex(evt.item);  // element index within parent
		    	}
				});
			}
			loadItems.call(this, items);
		}
		function reset(items) {
			target.innerHTML = '';
			items = items || [];
		}
		function addOnDrop(evt){
			var A = evt.item;
			//handle if dragged over target and put back in original
			if(A.parentNode === target){
				var newItem;
			 	if(typeof A.dataset.type !== 'undefined') {
					newItem = new Cobler.types[A.dataset.type](this);
				}else{
					// var temp = cob.options.removed.get();
					var temp = cob.options.removed.toJSON({editor:true});
					newItem = new Cobler.types[temp.widgetType](this);
					debugger;
					newItem.set(temp);
					evt.newIndex = getNodeIndex(evt.item);
				}
				var renderedItem = renderItem.call(this.owner, newItem);
			 	var a = A.parentNode.replaceChild(renderedItem, A);
				items.splice(evt.newIndex, 0 , newItem);
				if(typeof newItem.initialize !== 'undefined'){
					newItem.initialize(renderedItem)
				}

				if(typeof A.dataset.type !== 'undefined') {
			 		activate(renderedItem);
			 	}else{
			 		cob.options.removed = false;
			 	}
			}
			this.owner.publish('moved', newItem)
		}
		function eventManager(e){
			if(typeof e.target.dataset.event !== 'undefined'){
				cob.publish(e.target.dataset.event, items[getNodeIndex($(e.target).closest('.slice')[0])])
			}
		}
		function instanceManager(e) {
			var referenceNode = $(e.target).closest('.slice')[0];//e.target.parentElement.parentElement.parentElement;
			var classList = e.target.className.split(' ');
			if(classList.indexOf('remove-item') >= 0){
				if(confirm('Are you sure you want to delete this widget?')){
					var olditem = items.splice(getNodeIndex(referenceNode), 1);
					target.removeChild(referenceNode);
				 	cob.publish('remove', olditem);
				 	cob.publish('change', olditem);
			 	}
			}else if(classList.indexOf('duplicate-item') >= 0){
				deactivate();
				var index = getNodeIndex(referenceNode);
				addItem.call(this, items[index].get(), index+1);
			}else if(classList.indexOf('edit-item') >= 0){
				activate(referenceNode);
			}else if(e.target.tagName === 'LI' && target.className.indexOf('cobler_select') != -1) {
				deactivate();
				activate(e.target);
			}
		}
		function activate(targetEL) {
			targetEL.className += ' ' + cob.options.active;
			active = getNodeIndex(targetEL);
			cob.publish('activate', items[active]);
			items[active].edit();
			cob.publish('activated', items[active]);
		}
		function update(data, item) {
			var item = item || items[active];
			item.set(data);
			var temp = renderItem.call(cob,item);
			temp.className += ' ' + cob.options.active;
			var modEL = elementOf(item);
		 	var a = modEL.parentNode.replaceChild(temp, modEL);
		 	if(typeof item.initialize !== 'undefined'){
				item.initialize(temp)
			}
		 	cob.publish('change', item);
		}
		
		function deactivate() {
			active = null;
			var elems = target.getElementsByClassName(cob.options.active);
			[].forEach.call(elems, function(el) {
			    el.className = el.className.replace(cob.options.active, '');
			});
		}
		function loadItems(obj) {
			deactivate();
			reset(obj);
			items = [];
			for(var i in obj) {
				addItem.call(this, obj[i], false, true);
			}
		}
		function addItem(widgetType, index, silent) {
			if(typeof widgetType === 'undefined' || typeof Cobler.types[widgetType.widgetType || widgetType] === 'undefined') {
				return false;
			}
			index = index || items.length;
			var newItem = new Cobler.types[widgetType.widgetType || widgetType](this)
			if(typeof widgetType !== 'string'){
				newItem.set(widgetType);
			}
			items.splice(index, 0, newItem);
			var renderedItem = renderItem.call(this.owner, newItem);

			target.insertBefore(renderedItem, target.childNodes[index]);

			// target.insertBefore(renderedItem, target.querySelectorAll(':scope > LI')[index]);

			if(typeof newItem.initialize !== 'undefined'){
				newItem.initialize(renderedItem)
			}
			if(!silent){
				activate(renderedItem);
				cob.publish('change', newItem)
			}
		}
		function toJSON(opts) {
			var json = [];
			for(var i in items){
				json.push(items[i].toJSON(opts));
			}
			if(opts.object)return {target: target.dataset.id, items: json};
			return json;
		}
		function toHTML() {
			var temp = '';
			for(var i in items){
				temp += Cobler.types[items[i].widgetType].render(items[i]);
			}
			return temp;
		}
		function destroy(){
			reset();
			if(typeof sortable !== 'undefined') { sortable.destroy(); }
			target.removeEventListener('click', instanceManager);
			target.removeEventListener('click', eventManager);
		}
		function indexOf(item){
			return items.indexOf(item);
		}
		function elementOf(item){
				return target.children[items.indexOf(item)];
			}
		return {
			addItem: addItem,
			toJSON: toJSON,
			toHTML: toHTML,
			deactivate: deactivate,
			clear: reset,
			load: loadItems,
			update: update.bind(this),
			destroy: destroy,
			owner: cob,
			init: init,
			indexOf: indexOf,
			elementOf: elementOf
		}
	}

	function renderItem(item){
		var EL;
		if(options.disabled){
			EL = document.createElement('DIV');
		} else {
			EL = document.createElement('LI');
		}
		EL.className = 'slice';
		EL.innerHTML = templates[item.template || this.options.itemContainer].render(item.get(), templates);
		EL.getElementsByClassName(item.target || this.options.itemTarget)[0].innerHTML += item.render();
		return EL;
	}
	function getNodeIndex(node) {
	  var index = 0;
	  while (node = node.previousSibling) {
	    if (node.nodeType != 3 || !/^\s*$/.test(node.data)) {
	      index++;
	    }
	  }
	  return index;
	}
	function addCollection(target, item){
		var newCol = new collection(target, item, this);
		newCol.init();
		collections.push(newCol);
	}
	function addSource(element){
		Sortable.create(element, {
			group: {name: 'cb', pull: 'clone', put: false}, 
			sort: false 
		});
	}
	function applyToEach(func){
		return function(opts){
			var temp = [];
			for(var i in collections) {
				temp.push(collections[i][func].call(null, opts));
			}
			this.publish(func, temp);
			return temp;
		}.bind(this)
	}

	return {
		collections: collections,
		addCollection: addCollection.bind(this),
		addSource: addSource,
		toJSON: applyToEach.call(this, 'toJSON'),
		toHTML: applyToEach.call(this, 'toHTML'),
		clear: applyToEach.call(this, 'clear'),
		deactivate: applyToEach.call(this, 'deactivate'),
		destroy: applyToEach.call(this, 'destroy'),
		on: this.subscribe//,
		//trigger: this.publish.bind(this)
	};
}

Cobler.types = {};


berryEditor = function(container){
	return function(){
		var formConfig = $.extend(true, {}, {
			// renderer: 'tabs', 
			attributes: this.get(), 
			fields: this.fields,
			autoDestroy: true
		}, this.formOptions || {});

		var opts = container.owner.options;
		var events = 'save';
		if(typeof opts.formTarget !== 'undefined' && opts.formTarget.length){
			formConfig.actions = false;
			events = 'change';
		}	
		var myBerry = new Berry(formConfig, this.formTarget || $(container.elementOf(this)).find('.panel-body'));
		myBerry.on(events, function(){
		 	container.update(myBerry.toJSON(), this);
		 	container.deactivate();
		 	save();
		 	myBerry.trigger('saved');
		}, this);
		myBerry.on('cancel',function(){
		 	container.update(this.get(), this)
		 	container.deactivate();
		}, this)
		return myBerry;
	}
}

Cobler.layouts = [
		{
			value: '0',
			classes: 'bu-3-6-3',
			label: '<i title="Wide Middle" class="bu-3-6-3"></i>',
			template: '<div class="col-md-3 col-sm-4 column"></div><div class="col-sm-8 col-md-6 column"></div><div class="col-md-3 col-sm-12 column"></div>'
		},
		{
			value: '1',
			classes: 'bu-6-3-3',
			label: '<i title="Wide Left" class="bu-6-3-3"></i>',
			template: '<div class="col-md-6 col-sm-8 column"></div><div class="col-md-3 col-sm-4 column"></div><div class="col-md-3 col-sm-12 column"></div>'
		},
		{
			value: '2',
			classes: 'bu-4-4-4',
			label: '<i title="Even" class="bu-4-4-4"></i>',
			template: '<div class="col-md-4 col-sm-6 column"></div><div class="col-md-4 col-sm-6 column"></div><div class="col-md-4 col-sm-12 column"></div>'
		},
		{
			value: '3',
			classes: 'bu-6-6',
			label: '<i title="Split" class="bu-6-6"></i>',
			template: '<div class="col-sm-6 column"></div><div class="col-sm-6 column"></div>'
		},
		{
			value: '4',
			classes: 'bu-12-12',
			label: '<i title="Full" class="bu-12-12"></i>',
			template: '<div class="col-lg-12 column"></div>'
		},
		{
			value: '5',
			classes: 'bu-oddshape',
			label: '<i title="Odd Split" class="bu-oddshape"></i>',
			template: '<div class="col-sm-4 column"></div><div class="col-sm-8"><div class="row"><div class="col-sm-12 column"></div></div><div class="row"><div class="col-sm-6 column"></div><div class="col-sm-6 column"></div></div></div>'
		},
		{
			value: '6',
			classes: 'bu-doubledown',
			label: '<i title="Double Down" class="bu-doubledown"></i>',
			template: '<div class="col-sm-12"><div class="row"><div class="col-sm-12 column"></div></div><div class="row"> <div class="col-sm-6 column"></div><div class="col-sm-6 column"></div></div><div class="row"><div class="col-sm-12 column"></div></div></div>'
		},
		{
			value: '7',
			classes: 'bu-3-6-3',
			label: '<i title="Middle Only" class="bu-3-6-3"></i>',
			template: '<div class="col-lg-offset-3 col-md-offset-2  col-lg-6 col-md-8 col-sm-12 column"></div></div>'
		},
	];