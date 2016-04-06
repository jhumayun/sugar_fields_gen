<?php
  function p_arr($arr,$prefix=NULL)
  {
    if(is_null($prefix))
    {
      echo "<pre>".print_r($arr,1)."</pre>";
    }
    else
    {
      echo "<pre>$prefix:".print_r($arr,1)."</pre>";
    }
  }
?>
