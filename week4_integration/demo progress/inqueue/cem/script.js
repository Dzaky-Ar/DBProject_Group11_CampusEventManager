const listNama = document.getElementById("listNama");
const more = document.getElementById("more");
const winnings = document.getElementById("winnings");
const morewin = document.getElementById("morewin");
const root = document.querySelector(":root");
const theme = document.getElementById("light");
const menu = document.querySelector(".leftNav > ul > li:nth-child(1)");
var next = 2;

if(document.documentElement.clientWidth > 680){
    for(let n=2; n<=document.querySelector(".leftNav > ul").childElementCount; n++){
        document.querySelector(".leftNav > ul > :nth-child("+ n +")").classList.remove("show"); 
    }
}

if(document.documentElement.clientWidth <= 840){
    morewin.innerHTML = "&#8744 next(" + next/2 +"/5)";
}

window.addEventListener("resize", function(){
    document.documentElement.clientWidth <= 840 ? morewin.innerHTML = "&#8744 next(" + next/2 +"/5)" : morewin.innerHTML = "&#8744 More";
    
    if(document.documentElement.clientWidth > 680){
        for(let n=2; n<=document.querySelector(".leftNav > ul").childElementCount; n++){
            document.querySelector(".leftNav > ul > :nth-child("+ n +")").classList.remove("show"); 
        }
    }
});

menu.addEventListener("click", function(){
    for(let n=2; n<=document.querySelector(".leftNav > ul").childElementCount; n++){
        document.querySelector(".leftNav > ul > :nth-child("+ n +")").classList.toggle("show");
        
    }
    console.log(menu.innerHTML.length);
    menu.innerHTML.length == 6 ? menu.innerHTML = "&#128938 Menu." :  menu.innerHTML = "&#9776 Menu"; 
    
})

more.addEventListener("click", function(){
    listNama.classList.toggle("collapse");
    listNama.classList.contains("collapse") ? more.innerHTML = "Show less" : more.innerHTML = "3+ more" ;
})



morewin.addEventListener("click", function(){
    if(document.documentElement.clientWidth > 840){
    winnings.classList.toggle("collapse_2");
    winnings.classList.contains('collapse_2') ? morewin.innerHTML = "&#8743 Less" : morewin.innerHTML = "&#8744 More";
    }
    else{
        if(next == winnings.childElementCount){
            next=0
            for(let n=1; n<=winnings.childElementCount; n++){
                document.querySelector("#winnings > :nth-child("+n+")").style.setProperty('order','1');
            }
        }
        next +=2;
        document.querySelector("#winnings > :nth-child("+ next +")").style.setProperty('order',0-next);  
        document.querySelector("#winnings > :nth-child("+ (next-1) +")").style.setProperty('order',0-next);
        morewin.innerHTML = "&#8744 next(" + next/2 +"/5)";
    }
})

document.getElementById("moreAnalytics").addEventListener("click", function(){
    document.getElementById("analisis_0").id = "analisis";
})

theme.addEventListener("click", function(){
    root.style.getPropertyValue('--main-bg-color') == "white" ? (root.style.setProperty('--main-bg-color','black'), root.style.setProperty('--opposite-color','white'))
    :(root.style.setProperty('--main-bg-color','white'), root.style.setProperty('--opposite-color','black'));
    
})

