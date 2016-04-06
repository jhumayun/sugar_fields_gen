<?php
require_once 'gen/Field.php';

class field_enum extends Field{

  public function __construct($csv_data)
  {
    parent::__construct($csv_data);
    $this->fill_dropdown();
  }

  private function fill_dropdown()
  {
    $tpl_path = 'templates/dropdown.txt';
    $content = $this->dropdown_tpl_content_replace($tpl_path);
    // Write the contents to the file,
    // using the FILE_APPEND flag to append the content to the end of the file
    // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
    file_put_contents($this->dropdown_file_path, $content, FILE_APPEND | LOCK_EX);
  }

  private function dropdown_tpl_content_replace($tpl_path)
  {
    $output = '';
    foreach($this->name_value_list as $name=>$val)
    {
      $content = file_get_contents($tpl_path);
      $content = str_replace('<option_list_name>',$this->options,$content);
      $content = str_replace('<option_name>',$name,$content);
      $content = str_replace('<option_value>',$val,$content);

      $output .= $content;
    }

    return $output;
  }

}
