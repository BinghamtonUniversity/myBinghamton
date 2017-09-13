<?php

class GroupMember extends BaseModel {
	protected $fillable = ['messaging_pref', 'group_id', 'pidm'];
	protected $primaryKey  = array('pidm', 'group_id');

	public function group() {
		return $this->belongsTo('Group');//->withPivot('messaging_pref', 'membership_status');
	}
	public function user() {
		return $this->belongsTo('User', 'pidm');
	}

	public static function boot()
	{
		parent::boot();

		static::creating(function($item)
		{
			return (validate::isSuper() || validate::isAdmin($item->group_id) || validate::isOwner($item->pidm));
		});

		static::updating(function($item)
		{
			return (validate::isSuper() || validate::isAdmin($item->group_id) || validate::isOwner($item->pidm));
		});

		static::deleting(function($item)
		{
			return (validate::isSuper() || validate::isAdmin($item->group_id) || validate::isOwner($item->pidm));
		});
	}
}