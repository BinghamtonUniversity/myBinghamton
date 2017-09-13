<?php

class CommunityPage extends BaseModel {
	protected $fillable = ['content','slug', 'name', 'layout', 'group_id', 'unlist', 'device', 'mobile_order', 'meta_updated_at', 'public'];
	// protected $primaryKey  = 'id';//array('group_id', 'slug');
	public $hidden = ['created_at'];

	public function group() {
		return $this->belongsTo('Group');//->withPivot('messaging_pref', 'membership_status');
	}	
	public function visits() {
		return $this->hasMany('Visit', 'pageid');//->withPivot('messaging_pref', 'membership_status');
	}

  public static function boot()
  {
    parent::boot();

    static::saving(function($model){
    	// dd('here');
      if($model->isDirty('name') || 
      	$model->isDirty('order') || 
      	$model->isDirty('groups') || 
      	$model->isDirty('device') || 
      	$model->isDirty('unlist')
      ){
        $model->meta_updated_at = date('Y-m-d H:i:s');
      }
  	});
  }
}