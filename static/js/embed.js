layui.use(['upload','form','element','layer','flow'], function(){
		var upload = layui.upload;
        var form = layui.form;
        var element = layui.element;
        var layer   = layui.layer;
        
		//图片懒加载
		var flow = layui.flow;
		flow.lazyimg({
            elem:'#found img'
        });
        //图片查看器
        layer.photos({
            photos: '#found'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
        layer.photos({
            photos: '#lightgallery'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
        
		//执行实例
		var uploadInst = upload.render({
            elem: '#upimg' //绑定元素
            //选择的时候触发
			,choose: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                this.url = './up.php';
                this.field = 'pic';
                $(".progress").hide();
                //console.log(this.url);
            }
            ,accept:'file'
            ,acceptMime:'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif'
            ,exts: 'jpg|jpeg|png|gif'
            ,size:10240
            ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                layer.load(); //上传loading
            }
			,done: function(res){
                //console.log(res);
                //上传完毕回调
                //如果上传失败
                if(res.code != 'success'){
                    layer.open({
                        title: '温馨提示'
                        ,content: res.msg
                    });  
                    layer.closeAll('loading');  
                }
                else{
                    layer.closeAll('loading'); 
                    $("#img-thumb a").attr('href',res.data.url);
                    $("#img-thumb img").attr('src',res.data.url);
                    $("#url").val(res.data.url);
                    $("#html").val("<img src = '" + res.data.url + "' />");
                    $("#markdown").val("![](" + res.data.url + ")");
                    $("#bbcode").val("[img]" + res.data.url + "[/img]");
                    $("#imgshow").show();
                }
			}
			,error: function(){
                //请求异常回调
                layer.closeAll('loading');
			}
        });
        //单文件上传END

});


//复制链接
//复制链接
function copyurl(info){
    var copy = new clipBoard(document.getElementById('links'), {
        beforeCopy: function() {
            info = $("#" + info).val();
        },
        copy: function() {
            return info;
        },
        afterCopy: function() {

        }
    });
    layui.use('layer', function(){
          var layer = layui.layer;
      
          layer.msg('链接已复制！', {time: 2000,icon:1})
    }); 
}


//显示图片操作按钮
function show_imgcon(id){
    $("#imgcon" + id).show();
}
//隐藏图片操作按钮
function hide_imgcon(id){
    $("#imgcon" + id).hide();
}

//显示图片链接
function showlink(url,thumburl){
    layer.open({
        type: 1,
        title: false,
        content: $('#imglink'),
        area: ['680px', '500px']
    });
    $("#img-thumb a").attr('href', thumburl);
    $("#img-thumb img").attr('src',thumburl);
    $("#url").val(url);
    $("#html").val("<img src = '" + url + "' />");
    $("#markdown").val("![](" + url + ")");
    $("#bbcode").val("[img]" + url + "[/img]");
    $("#imglink").show();
}


/**
 * 创建并下载文件
 * @param  {String} fileName 文件名
 * @param  {String} content  文件内容
 */
function createAndDownloadFile(fileName, content) {
    var aTag = document.createElement('a');
    var blob = new Blob([content]);
    aTag.download = fileName;
    aTag.href = URL.createObjectURL(blob);
    aTag.click();
    URL.revokeObjectURL(blob);
}
