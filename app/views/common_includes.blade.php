<?php
Assets::add('font-awesome.min.css','//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/');
Assets::add('css?family=Open+Sans:300italic,400italic,600italic,400,600,300,700','//fonts.googleapis.com/');
Assets::add('default/default.css');
Assets::add('application.css');
Assets::add('toastr.css');

Assets::add('jquery.min.js');
Assets::add('', '/assets/js/berry/vendor/js/');
Assets::add('', '/assets/js/berry/vendor/css/');

Assets::add('c3.css', '//cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/');
Assets::add('d3.v3.js', '//d3js.org/');
Assets::add('c3.min.js', '//cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/');
Assets::add('ractive.min.js', '//unpkg.com/ractive/');
	Assets::add('sortable.js');
if(Config::get('app.debug')){
	// Assets::add('jquery-ui.min.js');	

	Assets::add('hogan-3.0.2.min.js');
	Assets::add('underscore.min.js');

	// Assets::add('core.berry.js', '/assets/js/berry/');
	// Assets::add('events.berry.js', '/assets/js/berry/');
	// Assets::add('field.berry.js', '/assets/js/berry/');
	// Assets::add('init.berry.js', '/assets/js/berry/');
	// // Assets::add('/', '/assets/js/berry/');
	// Assets::add('', '/assets/js/vendor/');

	// Assets::add('', '/assets/js/berry/');
	// Assets::add('', '/assets/js/berry/advanced/');
	// Assets::add('', '/assets/js/berry/enhance/');
	// Assets::add('', '/assets/js/berry/render/');
	// Assets::add('', '/assets/js/berry/fields/');
		
	Assets::add('full.berry.min.js');
	Assets::add('', '/assets/js/vendor/');

	// Assets::add('datetime.berry.js', '/assets/js/berry/advanced/');

	Assets::add('modal.mustache');

	Assets::add('', '/modules/groups/views/');
	Assets::add('', '/modules/images/views/');
	Assets::add('', '/modules/users/views/');
	Assets::add('', '/modules/polls/views/');
	Assets::add('', '/modules/forms/views/');
	Assets::add('', '/modules/communities/views/');
	Assets::add('', '/modules/services/views/');
	Assets::add('', '/modules/microapps/views/');
	Assets::add('', '/modules/endpoints/views/');
	Assets::add('portal_config_view.mustache');
	Assets::add('filter.mustache');
	Assets::add('spinner.mustache');
	Assets::add('template_init.js');

}else{
	Assets::add('full.berry.min.js');
	Assets::add('vendor.min.js');	
	// Assets::add('datetime.berry.js', '/assets/js/vendor/');
	Assets::add('common_includes.js');
}
Assets::add('filter.js');


Assets::add('ace.js', '//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/');
Assets::add('tinymce.min.js','//cdn.tinymce.com/4/');
// Assets::add('jquery.tinymce.min.js','//cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.6/');

// https://cloud.tinymce.com/stable/tinymce.min.js
// Assets::add('widget_factory.js', '/assets/js/widgets/');

Assets::add('cob.js', '/assets/js/');
Assets::add('content.cob.js', '/assets/js/');
Assets::add('service.cob.js', '/assets/js/');
Assets::add('microapp.cob.js', '/assets/js/');




Assets::add('jquery.flot.min.js', '/assets/js/vendor/flot/');
Assets::add('jquery.flot.pie.min.js', '/assets/js/vendor/flot/');
Assets::add('new_app.js');

// Assets::add('app.js');



// Assets::add('routes.js', '/modules/assets/js/');
