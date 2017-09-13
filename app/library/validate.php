<?php

class validate {
  protected static $assertedSuper;
  public static function hasPermission($app, $group_admin, $group_member, $owner = false, $public = false) {
    if(Auth::user() == NULL){return $public;}
    $result = false;
    if($app !== false) {
      $result = ($result || validate::isSuper($app));
    };
    if($group_admin !== false) {
      $result = ($result || validate::isAdmin($group_admin));
    };
    if($group_member !== false) {
      $result = ($result || validate::isMember($group_member));
    };
    if($owner !== false) {
      $result = ($result || validate::isOwner($owner));
    };
    return $result;
  }

  public static function assertSuper($app = 'Portal') {
    static::$assertedSuper = $app;
  }
  public static function isAny() {
    return (Session::get('SuperAdmin') || (count(Session::get('owned')) > 0) || (count(Session::get('groups')) >0));
  }
  public static function isSuper($app = 'Portal') {
    if(isset(static::$assertedSuper)){
      return static::$assertedSuper;
    }
    return Session::get('SuperAdmin');
  }
  public static function isAdmin($group) {
    return in_array ( $group, Session::get('owned') );
  }

  public static function isAnyAdmin() {
    return (count(Session::get('owned')) > 0);
  }

  public static function isMember($group) {
    $state = ($group === Config::get('app.global_group') || in_array ( $group, Session::get('groups') ));
    //check for composit groups
    if(!$state) {
      $state = !empty(array_intersect(GroupComposite::where('group_id', '=', $group)->lists('composite_id'), Session::get('groups')));
    }
    return $state;
  }


  public static function isOwner($pidm) {
    return (Auth::user()->pidm == $pidm);
  }
}

