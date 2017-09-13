<?php
class Poll extends BaseModel {
	protected $fillable = ['poll_name', 'content', 'shuffle', 'group_id'];

	public function submissions() {
		return $this->hasMany('PollSubmission');
	}

	public function group() {
		return $this->belongsTo('Group');
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
			return (validate::isSuper() || validate::isAdmin($item->group_id));
		});
	}
}