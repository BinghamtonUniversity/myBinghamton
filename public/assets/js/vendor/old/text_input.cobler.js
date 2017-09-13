$(function(){
	cobler.register({
		type: 'textbox',
		category: 'form',
		icon: 'font',
		display: 'Text',
		defaults: {
			label: 'Label',
			type: 'text',
			required: false,
			help: '',
			// columns: '12'
		},
		filter: {
			type: 'text',
			required: false,
			help: '',
			// columns: '12',
			placeholder: '',
			instructions: '',
			value:'',
			name:''
		},
		toJSON: function(publishing){
			cobler.slice.prototype.toJSON.call(this, publishing);
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
			{type: 'select', label: 'Display', name: 'type', value: 'dropdown', 'choices': [
				{label: 'Single Line', value: 'text'},
				{label: 'Multi-line', value: 'textarea'},
				{label: 'Phone', value: 'phone'},
				{label: 'Email', value: 'email'},
				{label: 'Date', value: 'date'},
				{label: 'Number', value: 'number'},
				{label: 'Color', value: 'color'}
			]},
			{type: 'text', label: 'Placeholder', name: 'placeholder'},
			{type: 'text', label: 'Default value', name: 'value'},
			{type: 'textarea', label: 'Instructions', name: 'help'},
			{type: 'checkbox', label: 'Required?', name: 'required'}
			// {type: 'select', label: 'Size', name: 'columns', choices: [
			// 	'3','4','6','8','9','12'
			// ]},
		],
		template:  function(){
			return 'berry_' + this.attributes.type;
		}
	});
});