<?php

class FieldFactory
{
    public static function create_field($csv_data,$lang_meta)
    {
        $field_type = $csv_data[14];
        $field_class_path = 'gen/field_types/field_'.$field_type.'.php';
        if(file_exists($field_class_path))
        {
          require_once $field_class_path;
          $field_obj_name = 'field_'.$field_type;
		  foreach($lang_meta as $lang_code => $meta)
		  {
			$field = new $field_obj_name($csv_data,$lang_code);
		  }
        }
        elseif(isset($field_type))
        {
          echo 'Class for field type "'.$field_type.'" does not exist here: '.$field_class_path.'<br/>';
        }

    }

    public static function init()
    {
      self::deleteDir('output');
      self::create_dir('output');
    }

    private static function create_dir($struct)
    {
      if(!is_dir($struct))
      {
        if (!mkdir($struct, 0777, true))
        {
            die('Failed to create folders...'.$struct);
        }
      }
    }

    private static function deleteDir($dirPath) {
        if (is_dir($dirPath)) {
          if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
              $dirPath .= '/';
          }
          $files = glob($dirPath . '*', GLOB_MARK);
          foreach ($files as $file) {
              if (is_dir($file)) {
                  self::deleteDir($file);
              } else {
                  unlink($file);
              }
          }
          rmdir($dirPath);
        }
    }
}
?>
