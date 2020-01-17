
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<title>Config</title>
</head>
<body>
<h1>
<?php
/**
 * Created by PhpStorm.
 * User: abb
 * Date: 16/01/20
 * Time: 19:03
 */
$root = '';
include "htdocs/conf/conf.php";
define('DOL_DOCUMENT_ROOT', $dolibarr_main_document_root);			// Filesystem core php (htdocs)
$path = DOL_DOCUMENT_ROOT . "/conf";
$action = !empty($_POST['action'])?$_POST['action']:'';
if ($action == 'setconf') {
    if (!copy("{$path}/{$_POST['file']}", "{$path}/conf.php")) {
        echo "La copie du fichier a échoué...\n";
        exit;
    }
    print "Configuration changée avec succès...<br><br>";
}
$files = array();
if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry == 'conf.php') {
            $lines = file("$path/conf.php");
            foreach ($lines as $line) {
                if (preg_match("/^(.*)main_db_name='(.*)'(.*)$/i", $line, $reg)) {
                    $database = $reg[2];
                    break;
                }
            }
        }
        elseif ($entry != "." && $entry != ".." and substr($entry, -4) == '.php') {
            $files[] = $entry;
        }
    }
    closedir($handle);
}
print ("Choisir une config.:");
print '<form method="post" >';
print '<input type="hidden" name="action" value="setconf">';
print '<select name="file">';
foreach ($files as $key => $file) {
    print "<option value='$file'";
    if (strchr($file, $database)) {
        print 'selected';
    }
    print ">" . substr($file, 0, -4) . "</option>";
}
print '</select>';
print '<input type="submit" class="button valignmiddle" value="Modifier">';
print '</form>';
?>
</h1>
</body>
</html>