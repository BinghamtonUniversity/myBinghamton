<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.0/backbone-min.js"></script>
<script type="text/javascript" src="/modules/assets/js/forms/resource.js"></script>
<script>		
		var form_id = {{$id}} || false;
		var url = '/forms';
		var form;
		$.ajax({
			url: url+'/'+form_id,
			success: function(data){
				$('#content').html(render('admin_edit_form_view', data));

				templates['itemContainer'] = Hogan.compile('<div class="cobler-li-content"></div><div class="btn-group parent-hover"><span class="remove-item btn btn-danger fa fa-trash-o" data-title="Remove"></span><span class="duplicate-item btn btn-default fa fa-copy" data-title="Duplicate"></span></div>')

				form = data;

				var items = {};
				if(typeof data.fields !== 'undefined' &&  data.fields !== null){
					try{
						items = JSON.parse(data.fields) || {};
					}catch(e){}
				}

	      cb = new Cobler({formTarget:$('#form'), disabled: false, targets: [document.getElementById('editor')],items:[items]})
	      list = document.getElementById('sortableList');
	      cb.addSource(list);
	      cb.on('activate', function(){
	        if(list.className.indexOf('hidden') == -1){
	          list.className += ' hidden';
	        }
	        $('#form, .reset-form-view').removeClass('hidden');
	      })
	      cb.on('deactivate', function(){
	        list.className = list.className.replace('hidden', '');
	        $('#form, .reset-form-view').addClass('hidden');
	      })
	      cb.on('remove', function(){
	      	cb.deactivate();
	      })

	      document.getElementById('sortableList').addEventListener('click', function(e) {
	        cb.collections[0].addItem(e.target.dataset.type);
	      })


			}
		})


$('body').on('click', '.options', function() {
		var attributes = _.clone(form);
		attributes.options = JSON.parse(attributes.options||"{}");

		this.berry = $().berry({flatten:false,legend:'Form Options', attributes: attributes, fields:{
			options:{label:false, fields:{
				Inline:{type: 'checkbox'},
			}},
			'Spreadsheat ID': {name:'gs_id'},
			Target:{},
			Name:{}
		}}).on('save', $.proxy(function(){

			var attributes = this.berry.toJSON();
			attributes.options = JSON.stringify(attributes.options);
			this.model.set(attributes);

			this.berry.trigger('saved');

		},this));
	})

$('body').on('click', '.save', function() {
					form.fields = JSON.stringify(cb.toJSON({})[0]);
					$.ajax({
						url:'/forms/{{$id}}',
						data: form,
						method:'PUT',
						success: function(model){
							form = model;
							toastr.success(model.name +' has been successfully saved.', 'Success!')

						}.bind(this),
						error: function(e){
							toastr.error(e, 'Error on save')
						},
						statusCode: {
					    404: function() {
								toastr.error('You are no longer logged in', 'Logged Out')
					    },
					    409: function() {
								toastr.warning('conflict detected', 'NOT SAVED')
					    },
					    401: function() {
								toastr.error('You are not authorized to perform this action', 'Not Authorized')
					    }
					  }
					})
	})



</script>