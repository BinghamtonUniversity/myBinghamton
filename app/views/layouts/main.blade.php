@include('includes')
<!DOCTYPE html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <add name="X-UA-Compatible" value="IE=edge" /><![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"><!--<![endif]-->
<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
				<title>{{ $name or ''}} - My Binghamton</title>
				<meta name="description" content="Portal">
				<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
				<meta name="apple-mobile-web-app-capable" content="yes">
				<meta name="mobile-web-app-capable" content="yes">

				<style>.file-input-wrapper { overflow: hidden; position: relative; cursor: pointer; z-index: 1; }.file-input-wrapper input[type=file], .file-input-wrapper input[type=file]:focus, .file-input-wrapper input[type=file]:hover { position: absolute; top: 0; left: 0; cursor: pointer; opacity: 0; filter: alpha(opacity=0); z-index: 99; outline: 0; }.file-input-name { margin-left: 8px; }</style>

				<!-- Needs images, font... therefore can not be part of main.css -->
				<link rel="stylesheet" href="/assets/fonts/themify-icons/themify-icons.min.css">
				<!-- <link rel="stylesheet" href="/assets/fonts/weather-icons/css/weather-icons.min.css"> -->
				<!-- end Needs images -->

		<?=Assets::styles();?>

@if (Config::get('app.custom_css'))
		<style type="text/css" id="custom-css">
			<?PHP echo file_get_contents(app()->make('path.public') . '/assets/css/customized.css');?>
		</style>
@endif
		<style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style>
		
@yield('head', '')
	</head>
		<body id="page" class="app" data-custom-page="" data-off-canvas-nav="" >
				<!--[if lt IE 9]>
						<div class="lt-ie9-bg">
								<p class="browsehappy">You are using an <strong>outdated</strong> browser.</p>
								<p>Please <a href="http://browsehappy.co m/">upgrade your browser</a> to improve your experience.</p>
						</div>
				<![endif]-->

			<section id="header" class="header-container header-fixed bg-primary">
				<header class="top-header clearfix">
					<!-- Logo -->
					<div class="logo">
							<a href="/" >
								<i class="bu-b bu fa-fw"></i> myBinghamton
								<div style="font-size: 12px;line-height: 10px;text-align: right;margin-right: 27px;">Your Binghamton University Portal</div>
							</a>
					</div>

					<!-- needs to be put after logo to make it work -->
					@if (isset(Auth::user()->bnum))
					<div class="menu-button hidden-sm hidden-md hidden-lg" toggle-off-canvas="">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
					</div>
					@endif
					<div class="top-nav">
						<ul class="nav-left list-unstyled">						 
							<li class="visible-sm visible-md visible-lg">
								<div class="hi-icon-wrap hi-icon-effect-3 hi-icon-effect-3b">
									<a href="http://ssb.cc.binghamton.edu:8080/ssomanager/c/SSB" target="_blank" class="hi-icon bu-bubrain" style="float:left"><div>BU&nbsp;Brain</div></a>
                  <a href="https://mycourses.binghamton.edu/" target="_blank" class="hi-icon fa fa-book" style="float:left"><div style="font-family: sans-serif;">myCourses</div></a>
									<a href="https://secure.binghamton.edu/qpLogin/login.jsp" target="_blank" class="hi-icon bu-quikpay" style="float:left"><div>QuikPay</div></a>
								</div>
							</li>
						</ul> 
					@if (isset(Auth::user()->bnum))

						<ul class="nav-right pull-right list-unstyled hidden-xs">
							<span class="btn btn-danger btn-lg pull-left begin-editing edit-show" style="margin-top: 18px;"><i class="fa fa-times"></i> Stop Editing</span>

@if (Session::get('SuperAdmin') || (isset($group_id) && in_array ($group_id, Session::get('owned'))))
							<li class="dropdown hidden-xs edit-show layout-area">
									<a href="javascript:void(0);" data-popins="layout" class="popins" name="layout"><i class="bu-6-6"></i></a>
							</li>
@endif	

@if (Session::get('SuperAdmin') || (isset($group_id) && in_array ($group_id, Session::get('owned'))))
		          <li class="dropdown edit-show">
									<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="ti-plus"></i>
									</a>
									<div class="dropdown-menu with-arrow panel panel-default">
											<div class="panel-heading">
													Add Widget
											</div>
											<ul class="list-group">
													<li class="list-group-item">
															<div class="add-widget" data-name="Service">
																Service
															</div>
													</li>
													<li class="list-group-item">
															<div class="add-widget" data-name="Microapp">
																Micro App
															</div>
													</li>   
													<li class="list-group-item">
															<div class="add-widget" data-name="Content">
																Content
															</div>
													</li> 
													<li class="list-group-item">
															<div class="add-widget" data-name="Image">
																Image
															</div>
													</li>  
													<li class="list-group-item">
															<div class="add-widget" data-name="Slider">
																Slide Show
															</div>
													</li> 
													<li class="list-group-item">
															<div class="add-widget" data-name="LinkCollection">
																Link Collection
															</div>
													</li>
													<li class="list-group-item">
															<div class="add-widget" data-name="Form">
																Form
															</div>
													</li>
													<li class="list-group-item">
															<div class="add-widget" data-name="Poll">
																Poll
															</div>
													</li>
													<li class="list-group-item">
															<div class="add-widget" data-name="RSS">
																RSS
															</div>
													</li>
													<li class="list-group-item">
															<div class="add-widget" data-name="Html">
																Raw HTML
															</div>
													</li>
											</ul>
									</div>
							</li>
@endif	

@if (isset($composites) && count($composites) )
							<li class="dropdown hidden-xs">
									<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti-user"></i></a>
									<ul class="dropdown-menu with-arrow  pull-right list-langs" role="menu">
										<li>
											<a href="javascript:void(0);" class="view-as" data-group="all"> All</a>
										</li>
										@foreach ($composites as $composite)
										<li>
											<a href="javascript:void(0);" class="view-as group_{{ $composite->id }}" data-group="{{ $composite->id }}"><i class="fa fa-check text-success"></i> {{ $composite->name }}</a>
										</li>
										@endforeach
									</ul>
							</li>
@endif		


@if (Session::get('SuperAdmin') || count (Session::get('owned')))
							<li class="dropdown hidden-xs">
									<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti-settings"></i></a>
									<ul class="dropdown-menu with-arrow  pull-right list-langs" role="menu">
@if (Session::get('SuperAdmin') || (isset($group_id) && in_array ($group_id, Session::get('owned'))))
										<li>
											<a href="javascript:void(0);" class="begin-editing edit-hide"> Edit</a>
										</li>									
										<li>
											<a href="javascript:void(0);" class="mobile-layout"> Mobile Layout</a>
										</li>
@endif		
										<li>
											<a href="/admin"> Control Panel</a>
										</li>
									</ul>
							</li>
@endif		


							<li class="dropdown text-normal nav-profile">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<!-- 										<img alt="" class="img-circle img30_30">
		-->
									<span class="hidden-xs">
										@if (isset(Auth::user()->bnum))

										<span ><?=Auth::user()->first_name.' '.Auth::user()->last_name;?> <i class="fa fa-chevron-down" style="font-size: 12px"></i></span>
										@endif
									</span>
								</a>
								<ul class="dropdown-menu with-arrow pull-right">
					<!-- 				<li>
										<a href="/myprofile#/mygroups">
											<i class="ti-user"></i>
											<span data-i18n="My Profile">My Profile</span>
										</a>
									</li> -->
									<li>
										<a href="/logout">
												<i class="ti-export"></i>
												<span data-i18n="Log Out">Log Out</span>
										</a>
									</li>
								</ul>
							</li>
						</ul>
						@endif
					</div>

				</header>
			</section>

				<div class="main-container">
					<aside id="nav-container" class="nav-container nav-fixed nav-horizontal bg-white">        
						<div class="nav-wrapper">
								<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
									<ul id="nav" class="nav" style="overflow: auto; width: auto; height: 100%;">
										{{ $mainMenu or ''}}
									</ul>
								<div class="slimScrollBar" style="width: 7px; position: absolute; top: 144px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 103.183894230769px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
						</div>
					</aside>
						<div id="content" class="content-container">
								@yield('content')
				</div>
		<footer class="footer">&copy; {{date('Y')}} Binghamton University, State University of New York</footer>
		<?= Assets::scripts(); ?>
		<?= Assets::templates(); ?>

		<script>
			(function($) {

			if(typeof authenticated !== 'undefined' && typeof pageID !== 'undefined' && !authenticated){
				pagePreferences = Lockr.get('/page_preference/' + pageID) || pagePreferences;
			}
				$('.nav-container .nav li a').on('click', function(e){
				if($('.slimScrollDiv').height()>42){
					$('.nav-container .nav li ul.active').removeClass('active');
					$('.nav-container .nav li.active').removeClass('active');
					$(e.currentTarget).siblings('ul').toggleClass('active');
					$(e.currentTarget).parent('li').toggleClass('active');
				}
					// $('.app>.main-container>.nav-container').toggleClass('showMenu');//.toggleClass('nav-horizontal nav-vertical');

				// .nav-container .nav li ul
				});
				if(typeof groupID !== 'undefined'){
					$('.community_'+groupID).closest('ul').toggleClass('active').parent().addClass('currentPageContainer')
				}
			})(jQuery);

			load = {{ isset($html) ? 'false' : 'true' }};
			$(function(){
				if(typeof pageID !== 'undefined'){
					$.ajax({
							url: '/visits',
							type: 'post',
							data: {path: window.location.pathname, referrer: document.referrer, width: window.innerWidth, id: pageID},
							success: function(){}
					});
					if(window.getComputedStyle($('.hidden-xs')[0]).display == 'none'){
						pageLayout = 4;
						pageData = [].concat.apply([],pageData)
						if(typeof mobile_order !== 'undefined'){
							pageData = _.sortBy(pageData, function(o) {
								return mobile_order.indexOf(o.guid);
							})
						}
						pageData = [pageData];
					}
					if(load){
						fullPage = false;
						$('body').toggleClass('editing', editor)
						if(typeof Cobler == 'undefined'){	
							wf = $('.generated-content').widget_factory()
							wf.load(pageData, pageLayout);
						}else{
							renderPage();

						}
					}

				}
			});

		</script>

@if (isset($composites) && count($composites) )
		<style>
@foreach ($composites as $composite)
			.group_{{ $composite->id }} .widget,.group_{{ $composite->id }} .page-menu ul li{display:none;}
			.group_{{ $composite->id }} .widget.group_all,.group_{{ $composite->id }} .widget.group_{{ $composite->id }}{display: block;}

			.group_{{ $composite->id }} .page-menu ul li.group_all,.group_{{ $composite->id }} .page-menu ul li.group_{{ $composite->id }}{display: table-cell}
			.view-as.group_{{ $composite->id }} i{display: none;}
			.group_{{ $composite->id }} .view-as.group_{{ $composite->id }} i{ display:inline;}
@endforeach

		</style>

		<script>
			window.onload = function(){
				$('.page-menu ul li').each(function(my, item){
						var groups =($(item).data('groups')+"").split(',')
						for(var i in groups){
							$(item).addClass('group_'+groups[i]);
						}
					
				})
				groups = <?=json_encode(Session::get('groups'))?>;
				for(var i in groups){
					$('.group_'+groups[i]).click();
				}
			}
		</script>
@endif



@if (!Config::get('app.debug'))

		<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '', 'auto');
  ga('send', 'pageview');

</script>

<script async="async" src="https://connect.binghamton.edu/ping">/**/</script>
@endif
</body></html>