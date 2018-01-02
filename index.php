<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>让兴趣，更有趣</title>
     <!-- 引入样式 -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/element-ui.css">
    <link rel="stylesheet" href="css/index.css">
    <!-- 引入组件库 -->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/vue.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="js/element-ui.js"></script>
</head>
<body>
<nav class="navbar navbar-default nav_box">
    <div class="container">
        <div class="row">
            <div class="col-md-2"><h1>LOFTER</h1></div>
            <div class="col-md-7 nav_list">
                <ul class="nav nav-pills">
                    <li><a href="#" class="cur">首页</a></li>
                    <li><a href="#">浏览</a></li>
                    <li><a href="#">APP</a></li>
                    <li><a href="#">ART</a></li>
                    <li><a href="#">摄影课堂</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false">更多<span class="caret"></span></a>
                        <ul class="dropdown-menu pull-down-box">
                            <li><a href="#">帐号设置</a></li>
                            <li><a href="#">寻找好友</a></li>
                            <li><a href="#">导入导出</a></li>
                            <li><a href="#">移动客户端</a></li>
                            <li><a href="#">LOFTCam-用心创造滤镜</a></li>
                            <li><a href="#">UAPP-创建个人应用</a></li>
                            <li><a href="#">帮助及反馈</a></li>
                            <li><a href="#">退出</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </div>
</nav>
<?php
// 获取旧的内容
$old_blog_content = file_get_contents('data/blog.txt');
// 字符串转换为数组
// json_decode
$old_blog_list = json_decode($old_blog_content, true);
//翻转数组
if (!empty($old_blog_list)) {
    $old_blog_list = array_reverse($old_blog_list);
}
?>
<!-- 引入组件 -->
<?php include "components/comment.html"; ?>
<?php include "components/music.html"; ?>
<?php include "components/tag.html"; ?>

 <div class="container" id="vue_box">
    <div class="row">
        <div class="col-md-9 pub_mes">
            <!-- 选择发布那些类型 顶部-->
            <div class="row pub_menu">
                <div class="col-md-2 pub_avatar">
                    <img src="images/avatar01.jpg" alt="">
                </div>
                <div class="col-md-2 pub_type">
                    <div class="pub_on" @click="show_publish('pub_font')">
                        <span class="glyphicon glyphicon-pencil"></span>
                        <p>文字</p>
                    </div>
                </div>
                <div class="col-md-2 pub_type">
                    <div class="pub_on" @click="show_publish('pub_pic')">
                        <span class="glyphicon glyphicon-camera"></span>
                        <p>图片</p>
                    </div>
                </div>
                <div class="col-md-2 pub_type">
                    <div class="pub_on" @click="show_publish('pub_music')">
                        <span class="glyphicon glyphicon-music"></span>
                        <p>音乐</p>
                    </div>
                </div>
                <div class="col-md-2 pub_type">
                    <div class="pub_on" @click="show_publish('pub_video')">
                        <span class="glyphicon glyphicon-film"></span>
                        <p>视频</p>
                    </div>
                </div>
                <div class="col-md-2 pub_type">
                    <div class="pub_on" @click="show_publish('pub_title')">
                        <span class="glyphicon glyphicon-list-alt"></span>
                        <p>长文章</p>
                    </div>
                </div>
            </div>
             <!-- 发布框 -->
             <!-- v-show  控制盒子的显示或隐藏值true盒子显示，反之,v-if    如果是false，那么整个节点就不会出现-->
             <div class="pub_box pub_font" v-show="pub_box_status">
                 <!-- 如果是发布音乐的话就不显示这个发布框 -->
                 <div v-if="blog_info.type!='pub_music'" class="pub_content_box">
                      <h4>&nbsp;微博标题</h4>
                     <!-- 文字内容 -->
                     <input type="text" class="form-control" v-model="blog_info.title" >
                     <input type="file" v-if="blog_info.type=='pub_pic'" @change="preview_pic()"   ref="blog_file" ><!-- 上传图片 -->
                     <!-- 预览图片 -->                
                     <img :src="preview_pic_src"  v-if="preview_pic_src!=''">
                     <!-- 标签组件 -->
                     <vincent-tag  @get_tag="get_tag"></vincent-tag>
                     <!-- 底部——发布取消 -->
                     <el-row class="footer_btn">
                         <el-col :span="3" >
                             <el-button @click="cancel_publish()">取消</el-button>
                         </el-col>
                         <el-col :span="3" :offset="18">
                             <el-button type="success" v-bind:disabled="blog_info.title==''"  @click="pub_blog()">发布</el-button>
                         </el-col>
                     </el-row>                        
                 </div>
                 <!-- 音乐组件 -->
                 <vincent-music @cancel="cancel_publish" v-if="blog_info.type=='pub_music'" @pub_music="pub_music"> </vincent-music>                        
             </div>
             <!-- 发布了的微博 -->
            <ul>
                <li v-for="info in blog_list" class="row blog_list">
                    <div class="col-md-2 blog_avatar"><img src="images/avatar02.jpg" alt=""></div>  
                    <div class="col-md-10  blog_content_list">
                        <!-- 微博内容 -->
                        <div class="blog_content">
                            <img :src="info.blog_pict" v-if="info.blog_pict">
                            <div>
                                <h4>{{info.blog_content}}</h4>
                                <audio :src="info.blog_music_src" v-if="info.blog_music_src" controls="true"></audio>                                
                            </div>                           
                        </div>
                        <!-- 标签 -->
                        <div class="tag_content">
                            <a v-for="tag in info.tag_list">&nbsp;#{{tag}}&nbsp;</a>
                        </div> 
                        <!-- 微博底部 -->
                         <div class="pull-right blog_content_btn">
                            <a data-toggle="modal" data-target="#myModal" class="btn btn-success"     @click="editInfo(info)" >编辑</a>
                            <a class="btn btn-danger" @click="delInfo(info)">删除</a>  
                        </div>                        
                        <!--  评论组件（模块化开发） -->
                        <vincent-comment :info="info"></vincent-comment>
                    </div>
                </li>
            </ul>
         </div>
         <!-- 右边布局 -->
         <div class="col-md-3 ">
            <ul class="my_center">
                <li>
                    <h4>微博</h4>
                    <p>888888888.lofter.com</p>
                </li>
            </ul>
         </div>
     </div>
     <!-- Modal 编辑框-->
     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                <div class="modal-body" v-if="update_info.blog_type!='pub_music'">
                    <form class="from">
                        <div class="form-group edit_blog">
                            <label >微博标题</label>
                            <input type="text" class="form-control" v-model="update_info.blog_content"> 
                            <div class="form-group"  v-if="update_info.blog_type=='pub_pic'">
                                <!-- 显示原图片 -->
                                <img :src="update_info.blog_pict"  v-if="preview_pic_src==''">
                                <!-- 预览的时候显示的img标签-->
                                <img :src="preview_pic_src"  v-if="preview_pic_src!=''">
                                <input type="file" @change="preview_pic()"  ref="blog_file" >
                            </div>
                        </div>
                    </form>                         
                </div>
                <!-- 音乐组件 -->
                <vincent-music  v-if="update_info.blog_type=='pub_music'" @pub_music="pub_music" :update_info="update_info"> </vincent-music>  
                <!-- 标签组件 -->
                <vincent-tag></vincent-tag>                                    
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary"  data-dismiss="modal" @click="updata()">更新</button>
                </div>
            </div>
        </div>
     </div>
 </div>
<script src="js/index-vue.js?5"></script>
</body>
</html>