<?php

class GroupAdmin extends BaseModel {
	protected $fillable = ['group_id', 'pidm'];
	protected $primaryKey  = array('pidm', 'group_id');

	public function group() {
		return $this->belongsToMany('Group');
	}
	public function user() {
		return $this->belongsTo('User', 'pidm');
	}

	public static function boot()
	{
		parent::boot();

		static::creating(function($item)
		{
			return (validate::isSuper() || validate::isAdmin($item->group_id));
		});

		static::updating(function($item)
		{
			return (validate::isSuper() || validate::isAdmin($item->group_id));
		});

		static::deleting(function($item)
		{
			return validate::isSuper();
		});
	}
	
}