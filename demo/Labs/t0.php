#!/usr/bin/php
<?php

require('../../../camarera/bootstrap.php');

echo 'version: ' . \Camarera\Camarera::conf('Camarera.version') . "\n";
echo 'version: ' . \Camarera\Camarera::conf('Camarera.Camarera.version') . "\n";

die('OK');
