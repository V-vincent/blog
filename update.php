<?php 
error_reporting(~E_NOTICE);
 // 获取传过来的信息
$update_info = json_decode($_POST['info'],true);


 // 获取旧的信息
$old_blog_content = file_get_contents('data/blog.txt');
// 把旧的信息json字符串转换数组
$data_blog=json_decode($old_blog_content,true);

foreach ($data_blog as $key => $value) {
	// 判断是不是当前被编辑的微博
	if ($value['bid']==$update_info['bid']) {
		if ($update_info['blog_type'] == 'pub_pic'&&$update_info['edit_after_pic']) {
            // 保存图片
            $update_info['blog_pict'] = saveBase64($update_info['edit_after_pic']);
            unset($update_info['edit_after_pic']);           
        }
        $update_info['comment_open']=false;
		$data_blog[$key] = $update_info;  //全部替换
	}
}

file_put_contents('data/blog.txt', json_encode($data_blog));
echo  json_encode(['status'=>1,'update_info'=>$update_info]);


 function saveBase64($base64_image_content){
    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){

        $type = $result[2]; //是获取图片的类型，例如.jpg、.gif

        $new_file = "data/".time().".{$type}";  //保存的文件路径

        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
          return $new_file;
        }else{
            return '';
        }
    }
 };
?>