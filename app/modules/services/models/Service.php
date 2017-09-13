<?php
class Service extends BaseModel {
	protected $fillable = ['id', 'name', 'template', 'css', 'script', 'path', 'data_type', 'group_id'];
	// protected $primaryKey  = 'id';

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