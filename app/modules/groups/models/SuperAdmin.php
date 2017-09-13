<?php

class SuperAdmin extends BaseModel {
	protected $fillable = [];
	protected $primaryKey  = array('pidm', 'app');

	// public function group() {
	// 	return $this->belongsToMany('Group');
	// }
	public function user() {
		return $this->belongsTo('User', 'pidm');
	}


	public static function boot()
	{
		parent::boot();

		static::creating(function($item)
		{
			return validate::isSuper();
		});

		static::updating(function($item)
		{
			return validate::isSuper();
		});

		static::deleting(function($item)
		{
			return validate::isSuper();
		});
	}

}