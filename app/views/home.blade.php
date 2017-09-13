@extends('layouts.main')

@section('head')
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
			editable = '{{ Session::get('SuperAdmin') or in_array ( $group_id , Session::get('owned') )	}}';
			editor = false;
			authenticated = @if (isset(Auth::user()->bnum))true || @endif false;
			editUrl = {{ isset($editUrl) ? $editUrl : '""' }};
			services = {{ isset($services) ? $services : '[]' }};
			microapps = {{ isset($microapps) ? $microapps : '[]' }};
			tags = {{ isset($tags) ? json_encode($tags) : '{}' }};
			composites = {{ isset($composites) ? json_encode($composites) : '[]' }};

		</script>
@stop
@section('content')
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
@stop