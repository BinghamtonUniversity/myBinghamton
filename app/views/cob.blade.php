<!DOCTYPE html>
<html lang="en">
<head>
	<title>Portal Runtime</title>
	<link rel="stylesheet" href="/assets/fonts/themify-icons/themify-icons.min.css">
	<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="/assets/css/main.css">	
	<!-- <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">	 -->
	<!-- <link rel="stylesheet" type="text/css" href="/assets/css/application.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="https://bootswatch.com/darkly/bootstrap.min.css"> -->
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,600,300,700">
	<link rel="stylesheet" href="/modules/assets/css/default.css">	
	<link rel="stylesheet" href="/assets/css/application.css">
	<link rel="stylesheet" href="/assets/css/nivo-slider.css">
	<link rel="stylesheet" href="/assets/css/default/default.css">
	<link rel="stylesheet" href="/assets/css/ie10-viewport-bug-workaround.css">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.on-collapse {
    display: none;
}




.parent-hover{visibility: hidden;}
*:hover > .parent-hover{visibility: visible;}
.cobler_container{list-style:none;/*padding-left:0;*/}
.cobler_container > li > .parent-hover{z-index: 10;opacity:.6;position:absolute;top:5px;right:5px;}
.cobler_container > li{position: relative;display: block;overflow:hidden;}
.cobler_container > li:hover{outline:dashed 1px #ccc;z-index: 1}
.cobler_container > li.sortable-chosen {outline:none;outline:dashed 1px #222;background:#fff;}
.cobler_container > li.sortable-ghost {opacity: 1;border:solid 0px #ccc;}
.cobler_container > li.sortable-ghost .parent-hover{display:none !important;}
.cobler_select.cobler_container > li:before{content:'';z-index: 10; position: absolute; top: 0; bottom: 0; left: 0; right: 0;}
.cobler_select.cobler_container > li.widget_active{outline: dashed 1px #222;z-index: 2;background:#FFFFF8;}
.cobler_select.cobler_container > li.sortable-ghost:before{background:#fff;}


.cobler_container > li.widget_active{outline: dashed 1px #999;z-index: 2;background:#FFFFF8;}


/*.opaque.cobler_container > li.sortable-ghost {opacity: 0;}
.opaque.cobler_container > li.sortable-fallback {opacity: 1 !important;overflow:initial;}
.opaque.cobler_container > li.sortable-chosen {outline:none;border:0;margin:0;background:none}
.opaque.cobler_container > li:hover .cobler-li-content{box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);}
.opaque.cobler_container > li.sortable-chosen .cobler-li-content{box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);}
.opaque.cobler_container > li:hover{outline:0;}*/

.column{min-height: 50px}

#page .nivo-controlNav {
    display:none;
}
#page .theme-default .nivoSlider {
    margin-bottom: 20px;
}

</style>
</head>
<body class="main-container" id="page">

  {{ $appMenu }}

	<div class="generated-content" id="content"></div>

	<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
	<!-- // <script type="text/javascript" src="/assets/js/vendor/jquery-ui.min.js"></script> -->
	<script type="text/javascript" src="/assets/js/new_runtime_toolbox.js"></script>  

	<!-- // <script type="text/javascript" src="/assets/js/vendor/bootstrap.min.js"></script> -->
	<script type="text/javascript" src="/assets/js/vendor/hogan-3.0.2.min.js"></script>
	<script type="text/javascript" src="/assets/js/full.berry.min.js"></script>
	<script type="text/javascript" src="/assets/js/vendor/bootstrap.full.berry.js"></script>
	<script type="text/javascript" src="/assets/js/vendor/underscore.min.js"></script>
	<!-- // <script type="text/javascript" src="/assets/js/vendor/backbone.min.js"></script> -->
	<script type="text/javascript" src="/assets/js/vendor/Chart.min.js"></script>
	<script type="text/javascript" src="/assets/js/vendor/moment.js"></script>
	<script type="text/javascript" src="/assets/js/vendor/bread.min.js"></script>
	<!-- // <script type="text/javascript" src="/assets/js/widgets/widget_factory.js"></script> -->
	<!-- // <script type="text/javascript" src="/assets/js/widgets.min.js"></script> -->
	<script type="text/javascript" src="/assets/js/sortable.js"></script>
	 <script type="text/javascript" src="/assets/js/widget_templates.js"></script>
	<script type="text/javascript" src="/assets/js/cob.js"></script>
	<script type="text/javascript" src="/assets/js/content.cob.js"></script>
	<script type="text/javascript" src="/assets/js/service.cob.js"></script>
  <script type="text/javascript" src="/assets/js/ie10-viewport-bug-workaround.js"></script>
  <script type="text/javascript" src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<script type="text/javascript" src="/assets/js/vendor.min.js"></script>
	<script type="text/javascript" src="/assets/js/new_app.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=&amp;sensor=true"></script>


  <!-- <script type="text/javascript" src="/assets/js/offcanvas.js"></script> -->
	<script type="text/javascript">

	groupID = '';
	groups = {{ $groups }};
	pages = {{ $pages }};
	preferences = {{ $preferences }};
	editable = false;
	editor = false;
	services = [];
	pagePreferences = [];
	page = "{{$page}}";
	tags = {{ isset($tags) ? json_encode($tags) : '{}' }};
	
	composites = {{ isset($composites) ? json_encode($composites) : '[]' }};


	function load() {
			hashParams = QueryStringToHash(document.location.hash.substr(1) || '')
			if(page) {
				hashParams['id'] = page;
			}
			var data = _.findWhere(pages, {id: parseInt(hashParams['id'])}) || pages[0];
			pagePreferences = _.findWhere(preferences, {page_id: data.id+""}) || [];
			pageData = JSON.parse(data.content);
			pageLayout = data.layout;
			pageID = data.id;
			groupID = data.group_id;
			var group = _.findWhere(groups, {id: data.group_id});

			// wf = $('.generated-content').widget_factory()
			// wf.load(pageData, pageLayout);

			fullPage = false;
			renderPage();



			$('.navbar-brand').html(group.name + ' - ' + data.name)

	}

	window.onhashchange = load;

	$(function() {
		load();
	});
	</script>
</body>
</html>
