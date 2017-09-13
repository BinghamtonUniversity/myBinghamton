@include('includes')
<!DOCTYPE html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <add name="X-UA-Compatible" value="IE=edge" /><![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"><!--<![endif]-->
<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
				<title>{{ $name }} - My Binghamton</title>
				<meta name="description" content="Portal">
				<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
				<meta name="apple-mobile-web-app-capable" content="yes">
				<meta name="mobile-web-app-capable" content="yes">

				<style>.file-input-wrapper { overflow: hidden; position: relative; cursor: pointer; z-index: 1; }.file-input-wrapper input[type=file], .file-input-wrapper input[type=file]:focus, .file-input-wrapper input[type=file]:hover { position: absolute; top: 0; left: 0; cursor: pointer; opacity: 0; filter: alpha(opacity=0); z-index: 99; outline: 0; }.file-input-name { margin-left: 8px; }</style>

				<!-- Needs images, font... therefore can not be part of main.css -->
				<link rel="stylesheet" href="/assets/fonts/themify-icons/themify-icons.min.css">
				<link rel="stylesheet" href="/assets/fonts/weather-icons/css/weather-icons.min.css">
				<!-- end Needs images -->

		<?=Assets::styles();?>

@if (Config::get('app.custom_css'))
		<style type="text/css" id="custom-css">
			<?PHP echo file_get_contents(app()->make('path.public') . '/assets/css/customized.css');?>
		</style>
@endif
		<style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style>
		
		<style type="text/css">	
			@media screen and (min-width: 768px) {
				.nav>.community_{{ $group_id }} a{
					border-bottom-color: #005A43 !important;
				  /*padding-bottom: 5px !important;*/
				}
			}
			@media screen and (max-width: 767px) {
				.nav>.community_{{ $group_id }} a{
					background:#005A43 !important;
					color:#fff/*e2ea66*/ !important;
				}
				.nav>.community_{{ $group_id }} a:hover{
					color:#fff/*e2ea66*/ !important;
				}
			}
			.nav-container.bg-white .nav li ul >li.community_{{ $group_id }}>a {
			    color: #fff/*e2ea66*/;
			}
			.page_{{ $id }} a{
				/*color:#fff !important;*/
				background-color: #014634 !important;
    		color: #fff/*e2ea66*/ !important;
			}
		</style>




		<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<script>
			pageData = {{ $content }};
@if (isset($mobile_order) && $mobile_order != null)
			mobile_order = {{ $mobile_order }};
@endif
			pagePreferences = {{ $preferences or "[]"}};
			pageLayout = {{ $layout }};
			pageID = '{{ $id }}';
			groupID = '{{ $group_id }}';
			editor = false;
			editUrl = {{ isset($editUrl) ? $editUrl : '""' }};
			services = {{ isset($services) ? $services : '[]' }};
			microapps = {{ isset($microapps) ? $microapps : '[]' }};
			tags = {{ isset($tags) ? json_encode($tags) : '{}' }};
			composites = {{ isset($composites) ? json_encode($composites) : '[]' }};
		</script>
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



							<li class="dropdown text-normal nav-profile">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<!-- 										<img alt="" class="img-circle img30_30">
		-->
									<span class="hidden-xs">

										<span ><?=Auth::user()->first_name.' '.Auth::user()->last_name;?> <i class="fa fa-chevron-down" style="font-size: 12px"></i></span>
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
										{{ $mainMenu }}
									</ul>
								<div class="slimScrollBar" style="width: 7px; position: absolute; top: 144px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 103.183894230769px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
						</div>
					</aside>
						<div id="content" class="content-container" @if (!isset(Auth::user()->bnum))style="margin-top:35px"@endif>
								<section class="view-container animate-fade-up">
									<div class="page">
										@if (isset(Auth::user()->bnum))

										<div style="background:#014634;margin: -15px -15px 30px;" class="visible-xs">
											<div class="hi-icon-wrap hi-icon-effect-3 hi-icon-effect-3b" style="width:100%;height:75px">
												<a href="http://ssb.cc.binghamton.edu:8080/ssomanager/c/SSB" target="_blank" class="hi-icon bu-bubrain"></a>
                        <a href="https://mycourses.binghamton.edu/" target="_blank" class="hi-icon fa fa-book" style="margin-right:50px;margin-left:50px"></a>
												<a href="https://secure.binghamton.edu/qpLogin/login.jsp" target="_blank" class="hi-icon bu-quikpay"></a>
											</div>
										</div>

										<div class="row page-menu">
											{{ $menu }}
										</div><br>
										@endif
										<div class="row generated-content">

										{{ isset($html) ? $html : '' }}
										</div>
										<div class="hidden-xs"> </div>

									</div>
								</section>
				</div>
		<footer class="footer">&copy; {{date('Y')}} Binghamton University, State University of New York</footer>
		<?= Assets::scripts(); ?>
		<?= Assets::templates(); ?>

		<script>
			(function($) {
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
				$('.community_'+groupID).closest('ul').toggleClass('active').parent().addClass('currentPageContainer')
			})(jQuery);

			load = {{ isset($html) ? 'false' : 'true' }};
			$(function(){
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