<?php
class Endpoint extends BaseModel {
	protected $fillable = ['name', 'target', 'authtype', 'username', 'group_id'];
	protected $hidden = ['password', 'updated_at', 'created_at'];
}