$(function(){
	cobler.register({
		type: 'widget_checkbox',
    category: 'widget',
		icon: 'check-square-o',
		display: 'Checkbox',
		defaults: {
			label: 'Label',
			type: 'checkbox',
			help: '',
			container: 'span',
			value: false,
			required : false,
			// columns: '12'
		},
		filter: {
			help: '',
			container: 'span',
			value: false,
			required : false,
			// columns: '12',
			name:''
		},
		toJSON: function(publishing){
			cobler.slice.prototype.toJSON.call(this, publishing);
			this.attributes.name = this.attributes.name || this.attributes.label;
			if(this.attributes.display_type == 'switch'){
				this.attributes['alt-display'] = 'ui-switch ui-switch-success';
				this.attributes.container = 'i';
			}else{
				this.attributes['alt-display'] = '';
				this.attributes.container = 'span';
			}
			if(publishing) {
				for(var i in this.filter){
					if(this.filter[i] === this.attributes[i]){
						delete this.attributes[i];
					}
				}
			}
			return this.attributes;
		},
		fields: [
			{type: 'text', required: true, label: 'Field Label', name: 'label', value: 'Label'},
			{type: 'text', label: 'Name', name: 'name'},
			// {type: 'select', label: 'Display', name: 'type', value: 'dropdown', 'choices': [
			// 	{label: 'Basic', value: 'checkbox'},
			// 	// {label: 'Collection', value: 'collection'},
			// ]},
			{type: 'select', label: 'Display', name: 'display_type', value: 'dropdown', 'choices': [
				{label: 'Checkbox', value: 'checkbox'},
				{label: 'Switch', value: 'switch'}
			]},
			{type: 'checkbox', label: 'Default Value', name: 'value'},
			{type: 'textarea', label: 'Instructions', name: 'help'},
			{type: 'checkbox', label: 'Allow User to edit', name: 'userEdit'},
			// {type: 'select', label: 'Size', name: 'columns', choices: [
			// 	'3','4','6','8','9','12'
			// ]},
		],
		template:  function(){
			return 'berry_checkbox';
		}
	});
});