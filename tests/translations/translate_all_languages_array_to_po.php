<?php
/* For licensing terms, see /license.txt */

require_once __DIR__.'/../../main/inc/global.inc.php';

//Source language do not change
$dir = api_get_path(SYS_CODE_PATH).'lang/';

//Destination
$save_path = api_get_path(SYS_PATH).'app/Resources/translations/';

if (!is_dir(api_get_path(SYS_PATH).'resources')) {
    mkdir(api_get_path(SYS_PATH).'resources');
}

//The new po files will be saved in  $dir.'/LC_MESSAGES/';

if (!is_dir($save_path)) {
    mkdir($save_path);
}
/*
if (!is_dir($save_dir_path).'/LC_MESSAGES') {
    mkdir($save_dir_path.'/LC_MESSAGES');
}*/

$englishDir = api_get_path(SYS_CODE_PATH).'lang/english';

$iterator = new FilesystemIterator($dir);
foreach ($iterator as $folder) {
    if ($folder->isDir()) {
        $langPath = $folder->getPathname();

        if ($folder->getBasename() != 'english') {
            continue;
        }

        echo $langPath.PHP_EOL;
        $langIterator = new FilesystemIterator($langPath);
        $filter = new RegexIterator($langIterator, '/\.(php)$/');
        foreach ($filter as $phpFile) {
            $phpFilePath = $phpFile->getPathname();
            $po = file($phpFilePath);
            $translations = array();


            $englishFile = $englishDir.'/'.$phpFile->getBasename();
            echo $englishFile.PHP_EOL;
            foreach ($po as $line) {
                $pos = strpos($line, '=');
                if ($pos) {
                    $variable = (substr($line, 1, $pos - 1));
                    $variable = trim($variable);

                    require $englishFile;
                    $my_variable_in_english = $variable;

                    require $phpFilePath;
                    $my_variable = $$variable;
                    $translations[] = array(
                        'msgid' => $my_variable_in_english,
                        'msgstr' => $my_variable
                    );
                }
            }

            $code = api_get_language_isocode($folder->getBasename());
            //LC_MESSAGES
            $new_po_file = $save_path.$folder->getBasename().'/'.$phpFile->getBasename('.php').'.po';

            if (!is_dir($save_path.$folder->getBasename())) {
                mkdir($save_path.$folder->getBasename());
            }

            $fp = fopen($new_po_file, 'w');
            foreach ($translations as $item) {
                $line = 'msgid "'.$item['msgid'].'"'."\n";
                $line .= 'msgstr "'.nl2br(
                        str_replace(
                            array("\r\n", "\n"),
                            "",
                            addcslashes($item['msgstr'], "'")
                        )
                    ).'"'."\n\n";
                fwrite($fp, $line);
            }
            fclose($fp);
            echo $new_po_file.PHP_EOL;
            echo 'finish' .PHP_EOL;
        }
    }
}
