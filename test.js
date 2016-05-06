/**
 * Created by jjpro on 4/23/16.
 */
$('head').append(widgetTag).append(scriptTag).append(linkTag).append(auditTag);
document.head.appendChild(scriptTag)
$('head').append(linkTag).append(auditTag);
if (location.href.indexOf('&from=pcrecommend') > 0) {
    var scriptTag = document.createElement('script');
    scriptTag.src = (document.location.protocol === 'http:' ? 'http:' : 'https:') + '//s4.cnzz.com/stat.php?id=1257245197&web_id=1257245197';
    $('head').append(scriptTag);
}
var hasClicked = false;
$('#export_song').on('click', function () {
    if (!hasClicked) {
        $('#exportFrame').attr('src', 'http://changba.com/now/export_song.php?workid=2772843&isvideo=0').dialog({title: '导出歌曲'});
        hasClicked = true;
    }
    window.location.href = "https://changba.com/official_login.php?redirect=aHR0cCUzQSUyRiUyRmNoYW5nYmEuY29tJTJGcyUyRkhUNmNTWXk1cnZFJTNGJTI2Y29kZSUzREt4aHN2NjA0NGlrJTI2ZnJvbSUzRHBjcmVjb21tZW5k";
});
(function () {
    var a = "http://mp3.changba.com/userdata/userwork/c11/d9b/d6320db.mp3", b = /userwork\/([abc])(\d+)\/(\w+)\/(\w+)\.mp3/, c = b.exec(a);
    if (c) {
        var d = c[1], e = c[2], f = c[3], g = c[4];
        e = parseInt(e, 8), f = parseInt(f, 16) / e / e, g = parseInt(g, 16) / e / e, "a" == d && g % 1e3 == f ? a = "http://a" + e + "mp3.ch" + "angba." + "com/user" + "data/user" + "work/" + f + "/" + g + ".mp3" : "c" == d && (a = "http://aliuwmp3.changba.com/userdata/userwork/" + g + ".mp3")
    }
    document.getElementById("audio").src = a;
})();
})
;window._bd_share_config = {
    "common": {
        "bdSnsKey": {},
        "bdText": "勇气 - Kelly于文文(#唱吧#录制)",
        "bdComment": "大家一起来唱吧围观一下Ta吧!",
        "bdMini": "2",
        "bdMiniList": false,
        "bdPic": "http://aliimg.changba.com/cache/photo/537821157_320_320.jpg",
        "bdStyle": "0",
        "bdSize": "16"
    }, "share": {}
};
with (document)0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
</
script >
