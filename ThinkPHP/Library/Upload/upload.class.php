<?php
namespace Upload;
class upload{

  public function local($photo){
    $content = ''; 
    $length = 0; 
    $fp = fopen($photo["tmp_name"],'r'); 
    if($fp) 
    { 
        $f = fstat($fp); 
        $length = $f['size']; 
        while(!feof($fp)) 
        { 
            $content .= fgets($fp,8192); 
        } 
    } 
    $upload_file_options = array('content' => $content, 'length' => $length);    
    import("Upload.ALIOSS");
    $oss_sdk_service = new \Upload\ALIOSS(); 
    $bucket = 'djkphoto'; 
    $timeout = 3600;
    $file_name = $photo["name"]; 
    
    $upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket, $file_name, $upload_file_options);    
  }


}