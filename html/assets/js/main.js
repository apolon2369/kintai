timerID = setInterval('clock()', 500);

function clock() {
    document.getElementById('view_clock').innerHTML = getNow();
}

function getNow() {
    let now = new Date();
    let year = now.getFullYear();
    let mon = ('0' + (now.getMonth() + 1)).slice(-2);
    let day = ('0' + now.getDate()).slice(-2);
    let hour = ('0' + now.getHours()).slice(-2);
    let min = ('0' + now.getMinutes()).slice(-2);
    let sec = ('0' + now.getSeconds()).slice(-2);
    let you = now.getDay();

    const youbi = new Array('日', '月', '火', '水', '木', '金', '土');

    return year + '年' + mon + '月' + day + '日' + '(' + youbi[you] + ')' + hour + '時' + min + '分' + sec + '秒';
}