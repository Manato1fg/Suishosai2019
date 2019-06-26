"""
これはinputされたファイルから自動で団体ページを作成するプログラムです。これがあると作業効率爆上がり

@license MIT
@author Manato1fg
"""

import argparse
import urllib.request
import urllib.parse
import xml.etree.ElementTree as ET
import csv
import os
import json


def main():
    flag = input_parse()
    template = './template.html'
    i = 0
    ary = []
    ary.append(["id","name","description","keywords","url"])
    outputfolder = "./output/"
    os.makedirs(outputfolder, exist_ok=True)
    pair = {
        "A":1,
        "B":1,
        "C":2,
        "E":2,
        "F":2,
        "G":2,
        "H":2,
        "I":2,
        "J":0,
        "K":0,
        "L":0,
        "O":10,
        "T":1,
        "U":2
    }
    pair2 = {0: "ステージ発表部門で投票する", 1: "調理食販部門で投票する", 2: "娯楽・展示部門で投票する", 10: "その他で投票する"}
    base_url = "https://jlp.yahooapis.jp/MAService/V1/parse?appid=dj00aiZpPTN1U1QzQzh5aVNBUSZzPWNvbnN1bWVyc2VjcmV0Jng9MzA-&results=ma,uniq&uniq_filter=9%7C10&sentence="
    with open(template) as t:
        d = t.read()
        with open("input.json", 'r') as f:
            js = json.loads(f.read(), encoding="utf-8")
            
            eeeeeee = []
            for c in js:
                i += 1
                o_id = c["id"] 
                o_name = c["data"]["name"]
                if flag == 10:
                    print("\""+str(o_id)+"\"" + ":\"" + o_name + "\",")
                    continue
                o_composition = c["data"]["composition"] 
                o_place = c["data"]["place"]
                o_description = c["data"]["description"]

                if flag == 2:
                    clas = {
                        "id":o_id,
                        "data": {
                            "name": o_name,
                            "composition": o_composition,
                            "place": o_place,
                            "description": o_description,
                            "SNS":{
                                "Twitter":"",
                                "Instagram":"",
                                "Youtube": ""
                            },
                            "MENU":[]
                        }
                    }
                    eeeeeee.append(clas)

                newfile = o_id.replace("-", "")+".html"
                o_url = "https://suishosai.netlify.com/events/"+newfile
                if(flag == 0):
                    print(o_url)

                s = o_composition + ":" + o_name + ":" + o_place + ":"
                data = ""
                m_temp = "<h2><br>MENU</h2><table class='col-head-type1'>MENUS</table>"
                mm_temp = "<tr><th>name</th><td>price翠円</td></tr>"
                if len(c["data"]["MENU"]) == 0:
                    data = d.replace("menusSSS", "")
                else:
                    pppp = ""
                    for k in c["data"]["MENU"]:
                        pppp += mm_temp.replace("name", k["name"]).replace("price", k["price"])
                    data = d.replace("menusSSS", m_temp.replace("MENUS", pppp))
                
                sns_temp = "<h3><br>公式SNS<ul class='icons' style='size: 4.0em;'>icns</ul></h3>"
                ppppp = ""
                twitter_temp = '<li><a href="url" class="icon fa-twitter"><span class="label">Twitter</span></a></li>'
                youtube_temp = '<li><a href="url" class="icon fa-youtube"><span class="label">Twitter</span></a></li>'
                instagram_temp = '<li><a href="url" class="icon fa-instagram"><span class="label">Instagram</span></a></li>'
                if c["data"]["SNS"]["Twitter"] != "":
                    ppppp += twitter_temp.replace("url", c["data"]["SNS"]["Twitter"])
                if c["data"]["SNS"]["Youtube"] != "":
                    ppppp += youtube_temp.replace("url", c["data"]["SNS"]["Youtube"])
                if c["data"]["SNS"]["Instagram"] != "":
                    ppppp += instagram_temp.replace("url", c["data"]["SNS"]["Instagram"])
                if ppppp == "":
                    data = data.replace("SSSSNNNNSSSS", "")
                else:
                    data = data.replace("SSSSNNNNSSSS", sns_temp.replace("icns", ppppp))
                
                if "poster" in c["data"].keys():
                    o_poster = c["data"]["poster"]
                    if not o_poster:
                        data = data.replace("<button onclick=\"vote('variable8', 3)\">看板部門で投票する</button>", "")
                    
                    poster_template = '<h3 style="width: 100%; margin-bottom: 0;">ポスター</h3><div class="image main"><img src="../images/poster/variable8.jpg"></div>'
                    data = data.replace("posterrrrrrr", poster_template)
                else:
                    data = data.replace("<button onclick=\"vote('variable8', 3)\">看板部門で投票する</button>", "")
                    data = data.replace("posterrrrrrr", "")

                t_template = '<span style="width: 100%; margin-bottom: 10px;"><h3 style="margin-bottom: 0;">公演時間<br>variable9</h3><a href="https://suishosai.netlify.com/timetable.html">タイムテーブルを見る</a></span>'
                if "Time" in c["data"].keys():
                    data = data.replace("Tiiiiimmmmm", t_template.replace("variable9", c["data"]["Time"]))
                else:
                    data = data.replace("Tiiiiimmmmm", "")
                
                if(flag == 1):
                    ss = '<div class="col-4 org-item" data-org="variable1"><span class="image fit"><a href="events/variable3.html"><img src="images/small/variable1.png" class="eventhover lazyestload" alt="variable2" data-src="images/middle/variable1.png"/></a></span></div>'
                    print(ss.replace("variable1", o_id).replace("variable2", o_name.replace("[COMMA]", ",")).replace("variable3", o_id.replace("-", "")))
                else:
                    res = [o_id, o_name, o_description, o_place]
                    url = base_url + urllib.parse.quote(o_description)
                    image_url = "https://suishosai.netlify.com/images/big/"+o_id+".png"
                    if flag == 4:
                        req = urllib.request.Request(url)
                        with urllib.request.urlopen(req) as response:
                            XmlData = response.read()
                            root = ET.fromstring(XmlData)
                            for child1 in root:
                                if child1.tag == '{urn:yahoo:jp:jlp}uniq_result':
                                    for child2 in child1:
                                        if child2.tag == '{urn:yahoo:jp:jlp}word_list':
                                            for child3 in child2:
                                                for child4 in child3:
                                                    if child4.tag == '{urn:yahoo:jp:jlp}surface':
                                                        s += child4.text + ":"
                        res.append(s[0:-1])
                        res.append(o_url)
                        ary.append(res)
                        
                    with open(outputfolder+newfile, "w") as ff:
                        variable6 = pair[o_id[0:1]]
                        if o_id[0:1] in "ABCDEFGHIO":
                            data = data.replace("variable10", "checkStatus();")
                        else:
                            data = data.replace(
                                '<h4 style="margin-bottom: 0; width: 100%;">運営情報<h5 style="margin-bottom: 0;" id="lastUpdate"></h5></h4>', "")
                            data = data.replace("variable10", "")
                        
                        newdata = data.replace("variable1", o_name.replace("[COMMA]", ",")).replace("variable2", o_composition).replace("variable3", o_place).replace(
                            "variable4", image_url).replace("variable5", o_description).replace("variable6", str(variable6)).replace("variable7", pair2[variable6]).replace("variable8", o_id)
                        ff.write(newdata)
            if(flag == 2):
                with open('input.json', "w") as f:
                    json.dump(eeeeeee, f, sort_keys=False,
                              ensure_ascii=False, indent=4)
    if(flag == 0):
        with open('output.csv', 'w') as f:
            writer = csv.writer(f, lineterminator='\n')
            writer.writerows(ary)
    
    print(i)


def input_parse():
    parser = argparse.ArgumentParser()
    parser.add_argument("-flag", required=True)
    args = parser.parse_args()

    return int(args.flag)


if __name__ == "__main__":
    main()
