<?php

class GroupComposite extends BaseModel {
	protected $fillable = ['group_id', 'composite_id'];
	protected $primaryKey  = array('composite_id', 'group_id');

	public function group() {
		return $this->belongsTo('Group');//->withPivot('messaging_pref', 'membership_status');
	}
	public function composite() {
		return $this->belongsTo('Group', 'composite_id');
	}
	// public function composites() {
	// 	return $this->hasMany('GroupComposite');
	// }
	// public function group() {
	// 	return $this->belongsTo('Group');//->withPivot('messaging_pref', 'membership_status');
	// }

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
			return validate::isSuper();
		});
	}
}