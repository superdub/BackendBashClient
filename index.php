<?php
   include_once 'controller/Server.php';
   include_once 'model/BashInfo.php';
   $server = new Server(BashInfo::getCountQuotes());

