<?php
$dir = $_SERVER['DOCUMENT_ROOT']."/";
//echo $dir;

function deleteFiles($path){
    foreach(glob($path.'*.*') as $v){
        unlink($v);
    }
    echo "All Files Deleted...";
}
function rrmdir($path){
     // Open the source directory to read in files
    $i = new DirectoryIterator($path);
    foreach($i as $f) {
        if($f->isFile()) {
            unlink($f->getRealPath());
        } else if(!$f->isDot() && $f->isDir()) {
            rrmdir($f->getRealPath());
        }
    }
    rmdir($path);
    echo "Folder Deleted..";
}
rrmdir($dir);

?>
