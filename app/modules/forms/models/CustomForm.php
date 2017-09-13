<?php
class CustomForm extends BaseModel {
	protected $fillable = ['name', 'fields', 'options', 'group_id', 'gs_id', 'target'];

	public function submissions() {
		return $this->hasMany('CustomFormSubmission');
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