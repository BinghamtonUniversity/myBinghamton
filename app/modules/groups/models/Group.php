<?php
class Group extends BaseModel {
	protected $fillable = ['name','slug', 'type', 'community_flag', 'priority', 'composites'];
	// protected $primaryKey  = 'id';
	// public $incrementing = false;
	// protected $hidden = ['updated_at', 'created_at'];
	protected $hidden = ['created_at'];   
	protected $softDelete = true;


	public function members() {
		return $this->hasMany('GroupMember');
	}

	public function membersCount()
	{
	  return $this->hasOne('GroupMember')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}
	 
	// public function getMembersCountAttribute()
	// {
	//   // if relation is not loaded already, let's do it first
	//   if ( ! array_key_exists('membersCount', $this->relations)) 
	//     $this->load('membersCount');
	 
	//   $related = $this->getRelation('membersCount');
	 
	//   // then return the count directly
	//   return ($related) ? (int) $related->aggregate : 0;
	// }

	public function adminsCount()
	{
	  return $this->hasOne('GroupAdmin')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}
	 
	// public function getAdminsCountAttribute()
	// {
	//   // if relation is not loaded already, let's do it first
	//   if ( ! array_key_exists('adminsCount', $this->relations)) 
	//     $this->load('adminsCount');
	 
	//   $related = $this->getRelation('adminsCount');
	 
	//   // then return the count directly
	//   return ($related) ? (int) $related->aggregate : 0;
	// }

	public function tags() {
		return $this->hasMany('GroupKey');
	}

	public function composites() {
		return $this->hasMany('GroupComposite');
	}

	public function pages() {
		return $this->hasMany('CommunityPage');
	}

	public function admins() {
		return $this->hasMany('GroupAdmin');
	}



	public function images() {
		return $this->hasMany('Image');
	}

	public function imagesCount()
	{
	  return $this->hasOne('Image')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}



	public function polls() {
		return $this->hasMany('Poll');
	}

	public function pollsCount()
	{
	  return $this->hasOne('Poll')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}



	public function forms() {
		return $this->hasMany('CustomForm');
	}

	public function formsCount()
	{
	  return $this->hasOne('CustomForm')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}



	public function endpoints() {
		return $this->hasMany('Endpoint');
	}

	public function endpointsCount()
	{
	  return $this->hasOne('Endpoint')
	    ->selectRaw('group_id, count(*) as aggregate')
	    ->groupBy('Group_id');
	}




	public function services() {
		return $this->hasMany('Service');
	}	

	public function microapps() {
		return $this->hasMany('Microapp');
	}
	
	public function scopeIsCommunity($query)
  {
      $query->where('community_flag', '=', '1');
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
			return (validate::isSuper() || validate::isAdmin($item->id));
		});

		static::deleting(function($item)
		{
			return validate::isSuper();
		});
	}
}