#!/usr/bin/env php
<?php
//echo "Ok";
$data=File("books/wget-list");
foreach ($data as $row){
$x=explode("/",trim($row));

echo($x[count($x)-1]."\n");
}