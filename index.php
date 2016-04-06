<?php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

require_once "config.php";
require_once "helpers.php";
require_once "gen/FieldFactory.php";

// Open fields csv file
$file = fopen($config['fields_file'],"r");
$defs = fgetcsv($file); // first row of csv containing csv column defs

FieldFactory::init();
while(! feof($file))
{
  $csv_data = fgetcsv($file);
  FieldFactory::create_field($csv_data);
}

fclose($file);

?>
