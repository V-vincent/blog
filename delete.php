<?php

error_reporting(~E_NOTICE);
$old_blog_content = file_get_contents('data/blog.txt');

$data_blog=json_decode($old_blog_content,true);
foreach ($data_blog as $key => $value) {
	if ($value['bid']==$_POST['id']) {
		unset($data_blog[$key]);
	}
}

file_put_contents('data/blog.txt', json_encode($data_blog));
echo  json_encode(['status'=>1]);
?>