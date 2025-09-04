#!/usr/bin/env php
<?php
$logdir = "log";
if(!file_exists("$logdir/autobuild.end")){
echo "Error: Wrong log directory!\n";
exit;
}


class ScriptLog {
    public $name;
    public $time;
    public $files;
    public $start;
    public $end;
}

function fileslist($dir){
$res=array();
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
           if(!in_array($file,array(".",".."))){
		$res[]=$file;
           }
        }
        closedir($dh);
    }
}


return $res;
}

function seconds2time($time) {
 $h=$time/3600;
 $m=($time%3600)/60;
 $s=$time%60;
 return sprintf ("%02d:%02d:%02d", $h,$m,$s);
}



$_scripts1=fileslist('scripts');

//var_dump($_scripts);
$_scripts2=fileslist('cscripts');
$_scripts=array_merge($_scripts1,$_scripts2);
sort($_scripts);

//var_dump($_scripts);



$scripts=array();
foreach ($_scripts as $script){
    $obj=new ScriptLog();
    $obj->name=$script;
    $obj->start=intval(trim(file_get_contents($logdir."/".$script.".start")));
    $obj->end=intval(trim(file_get_contents($logdir."/".$script.".end")));
    $obj->time=($obj->end)-($obj->start);
    $obj->files=count(File($logdir."/".$script.".files"));
    $scripts[]=$obj;
}

//var_dump($scripts);
$start=intval(trim(file_get_contents($logdir."/autobuild.start")));
$end=intval(trim(file_get_contents($logdir."/autobuild.end")));
$buildtime=$end-$start;

echo "Umvirt LFS Auto Builder \n";
echo "======================= \n";
echo "Disk image build report\n";
echo "-----------------------\n\n";
echo "Scripts executed:\n";
$c=0;
foreach ($scripts as $script){
$c++;
echo $c."\t".str_pad($script->name,20)."\t".seconds2time($script->time)."\t".$script->files."\n";
}

echo "\n\n";

echo "Scripts executed: ".count($scripts)."\n";
echo "Total build time: ".seconds2time($buildtime)."\n";


