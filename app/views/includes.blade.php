@include('common_includes')
<?php
// Assets::add('widget_factory.js', '/assets/js/widgets/');
// if(Config::get('app.debug')){
// 	Assets::add('widget_templates.js');
// 	// Assets::add('', '/assets/js/widgets/');
// }else{
// 	// Assets::add_script('widgets.min.js?v=1.1');
// }
Assets::add('widget_templates.js');

Assets::add_script('maps/api/js?key=&amp;sensor=true','//maps.googleapis.com/');

Assets::add('default.css', '/assets/css/');
