/**
 * Created by Administrator on 2015/6/5.
 */
(function($){

    //document.write("<script type=\'text/javascript\' src=\"http://api.map.baidu.com/api?v=1.5&ak=A8773a52f6d9bc0ecaea7f15acdd13e0\"></script>")
    var ztools= $.ztools={};
    //通过经纬度显示百度地图
        ztools.getbdMap=function(defaults) {
            var options={
                "lng":109.845664,//默认定位到包头
                "lat":40.666166,
                "mapId":"container"
            };
            var opt= $.extend(options,defaults);
        var map = new BMap.Map(opt.mapId);
        var point = new BMap.Point(opt.lng,opt.lat);
        map.centerAndZoom(point, 15);
        var marker = new BMap.Marker(point);        // 创建标注
        map.addOverlay(marker);                     // 将标注添加到地图中
        map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
        map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
        map.addControl(new BMap.NavigationControl());//地图平移缩放控件
        //map.addControl(new BMap.ScaleControl());//比例尺控件
        map.addControl(new BMap.OverviewMapControl());//缩略地图控件
        //map.addControl(new BMap.MapTypeControl());//地图类型控件
        var geoc = new BMap.Geocoder();//反向地理编码
        map.addEventListener("click",function(e){
            var point = new BMap.Point(e.point.lng, e.point.lat);
            //map.centerAndZoom(point, 15);
            map.clearOverlays();                  //销毁标注
            var marker = new BMap.Marker(point);  // 创建标注
            map.addOverlay(marker);               // 将标注添加到地图中
            marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

            var pt = e.point;
            geoc.getLocation(pt, function(rs) {
                var addComp = rs.addressComponents;
                address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber;
                //$("#"+opt.inputId).val(address);
            })
        })

        };
    //关键字搜索
    ztools.searchLocal=function(defaults){
        var options={
            "mapId":"bdmapshow",
            "inputId":"suggestId",
            "defCity":'包头'
        };
        var opt= $.extend(options,defaults);
        var map = new BMap.Map(opt.mapId);
        map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
        map.addControl(new BMap.NavigationControl());//地图平移缩放控件
        map.centerAndZoom(opt.defCity, 15);
        //建立一个自动完成的对象
        var ac = new BMap.Autocomplete({"input" : opt.inputId, "location" : map});
        ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
            var str = "";
            var _value = e.fromitem.value;
            var value = "";
            if (e.fromitem.index > -1) {
                value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            }
            str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

            value = "";
            if (e.toitem.index > -1) {
                _value = e.toitem.value;
                value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            }
            str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
            G("searchResultPanel").innerHTML = str;
        });
        //鼠标点击下拉列表后的事件
        var myValue;
        ac.addEventListener("onconfirm", function(e) {

            var _value = e.item.value;
            myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            G("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
            setPlace();
        });
        function setPlace(){
            map.clearOverlays();    //清除地图上所有覆盖物
            function myFun(){
                var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
                map.centerAndZoom(pp, 18);
                map.addOverlay(new BMap.Marker(pp));    //添加标注
            }
            var local = new BMap.LocalSearch(map, { //智能搜索
                onSearchComplete: myFun
            });
            local.search(myValue);
        }
        function G(id) {
            return document.getElementById(id);
        }
        var geoc = new BMap.Geocoder();//反向地理编码
        map.addEventListener("click",function(e){
            var point = new BMap.Point(e.point.lng, e.point.lat);
            //map.centerAndZoom(point, 15);
            map.clearOverlays();                  //销毁标注
            var marker = new BMap.Marker(point);  // 创建标注
            map.addOverlay(marker);               // 将标注添加到地图中
            marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

            var pt = e.point;
            geoc.getLocation(pt, function(rs) {
                var addComp = rs.addressComponents;
                address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber;
                $("#"+opt.inputId).val(address);
            })
        })

    };
    //通过ip获取城市


    //建立遮罩
    ztools.maskshow= function () {
        if($("#maskDiv").length<=0){
             maskdiv=$("<div id=\"maskDiv\"></div>");
            $("body").append(maskdiv);
            maskdiv.css({"position":"absolute","top":0,"left":0,"height":$(document).height(),"width":$(document).width(),"backgroundColor":"#000","opacity":"0.6","zIndex":88});
        }else{
            maskdiv.show();
        }
    };
    ztools.maskhide=function(){
        maskdiv.hide();
    };

    //倒计时
    ztools.countdown=function(intDiff,fn){
        var timers=window.setInterval(function(){
            var day=0,
                hour=0,
                minute=0,
                second=0;//时间默认值
            if(intDiff > 0){
                //day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60));
                minute = Math.floor(intDiff / 60)-(hour*60) ;
                second = Math.floor(intDiff)-(hour*60*60)-(minute*60);
            }
            if (minute <= 9) minute = '0' + minute;
            if (second <= 9) second = '0' + second;
            if (hour <= 9) hour = '0' + hour;
            fn(hour,minute,second);
            intDiff--;
        }, 1000);
        return timers;
    };
    //设置cookie
    ztools.setCookie=function(cname, cvalue, exdays) {
        var d = new Date();
        exdays=exdays || 30;
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + encodeURIComponent(cvalue) + "; " + expires+";path=/";//多1个空格，获取方便,存放路径为根目录
        //document.cookie=cname+"="+encodeURIComponent(cvalue);
    };
    //获取cookie
    ztools.getCookie=function(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return decodeURIComponent(c.substring(name.length, c.length));
        }
        return "";
    };
    //清除cookie
    ztools.clearCookie=function(name) {
        ztools.setCookie(name, "", -1);
    };
    ztools.checkCookie=function() {
        var user = getCookie("username");
        if (user != "") {
            alert("Welcome again " + user);
        } else {
            user = prompt("Please enter your name:", "");
            if (user != "" && user != null) {
                setCookie("username", user, 365);
            }
        }
    };
    //获取餐厅cookie地址
    ztools.get_restaurant=function(){
        var url = ztools.getCookie('restaurant_url');
        if (url != '') {
            window.location.href = url
        }
    };
 //省市三级联动菜单    ztools.getPctMenu1=function(defaults){        var options={            "provinceId":"province",//省份id            "p_url":'',//省份url            "cityId":"city",            "c_url":'',            "countyId": "county"        };        var opt= $.extend(options,defaults);        $("#"+opt.provinceId).change(function(){            var $this=$(this);            var code=$this.val();            $.get(opt.p_url,'provinceCode='+code).success(function(data){                var num=data.length;                var options='';                for(var i= 0;i<num;i++){                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";                }                $("#"+opt.cityId).empty().append(options);                $("#"+opt.cityId).change();            });        });        $("#"+opt.cityId).change(function(){            var $this=$(this);            var code=$this.val();            $.get(opt.c_url,'cityCode='+code).success(function(data){                var num=data.length;                var options='';                for(var i= 0;i<num;i++){                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";                }                $("#" + opt.countyId).empty().append(options);            });        });    };
    //省市三级联动菜单
    ztools.getPctMenu=function(defaults){
        var options={
            "provinceId":"province",//省份id
            "p_url":'',//省份url
            "cityId":"city",
            "c_url":'',
            "townId":"town",
            "onSelectCity":function(){}
        };
        var opt= $.extend(options,defaults);
        $("#"+opt.provinceId).change(function(){
            var $this=$(this);
            var code=$this.val();
            $.get(opt.p_url,'provinceCode='+code).success(function(data){
                var num=data.length;

                var options='';
                for(var i= 0;i<num;i++){
                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";
                }
                $("#"+opt.cityId).empty().append(options);
                $("#"+opt.cityId).change();
            });
        });
        $("#"+opt.cityId).change(function(){
            var $this=$(this);
            opt.onSelectCity(this);
            var code=$this.val();
            $.get(opt.c_url,'cityCode='+code).success(function(data){
                var num=data.length;
                var options='';
                for(var i= 0;i<num;i++){
                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";
                }
                $("#"+opt.townId).empty().append(options);
            });
        });
    };
