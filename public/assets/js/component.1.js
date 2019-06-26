function A(callback) {
    if(getUserID() !== null){
        return;
    }
    getBrowserFingerPrint(function (fingerprint) {
        
        postData(
            "https://suishosai-server-php.herokuapp.com/redirect4.php",
            createRequest("userid", fingerprint),
            function (e) {
                var status = e.target.status;
                var readyState = e.target.readyState;
                var response = e.target.responseText;
                if (status === 200 && readyState === 4) {
                    // 利用例
                    if (!storageAvailable('localStorage')) {
                        
                        alert("プライベートブラウザの使用はできません。あるいはCookieが有効になっていません");
                        location.href = "https://suishosai.netlify.com";
                        
                    } else {
                        window.localStorage.setItem("userid3", fingerprint);

                        callback();
                    }
                }
            }
        )
    });
}

function storageAvailable(type) {
    try {
        var storage = window[type],
            x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    } catch (e) {
        return e instanceof DOMException && (
            // everything except Firefox
            e.code === 22 ||
            // Firefox
            e.code === 1014 ||
            // test name field too, because code might not be present
            // everything except Firefox
            e.name === 'QuotaExceededError' ||
            // Firefox
            e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
            // acknowledge QuotaExceededError only if there's something already stored
            storage.length !== 0;
    }
}
function postData(url, data, callback) {

    var xhr = new XMLHttpRequest();

    xhr.open('POST', url);
    xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');

    xhr.onreadystatechange = callback;

    xhr.send(data);
}

function createVoteRequestUrl(data1, data2) {
    console.log(getUserID());
    
    var str =
        createRequest("data_org", data1) + "&" +
        createRequest("data_index", data2) + "&" +
        createRequest("accessToken", getUserID());

    return str;
}

function createStampRallyRequestUrl(content) {
    var str =
        createRequest("text", content) + "&" +
        createRequest("accessToken", getUserID());

    return str;
}

function createRequest(name, value) {
    return name + "=" + value;
}

function getUserID() {
    return window.localStorage.getItem("userid3");
}

function getQueryString() {
    var result = {};
    if (1 < window.location.search.length) {
        var query = window.location.search.substring(1);

        var parameters = query.split('&');

        for (var i = 0; i < parameters.length; i++) {
            var element = parameters[i].split('=');

            var paramName = decodeURIComponent(element[0]);
            var paramValue = decodeURIComponent(element[1]);

            result[paramName] = paramValue;
        }
    }
    return result;
}

function getBrowserFingerPrint(callback) {
    if (window.requestIdleCallback) {
        requestIdleCallback(function () {
            Fingerprint2.get(function (components) {
                var murmur = Fingerprint2.x64hash128(components.map(function (pair) { return pair.value }).join(), 31)
                callback(murmur)
            })
        })
    } else {
        setTimeout(function () {
            Fingerprint2.get(function (components) {
                var murmur = Fingerprint2.x64hash128(components.map(function (pair) { return pair.value }).join(), 31)
                callback(murmur)
            })
        }, 500)
    }
}
