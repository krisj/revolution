<?php
$v= array ();
$v['version']= '2'; // Current version.
<<<<<<< HEAD
$v['major_version']= '1'; // Current major version.
$v['minor_version']= '0'; // Current minor version.
$v['patch_level']= 'dev'; // Current patch level.
=======
$v['major_version']= '0'; // Current major version.
$v['minor_version']= '7'; // Current minor version.
$v['patch_level']= 'pl'; // Current patch level.
>>>>>>> 8ce2e449ca00fcde28d1968498f584f46a5b2bbc
$v['code_name']= 'Revolution'; // Current codename.
$v['distro']= '@git@';
$v['full_version']= $v['version'] . ($v['major_version'] ? ".{$v['major_version']}" : ".0") . ($v['minor_version'] ? ".{$v['minor_version']}" : ".0") . ($v['patch_level'] ? "-{$v['patch_level']}" : "");
$v['full_appname']= 'MODx' . ($v['code_name'] ? " {$v['code_name']} " : " ") . $v['full_version'] . ' (' . trim($v['distro'], '@') . ')';
return $v;
?>
