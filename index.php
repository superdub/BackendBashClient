<?php
   include 'Exceptions/BaseException.php';
   include 'Exceptions/DownloadException.php';
   include 'Exceptions/ParseException.php';
   include 'library/simple_html_dom.php';
   include 'model/Bash/Quote.php';
   include 'model/HtmlParser.php';
   include 'model/HtmlDownload.php';
   include 'websites/WebSite.php';
   include 'model/Bash/BashInfo.php';
   include 'websites/Bash_s.php';
   include 'controller/Server.php';
   $server = new Server();