//省市商圈四级联动菜单    ztools.getLianDong=function(defaults){        var options={            "provinceId":"province",//省份id            "p_url":'',//省份url            "cityId":"city",            "c_url":'',            "townId":"town",            "t_url":'',            "sqId":"shangquan"        };        var opt= $.extend(options,defaults);        $("#"+opt.provinceId).change(function(){            var $this=$(this);            var code=$this.val();            $.get(opt.p_url,'provinceCode='+code).success(function(data){                var num=data.length;                var options='';                for(var i= 0;i<num;i++){                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";                }                $("#"+opt.cityId).empty().append(options);                $("#"+opt.cityId).change();            });        });        $("#"+opt.cityId).change(function(){            var $this=$(this);            var code=$this.val();            $.get(opt.c_url,'cityCode='+code).success(function(data){                var num=data.length;                var options='';                for(var i= 0;i<num;i++){                    options+="<option value="+data[i].code+">"+data[i].name+"</option>";                }                $("#"+opt.townId).empty().append(options);                $("#"+opt.townId).change();            });        });         $("#"+opt.townId).change(function(){            var $this=$(this);            var code=$this.val();            $.get(opt.t_url,'townCode='+code).success(function(data){                if(data!=null){                    var num=data.length;                    var options='';                    for(var i= 0;i<num;i++){                        options+="<option value="+data[i].sq_id+">"+data[i].sq_name+"</option>";                    }                    $("#"+opt.sqId).empty().append(options);                }else{                    $("#"+opt.sqId).empty().append("<option>暂时未建商圈</option>");                }            });        });    };
    //是否含有中文（也包含日文和韩文）
     ztools.isChineseChar=function(str){
        var reg = /[\u4E00-\u9FA5\uF900-\uFA2D]/;
        return reg.test(str);
     };
    //同理，是否含有全角符号的函数
     ztools.isFullwidthChar=function(str){
        var reg = /[\uFF00-\uFFEF]/;
        return reg.test(str);
     };

    /**
     * 取String 或者 object的长度
     *
     * */
     ztools.count=function(o){
        var t = typeof o;
        if(t == 'string'){
            return o.length;
        }else if(t == 'object'){
            var n = 0;
            for(var i in o){
                n++;
            }
            return n;
        }
        return false;
    };
    //判断浏览器Cookie功能是否开启,true=开启，false=未开启
    ztools.cookieEnable=function() {
        var result=false;
        if(navigator.cookiesEnabled)
            return true;
        document.cookie = "testcookie=yes;";
        var cookieSet = document.cookie;
        if (cookieSet.indexOf("testcookie=yes") > -1)
            result=true;
        document.cookie = "";
        return result;
    };


    /*===========================================String 中文字符串或英文===============================================================================*/
    //计算字符串长度
    String.prototype.strLen = function() {
        var len = 0;
        for (var i = 0; i < this.length; i++) {
            if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0) len += 2; else len ++;
        }
        return len;
    };
    //将字符串拆成字符，并存到数组中
    String.prototype.strToChars = function(){
        var chars = [];
        for (var i = 0; i < this.length; i++){
            chars[i] = [this.substr(i, 1), this.isCHS(i)];
        }
        String.prototype.charsArray = chars;
        return chars;
    };
    //判断某个字符是否是汉字
    String.prototype.isCHS = function(i){
        if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0)
            return true;
        else
            return false;
    };
    //截取字符串（从start字节到end字节）
    String.prototype.subCHString = function(start, end){
        var len = 0;
        var str = "";
        this.strToChars();
        for (var i = 0; i < this.length; i++) {
            if(this.charsArray[i][1])
                len += 2;
            else
                len++;
            if (end < len)
                return str;
            else if (start < len)
                str += this.charsArray[i][0];
        }
        return str;
    };
    //截取字符串（从start字节截取length个字节）
    String.prototype.subCHStr = function(start, length){
        return this.subCHString(start, start + length);
    };
    window.ztools=ztools;

})(jQuery);