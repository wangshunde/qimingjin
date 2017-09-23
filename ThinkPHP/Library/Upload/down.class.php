<?php
namespace Upload;
class down{
	public function load($info){
		import("Upload.ALIOSS");
	    $oss_sdk_service = new \Upload\ALIOSS(); 
	    $bucket = 'djkphoto'; 
	    $timeout = 3600;
	    $file_name = $info;  
	    $response = $oss_sdk_service->get_sign_url($bucket,$file_name,$timeout);

	    return $response;
	}
}