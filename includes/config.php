<?php
/**
 * Config file
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 */

define("DEBUG", true);

if (DEBUG) {
    ini_set("display_errors", E_ALL);
    error_reporting(E_ALL);
}

session_start();

date_default_timezone_set("America/Sao_Paulo");

require "quiz.class.php";