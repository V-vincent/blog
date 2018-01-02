<?php

error_reporting(~E_NOTICE);
// 接口API
// _REQUEST只能获取text/textarea
// _FILES  获取file文件内容
$blog_content=$_REQUEST['blog_content'];//获取微博内容
$cur_blog_type=$_REQUEST['cur_blog_type'];//获取微博类型
$blog_pict =  '';
$blog_music='';
$blog_music_src='';


if($cur_blog_type=='pub_pic'){
    // 保存图片
    // 第一步：获取临时文件的值
    $temp_file=$_REQUEST['blog_pict'];
    $blog_pict = saveBase64($temp_file);
}
else if($cur_blog_type=='pub_music'){
    //保存音乐
    $music_info = json_decode($_REQUEST['other_file'],true);
    $blog_content=$music_info['blog_content'];
    $blog_pict = $music_info['blog_pict'];
    $blog_music_src = $music_info['blog_music_src'];
}

//获取文件内容
$old_blog_list = getData("blog.txt");
$old_tag_list = getData("tag.txt");

$tag_list  = $_REQUEST['tag_list'];
$tag_name_a = getAlltagname($old_tag_list);

$bid=count($old_blog_list)+1;
// 以后要拓展，发布微博的时候成功和失败
$rtnData = [
    'bid'=>$bid,
    'blog_type'=>$cur_blog_type,//微博类型
    'blog_content'=>$blog_content,//140简短内容
    'blog_pict'=>$blog_pict,//微博图片
    'blog_music'=>$blog_music,//音乐
    'blog_music_src'=>$blog_music_src,//音乐的路径
    'comment_open'=>false,//评论框控制开关
    'comment_list'=>[],//存放评论的内容  
    'tag_list'=>$tag_list //标签的内容
];
$old_blog_list[] =$rtnData;

//保存文件内容
setData("blog.txt",$old_blog_list);
//标签的保存
$tag_list=$_REQUEST['tag_list'];
if(!empty($tag_list)){
    $old_tag_count=count($old_tag_list);//获取数量
    foreach ($tag_list as $key => $value) {
        // 判断这个标签到底存不存在
        // 如果存在的话，那么标签id就用之前
        // 反之，就自增+1
        if (in_array($value, array_keys($tag_name_a))) {
             $tid = $tag_name_a[$value];
        }else{
            $tid = ++$old_tag_count;//生成标签id
            $old_tag_list[] =array(
                "tid"=>$tid,
                "tag_name"=>$value
            );
        }
        //这里把id加进中间表中
        setDataByWrite('blog_tag.txt',array(
            'blog_id'=>$bid,
            'tag_id'=>$tid,
        ));
    }
    setData("tag.txt",$old_tag_list);
};

echo  json_encode($rtnData);

/**
 * 保存base64图片方法
 * @param  string $base64_image_content base64长内容
 * @return string                       图片保存后的路径
 */
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
/**
 * 获取文件内容
 * @param  string $file_name 内容所在的文件名
 * @return array            里面所有的内容列表
 */
function getData($file_name){
    // 获取旧的内容
    $old_blog_content = file_get_contents('data/'.$file_name);
    // 字符串转换为数组
    // json_decode
    return json_decode($old_blog_content,true);
};
/**
 * 存储内容
 * @param string $file_name 内容所在的文件名
 * @param array $new_list  新的内容数组列表
 */
function setData($file_name,$new_list){
    file_put_contents('data/'.$file_name,json_encode($new_list));
};
/*
unserialize序列化
规律会显示这个值的类型，每一个键的类型，值的长度
数据的类型：
$data=[
  'a'=>[
     微博ID1=>[标签ID1、标签ID2]，
     微博ID2=>[标签ID3、标签ID2]，     
  ]
]
*/
function setDataByWrite($file_name,$new_list){
    $save_data_a=[];
    $old_middle_data=file_get_contents('data/'.$file_name);
    if($old_middle_data){
        $old_data=unserialize($old_middle_data);
        $save_data_a['a']=$old_data['a'];
    };
    $save_data_a['a'][$new_list['blog_id']][]=$new_list['tag_id'];
    // serialize 把一个字符串变成序列化
    file_put_contents('data/'.$file_name,serialize($save_data_a));
};

/**
 * 获取所有的标签名称
 * @param  array $old_tag_list 整个标签
 * @return array               ['友情'=>标签id,'爱情'=>标签id.....]
 */
function getAlltagname($old_tag_list){
    $rtn_data = [];
    if ($old_tag_list) {
        foreach ($old_tag_list as $key => $value) {
            $rtn_data[$value['tag_name']]= $value['tid'];
        }
    }
    return $rtn_data;
}

?>
