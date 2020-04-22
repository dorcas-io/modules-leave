<?php

Route::group(['namespace' => 'Dorcas\ModulesPeopleLeaves\Http\Controllers', 'middleware' => ['web', 'auth'], 'prefix' => 'mpe'], function () {
    Route::get('leave-main', 'ModulesPeopleLeavesController@index')->name('leave-main');

    Route::get('leave-types', 'ModulesPeopleLeavesController@typesIndex')->name('leave-types-main');
    Route::post('leave-types', 'ModulesPeopleLeavesController@createLeaveType')->name('leave-types');
    Route::get('leave-types/{id}', 'ModulesPeopleLeavesController@singleLeaveType')->name('leave-types');
    Route::put('leave-types/{id}', 'ModulesPeopleLeavesController@updateLeaveType')->name('leave-types');
    Route::delete('leave-types/{id}', 'ModulesPeopleLeavesController@deleteLeaveType')->name('leave-types');
    Route::get('leave-type-search', 'ModulesPeopleLeavesController@searchLeaveTypes')->name('leave-type-search');

    Route::get('leave-groups', 'ModulesPeopleLeavesController@groupsIndex')->name('leave-groups-main');
    Route::get('leave-groups/create', 'ModulesPeopleLeavesController@leaveGroupsForm')->name('leave-groups-create');
    Route::post('leave-groups', 'ModulesPeopleLeavesController@createLeaveGroup')->name('leave-groups');
    Route::get('leave-groups/{id}', 'ModulesPeopleLeavesController@singleLeaveGroup')->name('leave-groups');
    Route::get('leave-groups/update/{id}', 'ModulesPeopleLeavesController@singleLeaveGroup')->name('leave-groups-single');
    Route::post('leave-groups-update/{id}', 'ModulesPeopleLeavesController@updateLeaveGroup')->name('leave-groups-update');
    Route::delete('leave-groups/{id}', 'ModulesPeopleLeavesController@deleteLeaveGroup')->name('leave-groups');
    Route::get('leave-group-search', 'ModulesPeopleLeavesController@searchLeaveGroups')->name('leave-group-search');



    Route::get('leave-request', 'ModulesPeopleLeavesController@requestIndex')->name('leave-request-main');
    Route::get('leave-request/create', 'ModulesPeopleLeavesController@leaveRequestForm')->name('leave-request-create');
    Route::post('leave-request', 'ModulesPeopleLeavesController@createLeaveRequest')->name('leave-request');
    Route::get('leave-request/{id}', 'ModulesPeopleLeavesController@singleLeaveRequest')->name('leave-request');
    Route::get('leave-request/single/{id}', 'ModulesPeopleLeavesController@getLeaveRequest')->name('leave-request-single');
    Route::post('leave-request/update/{id}', 'ModulesPeopleLeavesController@updateLeaveRequest')->name('leave-request-update');
    Route::delete('leave-request/{id}', 'ModulesPeopleLeavesController@deleteLeaveRequest')->name('leave-request');
    Route::get('leave-request-search', 'ModulesPeopleLeavesController@searchLeaveRequest')->name('leave-request-search');

});
