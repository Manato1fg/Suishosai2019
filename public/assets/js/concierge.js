var Settings = {
    init_message:   "翠翔祭ホームページへようこそ！<br>"+
                    "私は案内人の<b>やめぃろ</b>と申します。<br>"+
                    "質問や雑談などお気軽にお話しくださいませ。<br>"+
                    "質問の際はクエスチョンマークのボタンを押すか、@qに続けて検索ワードを入力してください。<br>"+
                    "例: @q 2階",
    concierge_img_url: "https://suishosai.netlify.com/images/concierge.png",
    send_icon_img_url: "https://suishosai.netlify.com/images/send-icon.png",
    question_img_url: "https://suishosai.netlify.com/images/question.png",
    KEY_ENTER: 13
}

Settings.template = `
    <div id="concierge">
        <div id="concierge-chat-box">
            <div id="concierge-chat" class="concierge-chat line-bc">
            </div>
            <div class="concierge-input">
                <img src="${Settings.question_img_url}" id="concierge-question-icon">
                <textarea id="concierge-input" placeholder="検索の際は上のボタンを押してください"></textarea>
                <img src="${Settings.send_icon_img_url}" id="concierge-send-icon">
            </div>
        </div>
    </div>
    <div id="concierge-button">
        <img src="${Settings.concierge_img_url}">
        <span>&nbsp;ご質問はこちらから&nbsp;</span>
    </div>`;

const SPEAKER_CONCIERGE = 0;
const SPEAKER_USER = 1;

const SPEAKER_CONCIERGE_BALLOON = `
    <div class="balloon6">
        <div class="faceicon">
            image
        </div>
        <div class="chatting">
            <div class="says">
                <p>{MESSAGE}</p>
            </div>
        </div>
    </div>`;
const SPEAKER_USER_BALLOON = `
    <div class="mycomment">
        <p>{MESSAGE}</p>
    </div>`;

window.addEventListener("DOMContentLoaded", (e) => {initConcierge()});

var concierge_callbacks = [];

var logs = [];

function initConcierge() {
    var parent = document.createElement("div");
    parent.innerHTML = Settings.template;
    document.body.appendChild(parent);

    //クリック時の挙動
    document.getElementById("concierge").hidden = true;

    document.getElementById("wrapper").addEventListener("click", function (e) {
        var chat_box_el = document.getElementById("concierge");

        if (!chat_box_el.hidden && !closest(e.target, "#concierge")) {
            triggerChatBox();
        }
    }, true)

    document.getElementById("concierge-button").addEventListener("click", function (e) {
        triggerChatBox();
    }, false)

    //textarea関連
    var textarea = document.getElementById("concierge-input");
    var canEnter = false

    textarea.addEventListener("keypress", function () {
        canEnter = true
    }, false);

    textarea.addEventListener("keyup", (e) => {
        if (!canEnter) {
            return
        }
        canEnter = false
        var chat_box_el = document.getElementById("concierge");
        if (!chat_box_el.hidden && e.keyCode === Settings.KEY_ENTER) {
            sendMessage();
            e.preventDefault();
        }
    });

    document.getElementById("concierge-question-icon").addEventListener("click", (e) => {
        var textarea = document.getElementById("concierge-input");
        textarea.value += "@q  ";
        textarea.focus();
    })

    document.getElementById("concierge-send-icon").addEventListener("click", (e) => {
        sendMessage();
    })

    createMessage(Settings.init_message, SPEAKER_CONCIERGE, true);
}

function triggerChatBox() {
    var chat_box_el = document.getElementById("concierge");
    var chat_btn_el = document.getElementById("concierge-button");
    if (chat_box_el.hidden) {
        chat_box_el.hidden = false;
        chat_btn_el.hidden = true;
    } else {
        chat_box_el.hidden = true;
        chat_btn_el.hidden = false;
    }
}

