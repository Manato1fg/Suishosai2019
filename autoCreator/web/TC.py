import json
def main():
    template = "<tr><th>menu</th><td>price翠円</td></tr>"
    with open("./input.txt") as f:
        contents = f.readlines()
        i = 0
        org = ""
        name = ""
        value = 0
        classs = [

        ]
        while(i < len(contents)):
            if contents[i] == "\n":
                if i != 0:
                    #print("</table>")
                    print(json.dumps(classs, sort_keys=False,
                                     ensure_ascii=False, indent=4))
                i += 1
                org = contents[i].replace("\n", "")
                #print("<table class='col-head-type1'>")
                classs = [

                ]
            else:
                menu, price = contents[i].replace("\n", "").replace("円", "").split(" ")
                res = template.replace("menu", menu).replace("price", price)
                clas = {
                    "name": menu,
                    "price": price
                }
                classs.append(clas)
                #print(res)
            i+=1
        print(json.dumps(classs, sort_keys=False,
                         ensure_ascii=False, indent=4))
        #print("</table>")
            
                


if __name__ == "__main__":
    main()
