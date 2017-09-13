<?php

class GroupKey extends BaseModel {
	protected $fillable = ['group_id', 'name', 'value'];
	// protected $primaryKey  = array('key_id', 'group_id');

	public function group() {
		return $this->belongsTo('Group');//->withPivot('messaging_pref', 'membership_status');
	}
	// public function key() {
	// 	return $this->belongsTo('Group', 'key_id');
	// }
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