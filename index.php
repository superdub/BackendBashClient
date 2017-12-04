<?php
   include 'Exceptions/BaseException.php';
   include 'Exceptions/DownloadException.php';
   include 'Exceptions/ParseException.php';
   include 'library/simple_html_dom.php';
   include 'model/Bash/Quote.php';
   include 'model/Zadolba/Story.php';
   include 'model/Zadolba/ZadolbaInfo.php';
   include 'model/HtmlParser.php';
   include 'model/HtmlDownload.php';
   include 'websites/WebSite.php';
   include 'websites/Zadolba_s.php';
   include 'model/Bash/BashInfo.php';
   include 'websites/Bash_s.php';
   include 'controller/Server.php';

  // $commands = $_GET['commands'];
   //$parameters = $_GET['parameters'];
   $server = new Server();

   //echo $parameters;

   //$server->HandlerUser($commands,$parameters);
