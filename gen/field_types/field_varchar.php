<?php
require_once 'gen/Field.php';

class field_varchar extends Field{

  public function __construct($csv_data,$lang_code,$generate_vardef=TRUE)
  {
    parent::__construct($csv_data,$lang_code,$generate_vardef);
  }

}
