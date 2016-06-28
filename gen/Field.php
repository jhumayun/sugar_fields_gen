<?php
class Field
{
    protected $module_singular;
    protected $module_plural;
    protected $field_name;
    protected $field_lbl;
    protected $reportable;
    protected $required;
    protected $auditable;
    protected $importable;
    protected $searchable;
    protected $mass_update;
    protected $dependent;
    protected $default_value;
    protected $field_help;
    protected $field_comment;
    protected $field_type;
    protected $options;
    protected $range_search_enable;

    protected $lbl_file_path;
    protected $vardef_file_path;
    protected $is_dropdown;
    protected $dropdown_file_path;
		
    protected $lang_code;

    public function __construct($csv_data,$lang_code)
    {
      global $config;
	  $this->lang_code = $lang_code;
      $this->module_plural = $csv_data[0];
      $this->module_singular = $csv_data[1];
      $this->field_name = $csv_data[2];
      $this->field_lbl = str_replace('"',"'",$csv_data[$config['lang_meta'][$this->lang_code]['field_lbl_csv_index']]);
      $this->reportable = $csv_data[4];
      $this->required = $csv_data[5];
      $this->auditable = $csv_data[6];
      $this->importable = $csv_data[7];
      $this->searchable = $csv_data[8];
      $this->mass_update = $csv_data[9];
      $this->dependent = $csv_data[10];
      $this->default_value = $csv_data[11];
      $this->field_help = str_replace('"',"'",$csv_data[12]);
      $this->field_comment = str_replace('"',"'",$csv_data[13]);
      $this->field_type = $csv_data[14];
      $this->options = $csv_data[15];
      $this->name_value_list = $this->prepare_nv_list($csv_data[$config['lang_meta'][$this->lang_code]['nv_list_csv_index']]);
      p_arr($this->lang_code.' - '.$this->field_name);
	  $this->field_len = $csv_data[18];
      $this->lbl_file_path = 'output/custom/Extension/modules/'.$this->module_plural.'/Ext/Language/'.$this->lang_code.'.'.$config['labels_file_name'].'.php';
      $this->vardef_file_path = 'output/custom/Extension/modules/'.$this->module_plural.'/Ext/Vardefs/'.$config['fields_filename_prefix'].'_'.$this->field_name.'.php';
      if($this->field_type=='enum' || $this->field_type=='multienum')
      {
        $this->is_dropdown = true;
        $this->dropdown_file_path = 'output/custom/Extension/application/Ext/Language/'.$this->lang_code.'.'.$config['labels_file_name'].'.php';
      }
      elseif($this->field_type=='datetime')
      {
        $this->range_search_enable = $csv_data[17];
      }

      $this->init();
      $this->fill_vardefs();
      $this->fill_labels();
    }

    protected function prepare_nv_list($nv_string)
    {
      $nv_string = str_replace('"',"",$nv_string);
      $nv_string = str_replace("'","",$nv_string);
      $out = array();
      if($nv_string=='')
      {
        return $out;
      }
      $options = explode(',',$nv_string);
      foreach($options as $opt)
      {
        $nv = explode('=>',$opt);
        $out[$nv[0]]=$nv[1];
      }
      return $out;
    }

    protected function fill_vardefs()
    {
      $tpl_path = 'templates/types/'.$this->field_type.'.txt';
      $content = $this->tpl_content_replace($tpl_path);
      // Write the contents to the file,
      // using the FILE_APPEND flag to append the content to the end of the file
      // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
      file_put_contents($this->vardef_file_path, $content, FILE_APPEND | LOCK_EX);
    }

    protected function fill_labels()
    {
      $tpl_path = 'templates/label.txt';
      $content = $this->tpl_content_replace($tpl_path);
      // Write the contents to the file,
      // using the FILE_APPEND flag to append the content to the end of the file
      // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
      file_put_contents($this->lbl_file_path, $content, FILE_APPEND | LOCK_EX);
    }

    protected function init()
    {
      $this->make_file($this->lbl_file_path);
      $this->insert_php_tag_to_file($this->lbl_file_path);

      $this->make_file($this->vardef_file_path);
      $this->insert_php_tag_to_file($this->vardef_file_path);

      if($this->is_dropdown)
      {
        $this->make_file($this->dropdown_file_path);
        $this->insert_php_tag_to_file($this->dropdown_file_path);
      }
    }

    protected function tpl_content_replace($tpl_path)
    {
      global $config;
      $content = file_get_contents($tpl_path);
      $content = str_replace('<field_name_uppercase>',strtoupper($this->field_name),$content);
      $content = str_replace('<label_string>',$this->field_lbl,$content);
      $content = str_replace('<field_name>',$this->field_name,$content);
      $content = str_replace('<module_singular>',$this->module_singular,$content);
      $content = str_replace('<module_plural>',$this->module_plural,$content);
      $content = str_replace('<is_reportable>',$this->reportable,$content);
      $content = str_replace('<is_required>',$this->required,$content);
      $content = str_replace('<is_auditable>',$this->auditable,$content);
      $content = str_replace('<is_searchable>',$this->searchable,$content);
      $content = str_replace('<field_options>',$this->options,$content);
      $content = str_replace('<field_type>',$this->field_type,$content);
      $content = str_replace('<is_importable>',$this->importable,$content);
      $content = str_replace('<mass_update>',$this->mass_update,$content);
      $content = str_replace('<field_help>',$this->field_help,$content);
      $content = str_replace('<field_comment>',$this->field_comment,$content);
      $content = str_replace('<default_value>',$this->default_value,$content);
      $content = str_replace('<field_source>',$config['fields_source'],$content);
      $content = str_replace('<field_len>',$this->field_len,$content);
      return $content;
    }

    protected function insert_php_tag_to_file($file)
    {
      $pre_content = file_get_contents($file);
      if(strlen($pre_content)===0)
      {
        $content = "<?php\r\n\r\n";
        // Write the contents to the file,
        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
      }
    }


    protected function make_file($file_path)
    {
      if(!file_exists($file_path))
      {
        $pieces = explode('/',$file_path);
        $flie_name = end($pieces);
        $dir = '';
        $count=0;
        foreach($pieces as $folder)
        {
          $count++;
          if($count<count($pieces))
          {
            $dir.=$folder.'/';
          }
        }
        $this->create_dir($dir);
        $handle = fopen($dir.$flie_name, "w");
        fclose($handle);
      }
    }

    protected function create_dir($struct)
    {
      if(!is_dir($struct))
      {
        if (!mkdir($struct, 0777, true))
        {
            die('Failed to create folders...'.$struct);
        }
      }
    }

    protected function deleteDir($dirPath) {
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
