<?php
class PollSubmission extends BaseModel {
	protected $fillable = ['poll_id', 'choice'];
	public function poll() {
		return $this->belongsTo('Poll');
	}

}