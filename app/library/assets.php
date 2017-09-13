<?php
class Meta{
  private static $metas = array();
  public static function add($name, $content=""){
    static::$metas[$name] = array('content'=>$content);
  }
  public static function data(){
    $return_val = null;
    foreach (static::$metas as $name=>$content){
      $return_val .=  '<meta name="'.$name.'" content="'.$content['content'].'">'."\n";
    }
    return $return_val;
  }
}
class Assets
{
  private static $script_files = array();
  private static $css_files = array();
  private static $template_files = array();

  public static function import($groupingname, $path="groupings/"){
    include_once($path.$groupingname.'.php');
  }

  public static function add($filename, $path=NULL) {
    if(is_dir(app_path().$path.$filename)) {
      $files_list = scandir(app_path().$path.$filename);
      foreach($files_list as $file){
        if(pathinfo($file, PATHINFO_EXTENSION) == 'mustache') {
          static::add_template($filename.$file, $path);
        } 
      }
    }elseif(is_dir(public_path().$path.$filename)){
      $files_list = scandir(public_path().$path.$filename);
      foreach($files_list as $file){
        if(pathinfo($file, PATHINFO_EXTENSION) == 'js') {
          static::add_script($filename.$file, $path);
        } elseif(pathinfo($file, PATHINFO_EXTENSION) == 'css' || pathinfo($file, PATHINFO_EXTENSION) == 'php'){
          static::add_style($filename.$file, $path);
        }
      }
    }else{
      if(pathinfo($filename, PATHINFO_EXTENSION) == 'js') {
        if($path == NULL){$path = "/assets/js/";}
        static::add_script($filename, $path);
      } elseif(pathinfo($filename, PATHINFO_EXTENSION) == 'mustache') {
        if($path == NULL){$path = "/views/";}
        static::add_template($filename, $path);
      } elseif(pathinfo($filename, PATHINFO_EXTENSION) == 'css' || pathinfo($filename, PATHINFO_EXTENSION) == 'php'){
        if($path == NULL){$path = "/assets/css/";}
        static::add_style($filename, $path);
      }
    }
  }

  public static function clear($filename, $path=NULL) {
    static::$script_files = array();
    static::$css_files = array();
    static::$template_files = array();
  }

  public static function templates($template='script' ){
    $return_val = null;
    foreach (static::$template_files as $filename=>$path_info){
      $path = app_path().$path_info["location"].$filename;
      if(file_exists($path)){
        $return_val .= View::make($template ,[
          "name" => str_replace("/","_",str_replace(".","_",str_replace(".mustache","",$filename))) , 
          "content" => preg_replace('/^\s+|\n|\r|\s+$/m', '', file_get_contents($path))
          ]);
      }
    }
    return $return_val;
  }

  public static function list_templates($template='script' ){
    $return_val = array();
    foreach (static::$template_files as $filename=>$path_info){
      $path = app_path().$path_info["location"].$filename;
      if(file_exists($path)){
        $return_val[] = $path;
      }
    }
    return $return_val;
  }

  public static function add_template($filename, $location="/views/"){
    static::$template_files[$filename] = array('location'=>$location);
  }

  public static function add_templates($path, $location="/views/"){
    $files_list = scandir(app_path().$location.$path);
    foreach($files_list as $filename){
      if(pathinfo($filename, PATHINFO_EXTENSION) == 'mustache'){
        static::add_template($path.$filename,$location);
      }
    }
  }


  public static function add_script($filename, $location="/assets/js/"){
    static::$script_files[$filename] = array('location'=>$location);
  }

  public static function scripts(){
    $return_val = null;
    foreach (static::$script_files as $filename=>$path_info){
      $return_val .=  "<script type='text/javascript' src='".$path_info["location"].$filename."'></script>\n";
    }
    return $return_val;
  }

  public static function add_style($filename, $location="/assets/css/"){
    static::$css_files[$filename] = array('location'=>$location);
  }

  public static function styles(){
    $return_val = null;
    $theme = "";
    if(isset($_GET['theme'])){$theme = "?theme=".$_GET['theme'];}
    foreach (static::$css_files as $filename=>$path_info){
      $return_val .=  "<link rel='stylesheet' type='text/css' href='".$path_info["location"].$filename.$theme."'>\n";
    }
    return $return_val;
  }
}
