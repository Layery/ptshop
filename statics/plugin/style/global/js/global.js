function btn_iframef5(){
	 window.parent.frames["iframe"].location.reload();	
}

function btn_checkbom(url){
	 window.parent.frames["iframe"].location=url;
}

function btn_caches(){

}

function btn_map(url){
	
	 window.parent.frames["iframe"].location=url;
}


//JS 删除 API
//url 地址
//msgs 消息提示信息
function Del(url,msgs){	
		
		
		var t=$.layer({
			type :0,
			area : ['auto','auto'],
			title : ['提示信息',true],
			border : [5 ,  10, '#7298a6', true],
			dialog:{
					msg:msgs,
					type:4,
					btns:2,
					btn : ['删除','取消'],
					yes : function(){
						 $.ajax({
							async:false
						 });
						 $.post(url,{ajax:true},function(data){			
							if(data=='no'){
								layer.msg("删除失败!",2,8);				
							}else{
								layer.msg("删除成功!",2,1);								
								window.parent.frames["iframe"].location=data;	
							}
						 });           
       				},
        			no : function(){
						layer.close(t);
        			}				
				}
		});
}

/**
 * JS 删除API2
 * @param url 后台处理URL
 * @param data 传递的数据 json shuju
 * @param type ajax的传输方式，'get','post'
 * @param msg 对话框的内容显示
 */
function ajaxDel(url,data,type,msg) {
	var t=$.layer({
		type :0,
		area : ['auto','auto'],
		title : ['提示信息',true],
		border : [5 ,  10, '#7298a6', true],
		dialog:{
			msg:msg,
			type:4,
			btns:2,
			btn : ['删除','取消'],
			yes : function(){
				switch (type){
					case 'get':
						$.get(url,data,function(data){
							if(data == 'ok'){
								layer.msg("删除成功!",2,1);
								window.parent.frames["iframe"].location.reload();
							}else{
								layer.msg(data,2,8);
							}
						});
						break;
					case 'post':
						$.post(url,data,function (data) {
							if(data=='ok'){
								layer.msg("删除成功!",2,1);
								window.parent.frames["iframe"].location.reload();
							}else{
								layer.msg(data,2,8);
							}
						});
				}

			},
			no : function(){
				layer.close(t);
			}
		}
	});
}

//JS 消息提示API
function message(msgs,type,time){
	layer.msg(msgs,time,type);
}

//JS 新建浏览器标签API
function openwinx(url,name,w,h) {
	if(!w) w=screen.width-4;
	if(!h) h=screen.height-95;
    window.open(url,name,"top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
}

$.focusblur = function(focusid){
	var focusblurid = $(focusid);
	var defval = focusblurid.val();  
	focusblurid.focus(function(){
			var thisval = $(this).val();
			if(thisval==defval){
				$(this).val("");
			}
	});
	focusblurid.blur(function(){
			var thisval = $(this).val();
			if(thisval==""){
				$(this).val(defval);
			}
	});
};