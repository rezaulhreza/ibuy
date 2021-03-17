<?php
$pdo = new PDO('mysql:host=freedb.tech;dbname=freedbtech_jokesite;charset=utf8', 'freedbtech_jokesite', 'jokesite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
