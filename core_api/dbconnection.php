<?php
ob_start();
$serverUserName = $_SERVER["HTTP_HOST"] == "localhost" ? "root" : "app_jhoice";
$serverPassword = $_SERVER["HTTP_HOST"] == "localhost" ? "" : "2I02bvg?";
$database = $_SERVER["HTTP_HOST"] == "localhost" ? "app_jhoice" : "app_jhoice";
$server = $_SERVER["HTTP_HOST"] == "localhost" ? "localhost" : "localhost";
$connection = mysqli_connect($server, $serverUserName, $serverPassword) or die("Database Connection Failed.");

if(!$connection)
{
    die("Database Connection Failed : " . mysqli_error($connection));
}
else
{
    mysqli_select_db($connection, $database) or die("Database Selection Failed : " . mysqli_error($connection));
}
?>