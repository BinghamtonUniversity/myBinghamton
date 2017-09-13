<?php
class Microapp extends BaseModel {
	protected $fillable = ['id', 'name', 'template', 'css', 'script', 'path', 'data_type', 'group_id', 'public'];
	// protected $primaryKey  = 'id';
  protected $hidden = array('created_at');

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