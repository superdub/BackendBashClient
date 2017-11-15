<?php
   include 'library/simple_html_dom.php';
   include 'model/HtmlParser.php';
   include 'model/HtmlDownload.php';
   include 'model/BashInfo.php';
   include 'model/BashQuotes.php';
   include 'websites/Bash_s.php';
   include 'controller/Server.php';
   $server = new Server(BashInfo::getCountQuotes());
