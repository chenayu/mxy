$(".preview").change(function (){
    var file = this.files[0];
    //转成字符串,调用下面函数转成字符串
    var str = getObjectUrl(file); 
    //在框的前面放一个图片

    //再次添加时删除上一个图片
    $(this).prev(".img_preview").remove();
    $(this).before("<div class='img_preview'><img src='"+str+"' width='80' height='80'></div>");
});


    // 把图片转成一个字符串
    function getObjectUrl(file) {
    var url = null;
    if (window.createObjectURL != undefined) {
    url = window.createObjectURL(file)
    } else if (window.URL != undefined) {
    url = window.URL.createObjectURL(file)
    } else if (window.webkitURL != undefined) {
    url = window.webkitURL.createObjectURL(file)
    }
    return url
    }