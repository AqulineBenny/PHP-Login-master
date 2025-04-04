<?php
session_start(); /* First we must start the session */
require_once '../src/session.php';
$session = new session();
$session->forgetSession();
?>