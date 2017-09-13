<?php

class PagePreference extends BaseModel {
	protected $fillable = ['content', 'pidm', 'page_id'];
	protected $primaryKey  = array('page_id', 'pidm');

	// public function group() {
	// 	return $this->belongsTo('Group', 'group_id', 'group_id');//->withPivot('messaging_pref', 'membership_status');
	// }
	public function user() {
		return $this->belongsTo('User', 'pidm');
	}

  public static function boot()
  {
    parent::boot();

    static::updating(function($item)
    {
    	return validate::isOwner($item->pidm);
    });
  }
}
