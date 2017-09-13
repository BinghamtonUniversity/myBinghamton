$(function(){
	cobler.register({
		type: 'checkbox',
    category: 'form',
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
			{type: 'checkbox', label: 'Default Value', name: 'value'},
			{type: 'textarea', label: 'Instructions', name: 'help'},
			{type: 'checkbox', label: 'Required?', name: 'required'}
			// {type: 'select', label: 'Size', name: 'columns', choices: [
			// 	'3','4','6','8','9','12'
			// ]},
		],
		template:  function(){
			return 'berry_checkbox';
		}
	});
});