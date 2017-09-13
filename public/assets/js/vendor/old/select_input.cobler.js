$(function(){
	cobler.register({
		type: 'select',
    category: 'form',
		icon: 'sort',
		display: 'Multiple Choice',
		defaults: {
			label: 'Label',
			type: 'select',
			required : false,
			help: '',
			useName : true,
			// columns: '12'
		},
		filter: {
			required : false,
			help: '',
			useName : true,
			// columns: '12',			
			value:'',
			name:'',
			choices: '',
			label_key: '',
			value_key: '',
			max: 0,
			min: 0,
			step: 0,
			options: [
				{
					label: '',
					value: ''
				}
			]
		},
		toJSON: function(publishing) {
			cobler.slice.prototype.toJSON.call(this, publishing);
			if(publishing) {
				for(var i in this.filter){
					// if(this.filter[i] === this.attributes[i]){
					if(_.isEqual(this.filter[i], this.attributes[i]) ){
						delete this.attributes[i];
					}
				}
			}
			return this.attributes;
		},
		fields: [
			{type: 'fieldset',name:'basics', legend: '<i class="fa fa-th"></i> Basics',inline: true, fields:[
				{type: 'text', required: true, label: 'Field Label', name: 'label', value: 'Label'},
				{type: 'text', label: 'Name', name: 'name'},
				{type: 'select', label: 'Display', name: 'type', value: 'dropdown', choices: [
					{name: 'Dropdown', value: 'select'},
					{name: 'Radio', value: 'radio'}//,
					// {name: 'Range', value: 'range'}
				]},
				// {type: 'text', label: 'External List', name: 'choices'},

				// {type: 'text', label: 'Label-key', name: 'label_key'},
				// {type: 'text', label: 'Value-key', name: 'value_key'},

				{type: 'text', label: 'Default Value', name: 'value'},
				// {type: 'number', label: 'Max', name: 'max'},
				// {type: 'number', label: 'Min', name: 'min'},
				// {type: 'number', label: 'Step', name: 'step'},
				{type: 'textarea', label: 'Instructions', name: 'help'},
			{type: 'checkbox', label: 'Required?', name: 'required'}
				// {type: 'select', label: 'Size', name: 'columns', choices: [
				// 	'3','4','6','8','9','12'
				// ]},
			]},
			{type: 'fieldset', name:'choices_c', legend: '<i class="fa fa-th-list"></i> Choices', inline: true, fields:[
				{"type": "fieldset", "label": false, "multiple": {"duplicate": true}, "name": "options", "fields": [
					{"label": "Label"},
					{"label": "Value"}
				]}
			]},		



			// {type: 'fieldset',name:'choices_c', legend: '<i class="fa fa-th-list"></i> Choices', inline: true, fields:[
			// 	{type: 'fieldset', label: false, legend: '<i class="fa fa-square"></i> Choices', multiple: {duplicate: true}, name: 'choices_list', toArray: true, fields: [
			// 		{type: 'text', label: 'Choice', name: 'choices', toArray: true},
			// 	]},
			// ]}
		],
		// template: function(){
		// 	return 'berry_' + this.attributes.type;
		// }
		toHTML: function(publishing) {
			return Berry.render('berry_' + this.attributes.type, Berry.processOpts($.extend(true, {}, this.attributes)));
		}
	});
});