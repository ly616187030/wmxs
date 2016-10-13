/**
 * Created by Administrator on 2015/6/11.
 */
//切换页面
$(function(){
    $('#vou1').click(function(){
        $('#vo1').show();
        $('#vo2').hide();
        $('#vo3').hide();
        $('#vo4').hide();
    })

    $('#vou2').click(function(){
        $('#vo1').hide();
        $('#vo2').show();
        $('#vo3').hide();
        $('#vo4').hide();
    })
    $('#vou3').click(function(){
        $('#vo1').hide();
        $('#vo2').hide();
        $('#vo3').show();
        $('#vo4').hide();
    })
    $('#vou4').click(function(){
        $('#vo1').hide();
        $('#vo2').hide();
        $('#vo3').hide();
        $('#vo4').show();
    })
})



//鼠标点击后的字体颜色
function init(){
    var a=document.links;
    for(var i=0;i<a.length;i++){
        a[i].style.color="";
        a[i].onclick=change;
    }
}
function change(){
    var a=document.links;
    for(var i=0;i<a.length;i++){
        a[i].style.color="";
    }
    this.style.color="#ff2d4b";
    shows(this);
}
window.onload=init;