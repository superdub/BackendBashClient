<?php
 class HtmlDownload
 {
    private $cUrl;
     
     private function checked_url($url)
     {
        if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
         return true;
         return false;
    }
     
     function download($url)
     {
        if($this->checked_url($url))
         {
             $this->cUrl = curl_init();
             curl_setopt($this->cUrl, CURLOPT_HEADER, 0);
	         curl_setopt($this->cUrl, CURLOPT_RETURNTRANSFER,1);
	         curl_setopt($this->cUrl, CURLOPT_URL, $url);
	         $data = curl_exec($this->cUrl);
	         curl_close($this->cUrl);
	         return $data;
         }
         return null;
     }
     
   
     
     function __destruct()
     {
         if($this->cUrl != null)
         $this->cUrl = null;
     }
     
 }
