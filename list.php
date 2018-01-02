<?php
// 只显示警告和致命性错误 
error_reporting(~E_NOTICE);
// 获取微博的内容
$old_blog_content = file_get_contents('data/blog.txt');
if ($old_blog_content) {
	$data_blog=json_decode($old_blog_content,true);
	$old_blog_content=array_reverse($data_blog);
}

// 获取标签的内容
$old_tag=file_get_contents('data/tag.txt');
if($old_tag){
	$temp_tag_data=json_decode($old_tag,true);//临时的标签内容
	$tag_data=[];
    // 再生成一个键和标签ID同值的数组，为的是后面直接可以通过标签ID获取标签的名称
	foreach ($temp_tag_data as $key => $value) {
		$tag_data[$value['tid']] =$value;
	}
	unset($temp_tag_data);
}

// 获取标签中间表的内容
$serialize_str=file_get_contents('data/blog_tag.txt');
// unserialize 反序列化，将字符串转换为
if($serialize_str){
	$middle_table_list=unserialize($serialize_str);
}
 //print_r($middel_table_list);exit();
// 获取当前每一个微博的Id
// 遍历所有的标签中间表，查找和当前微博ID一样的那条记录
//生成微博对应的tag_list 标签列表
//id  标签ID
//tag_name 标签名字 
if($old_blog_content){
	foreach ($old_blog_content as $key => $value) {
	//有标签的时候才获取标签
	if(!empty($middel_table_list['a'][$value['bid']])){
		// $val 就是标签的ID
		foreach ($middel_table_list['a'][$value['bid']] as $val) {
			$old_blog_content[$key]['tag_list'][] = ['tid'=>$val,'tag_name'=>$tag_data[$val]['tag_name']]; 
		}
	}
  }
}

echo  json_encode($old_blog_content);
?>





