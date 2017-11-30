<?php
 class HtmlDownload
 {
     /**
      * download html page
      */
     public static function download($url)
     {
         try {
             if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                 $cUrl = curl_init();
                 curl_setopt($cUrl, CURLOPT_HEADER, 0);
                 curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($cUrl, CURLOPT_URL, $url);
                 $data = curl_exec($cUrl);
                 curl_close($cUrl);
                 if($data == null) throw new DownloadException();
                 unset($cUrl);
                 return $data;
             }
             throw new DownloadException();
         }
         catch (DownloadException $e){echo $e->getMessage();}
     }
 }
