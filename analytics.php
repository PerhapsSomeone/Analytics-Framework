<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 09.07.18
 * Time: 16:28
 */

require "classes.php";

if (!(isset($_GET["country"]) && isset($_GET["countrycode"]) && isset($_GET["ip"]) && isset($_GET["ineu"]) && isset($_GET["page"]))) {
    header("HTTP/1.1 400 Bad Request");
    die("<b>400 - Bad Request</b>");
}

config::setupSentry();

analytics::autoEnterVisitor();

