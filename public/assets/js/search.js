var last = "unknown"
var recommending = false;

function onInputChange(){
    var value = document.getElementById("search-input").value;
    
    if(value === ""){
        
        if (recommending) {
            
            recommend(last, true);
            return;
        }
        
        var els = document.querySelectorAll('.org-item');
        for (const el of els) {
            el.classList.remove("hide");
        }
        document.getElementById("empty").classList.add('hide');
        return;
    }

    value = _encodeURI(value);

    postData(
        "https://suishosai-server-php.herokuapp.com/search.php",
        createRequest("query", value),
        function (e) {
            var status = e.target.status;
            var readyState = e.target.readyState;
            var response = e.target.responseText;
            if (status === 200 && readyState === 4) {
                if(document.getElementById("search-input").value === ""){
                    return;
                }
                var orgs = JSON.parse(response);
                var els = document.querySelectorAll('.org-item');
                var count = 0;
                for(const el of els){
                    var org = el.getAttribute("data-org");
                    
                    if(orgs.includes(org)){
                        el.classList.remove("hide");
                        count ++;
                        
                    }else{
                        el.classList.add("hide");
                    }
                }
                lazyestload();
                if(count === 0){
                    document.getElementById("empty").classList.remove('hide');
                }else{
                    document.getElementById("empty").classList.add('hide');
                }
            }
        }
    )
}

function _encodeURI(val) {
    var dict_dict = {
        "１": "1", "一": "1",
        "２": "2", "二": "2",
        "３": "3", "三": "3",
        "４": "4", "四": "4",
        "５": "5", "五": "5",
        "６": "6", "六": "6",
        "７": "7", "七": "7",
        "８": "8", "八": "8",
        "９": "9", "九": "9",
        "０": "0", "零": "0",
        "年": "-", "組": "あ"
    };

    var str = "";


    val.split("").forEach(x => {
        if (dict_dict[x]) {
            str += dict_dict[x] === "あ" ? "" : dict_dict[x];
        } else {
            str += x;
        }
    })



    return encodeURI(str);
}


function recommend(val, opt = false){
    var els = document.querySelectorAll('.org-item');
    for (const el of els) {
        var org = el.getAttribute("data-org");
        if (val === last && !opt) el.classList.remove("hide");
        else el.classList.add("hide");
    }
    
    if (val === last && !opt){
        recommending = false;
        last = "unknown";
        return;
    }

    for (const el of els) {
        var org = el.getAttribute("data-org");
        val.split("").forEach(x => {
            if (x == "K" && org == "K-12"){
                return;
            }

            if (x == "L" && org == "K-12"){
                el.classList.remove("hide");
            }

            if (org.includes(x)) {
                el.classList.remove("hide");
                return;
            }
        })
    }

    last = val;
    lazyestload();
    recommending = true;
}
var zoomed = false;

function zoom(n){
    if(n === -1 && zoomed){
        zoomed = false;
        _zoom(zoomed)
    } else if (n === 1 && !zoomed){
        zoomed = true;
        _zoom(zoomed)
    }
}

function _zoom(b){
    var els = document.querySelectorAll('.org-item');
    var prev = b ? "col-4" : "col-6";
    var after = b ? "col-6" : "col-4";
    for (const el of els) {
        el.classList.remove(prev);
        el.classList.add(after);
    }
}