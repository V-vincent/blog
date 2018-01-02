// 生命周期
// 自动执行

// 以数据驱动的框架MVVM
// M：模型model 定义用来驱动的数据
// V：视图view  html节点
// VM:ViewModel视图模型   连接视图和模型纽带，起着桥梁的作用
new Vue({
    el: '#vue_box', //el :element节点，定义视图节点
    data: {
        // model
        blog_info: {
            title: '', //标题
            type: '', //类型
            pict: '' //图片
        }, //微博信息
        pub_box_status: false, //发布微博盒子的状态
        blog_list: [], //微博列表
        preview_pic_src: '', //预览图片的路径
        update_info: [], //暂时存储更新的数据
        tag_list:[]//标签内容
    },
    created() {
        // 创建的时候
        //数据一进来是从js里面获取

        $.get('list.php', (rtnData) => {
            if (rtnData) {
                this.blog_list = JSON.parse(rtnData)
            }
        })
    },
    methods: {
        //显示发布框
        show_publish: function(pub_type) {
            this.blog_info.type = pub_type;
            this.pub_box_status = true; //显示
            this.blog_info.title = ''; //每一次打开都清空输入框的内容
            this.preview_pic_src = '';
        },
        //取消发布框
        cancel_publish: function() {
            this.blog_info.title = ''; //每一次打开都清空输入框的内容
            this.pub_box_status = false; //隐藏
        },
        //点击发布
        pub_blog: function() {
            var blog_content = this.blog_info.title;
            var cur_blog_type = this.blog_info.type;
            //在这里获取标签的内容
            $.post('pub.php', {
                    blog_content: blog_content,
                    cur_blog_type: cur_blog_type,
                    blog_pict: this.preview_pic_src,
                    tag_list:this.tag_list
                },
                (rtnData) => {
                    if (rtnData) {
                        var rtnData = JSON.parse(rtnData);
                        this.blog_list.unshift(rtnData); //遍历新的内容                            
                    }
                    this.pub_box_status = false; //隐藏      
                })
        },
        //获取标签列表
        get_tag:function(tag_list){
            this.tag_list=tag_list;
        },
        //发布音乐微博
        pub_music:function(music_info,blog_content){
            var cur_blog_type = this.blog_info.type;
            $.post('pub.php', {
                blog_content: blog_content,
                cur_blog_type: cur_blog_type,
                 tag_list:this.tag_list,
                other_file: JSON.stringify(music_info)
            }, (rtnData) => {
                if (rtnData) {
                    var rtnData = JSON.parse(rtnData);
                    this.blog_list.unshift(rtnData); //遍历新的内容                            
                }
                this.pub_box_status = false; //隐藏                        
            })
        },
        //预览图片
        preview_pic: function() {
            // 预览图片
            var file = this.$refs.blog_file.files[0];
            // 1、创建一个reader
            var fr = new FileReader();
            // 2、将图片将转成 base64 格式
            fr.readAsDataURL(file);
            // 3、读取成功后的回调
            var self = this;
            fr.onloadend = function() {
                self.preview_pic_src = this.result;
            };
        },     
        //删除操作
        delInfo: function(info) {
            $.post('delete.php', {
                id: info.bid,
            }, (rtnData) => {
                // 删除微博
                // 怎么找到它要删除的这条数据 ？info
                // 怎么在js的数组中找对应的值？indexOf
                // 怎么在js的数组中删除指点的值？
                var info_index = this.blog_list.indexOf(info);
                this.blog_list.splice(info_index, 1);
            })
        },
        //编辑操作
        editInfo: function(info) {
            this.update_info = info;
        },
        //更新操作
        updata: function() {
            if (this.update_info.blog_type == 'pub_pic' && this.preview_pic_src != '') {
                this.update_info.edit_after_pic = this.preview_pic_src;
            }
            // 更新
            $.post("update.php", {
                info: JSON.stringify(this.update_info)
            }, (rtnData) => {
                $("#myModal").modal('hide');
                var rtnData = JSON.parse(rtnData);
                // 替换新的图片
                this.update_info.blog_pict = rtnData.update_info.blog_pict;
            });
            this.preview_pic_src = '';
        },
       
    }
});
