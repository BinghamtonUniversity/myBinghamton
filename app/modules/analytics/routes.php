<?php
// Route::resource('/analytics', 'AnalyticsController');
// Route::get('/analytics_data', 'AnalyticsController@store');
// Route::get('/group_visits_per_day', 'AnalyticsController@group_visits_per_day');
// Route::get('/page_visits_per_day', 'AnalyticsController@page_visits_per_day');
// Route::get('/page_visits_per_group', 'AnalyticsController@page_visits_per_group');
// Route::get('/page_visits_per_group_all', 'AnalyticsController@page_visits_per_group_all');

// Route::resource('/analytics_group_data', 'AnalyticsGroupController');

Route::group(array('before' => 'permission:adminorhigher'), function() {
	Route::get('/analytics/group/{group_id}', 'AnalyticsGroupController@get_data');
});