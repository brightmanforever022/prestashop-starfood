<?php

if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    require_once ('FfProject_15.php');
else
    require_once ('FfProject_14.php');