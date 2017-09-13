<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
class User extends BaseModel implements UserInterface, RemindableInterface {
	protected $primaryKey  = 'pidm';
	protected $fillable = ['name'];
	protected $table = 'users';

	
	public function Groups() {
		return $this->belongsToMany('Group', 'group_members', 'pidm')->withPivot('messaging_pref', 'membership_status');
	}
	public function OwnedGroups() {
		return $this->belongsToMany('Group', 'group_admins', 'pidm')->orderBy('order', 'asc');
	}

	public function invalidate()
	{
		$this->invalidate = time();
		$this->save();
	}

	public function createIfNotExists()
	{
		$this->invalidate();
		// $this->save();
	}

	public function sync()
	{
		//$this->invalidate();
		// $this->save();
	}

		/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
	
	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

}



