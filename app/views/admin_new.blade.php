@include('admin_includes')
<!DOCTYPE html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"><!--<![endif]--><head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
				<title>Portal Admin</title>
				<meta name="description" content="Portal Admin Section">
				<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">

				<style>.file-input-wrapper { overflow: hidden; position: relative; cursor: pointer; z-index: 1; }.file-input-wrapper input[type=file], .file-input-wrapper input[type=file]:focus, .file-input-wrapper input[type=file]:hover { position: absolute; top: 0; left: 0; cursor: pointer; opacity: 0; filter: alpha(opacity=0); z-index: 99; outline: 0; }.file-input-name { margin-left: 8px; }</style>
				<link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,600,300,700" rel="stylesheet" type="text/css">
				<!-- Needs images, font... therefore can not be part of main.css -->
				<link rel="stylesheet" href="/assets/fonts/themify-icons/themify-icons.min.css">
				<link rel="stylesheet" href="/assets/fonts/weather-icons/css/weather-icons.min.css">
				<!-- end Needs images -->

						
				<link rel="stylesheet" href="/assets/css/main.css">

		<?=Assets::styles();?>

		<style type="text/css"></style><style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style>

	</head>
		<body id="app" class="app">
				<section id="header" class="header-container header-fixed bg-primary">
				<header class="top-header clearfix">
				<span class="btn btn-bu pull-left smallbarToggle" style="margin-top: 9px;margin-left: 10px;"><i class="fa fa-bars"></i></span>
		<!-- Logo -->
		<div class="logo">
				<a href="/">
					<i class="bu-b bu fa-fw"></i>Admin
<!-- 						<img src="/assets/img/binghamton-university-portal-logo.png" />
 -->				</a>
 		</div>

		<!-- needs to be put after logo to make it work -->
		<div class="menu-button" toggle-off-canvas="">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
		</div>

		<div class="top-nav" style="padding-left: 220px;">
<!-- 				<ul class="nav-left list-unstyled visible-md visible-lg">

						<li class="search-box">
								<div class="input-group">
										<span class="input-group-addon"><i class="ti-filter"></i></span>
										<input type="text" class="form-control filter" name="filter" style="color:#fff" placeholder="Filter...">
								</div>
						</li>
				</ul> 
 -->
				<ul class="nav-right pull-right list-unstyled visible-md visible-lg">

						<li class="dropdown text-normal nav-profile">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<!-- <img alt="" class="img-circle img30_30"> -->
										<span class="hidden-xs">
												<span data-i18n="<?=Auth::user()->first_name.' '.Auth::user()->last_name;?>"><?=Auth::user()->first_name.' '.Auth::user()->last_name;?> <i class="fa fa-chevron-down" style="font-size: 12px"></i></span>
										</span>
								</a>
								<ul class="dropdown-menu with-arrow pull-right">
<!-- 										<li>
												<a href="/#/mygroups">
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
		</div>

</header>
</section>

				<div class="main-container">
						<aside id="nav-container" class="nav-container nav-fixed nav-vertical bg-white">        
<div class="nav-wrapper">
		<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
{{ $menu }}
		<div class="slimScrollBar" style="width: 7px; position: absolute; top: 144px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 103.183894230769px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
</div></aside>

						<div id="content" class="content-container">
							<center><i class="fa fa-spinner fa-spin" style="font-size:60px;margin:150px auto;color:#eee"></i></center>

<!-- 								<section class="view-container animate-fade-up"><div class="page">
<div id="alt-sidebar"></div>
		<section class="panel panel-default">
				<div class="panel-heading"><strong><span class="glyphicon glyphicon-th"></span> Blank Page</strong></div>
				<div class="panel-body">
						<p>Content goes here</p>
				</div>
		</section> 

</div></section> -->
						</div>
				</div>

		<?=Assets::scripts();?>
		<script>


updateMenuState(Lockr.get('menu') || false);
</script>
		{{ $script }}

		<?=Assets::templates();?>
</body></html>