function sendMessage(){
    let textarea = document.getElementById("concierge-input");
    if (!(textarea.value.startsWith("\n") || textarea.value === "")) {
        createMessage(textarea.value, SPEAKER_USER, false);

        var query = _encodeURI(textarea.value);
        var url = "https://suishosai-server-php.herokuapp.com/concierge.php";
        postData(url, createRequest("query", query), function(e){
            var status = e.target.status;
            var readyState = e.target.readyState;
            var response = e.target.responseText;
            if (status === 200 && readyState === 4) {
                
                var data = JSON.parse(response);
                replyMessage(data, 0);
            }
        });
    }
    textarea.value = "";
}

function _encodeURI(val){
    var dict_dict = {
        "１" : "1", "一" : "1",
        "２" : "2", "二" : "2",
        "３" : "3", "三" : "3",
        "４" : "4", "四" : "4",
        "５" : "5", "五" : "5",
        "６" : "6", "六" : "6",
        "７" : "7", "七" : "7",
        "８" : "8", "八" : "8",
        "９" : "9", "九" : "9",
        "０" : "0", "零" : "0",
        "年" : "-", "組" : "あ"
    };

    var str = "";
    

    val.split("").forEach(x => {
        if(dict_dict[x]){
            str += dict_dict[x] === "あ" ? "" : dict_dict[x];
        }else{
            str += x;
        }
    })
    

    
    return encodeURI(str);
}

function replyMessage(data, index){
    if(index === data.length){
        return;
    }
    for (var j = 0; j < concierge_callbacks.length; j++) {
        concierge_callbacks[j](data[index]);
    }
    createMessage(data[index], SPEAKER_CONCIERGE, false);
    index ++;
    setTimeout(function(){
        replyMessage(data, index)
    }, 800);
}

/**
 * 
 * @param {String} msg a meesage
 * @param {int} speaker a sender. (refer to constant above.)
 * 
 * @return message object
 */
var last = 0;
function createMessage(msg, speaker, isInit) {
    var msg_balloon = "";
    var message = "";

    if (speaker === SPEAKER_CONCIERGE) msg_balloon = SPEAKER_CONCIERGE_BALLOON;
    if (speaker === SPEAKER_USER) msg_balloon = SPEAKER_USER_BALLOON;

    message = msg_balloon.replace("{MESSAGE}", msg);
    if(isInit) message = message.replace("image", "<img src='https://suishosai.netlify.com/images/concierge.png'>")
    else message = message.replace("image", "")

    var chat = document.getElementById("concierge-chat");
    chat.insertAdjacentHTML('beforeend', message)
    
    if ((chat.offsetHeight + chat.scrollTop) / chat.scrollHeight > 0.8 || last == 0)
        chat.scrollTop = chat.scrollHeight
    
    if(!isInit){
        var nodes = chat.querySelectorAll('.faceicon');
        
        let clone = document.importNode(nodes[0].children[0], true)
        if (nodes[nodes.length - 1].children.length == 0){
            nodes[nodes.length - 1].appendChild(clone);
        }

        if (nodes.length > 3){
            last = 1;
        }
    }
    
}

function closest(el, selector) {
    var matchesFn;

    // find vendor prefix
    ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {
        if (typeof document.body[fn] == 'function') {
            matchesFn = fn;
            return true;
        }
        return false;
    })

    var parent;

    // traverse parents
    while (el) {
        parent = el.parentElement;
        if (parent && parent[matchesFn](selector)) {
            return parent;
        }
        el = parent;
    }

    return null;
}

function postData(url, data, callback){

    var xhr = new XMLHttpRequest();

    xhr.open('POST', url);
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');

    xhr.onreadystatechange = callback;

    xhr.send(data);
}

function createRequest(name, value) {
    return name + "=" + value;
}

function appendConciergeCallback(func) {
    concierge_callbacks.push(func);
}


//for PWA
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice
        .then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the A2HS prompt');
            } else {
                console.log('User dismissed the A2HS prompt');
            }
            deferredPrompt = null;
        });
});

function vote(org, type){
    if(org[0] == "O"){
        alert("総合案内所に投票ありがとうございます。ただ、総合案内所には投票できませんごめんなさい。")
        return;
    }else{
        var url = 'https://suishosai.netlify.com/vote.html?org="A"&type=P&QR=no';
        url = url.replace("A", org).replace("P", type);
        if(confirm("本当に投票してもよろしいですか？")){
            location.href = url;
        }
    }
}