

GV={};
GV.launchFunction;//function
GV.cssWaiter=new cssWaiter();
GV.cssWaiter.setStyle({
    bgColor:"#FFFFFF",
    radius:80,
    mainColor:"#CCCCCC",
    turnColor:"#318be3",
    border:"10px"
});

GV.cssWaiterSmall=new cssWaiter();
GV.cssWaiterSmall.setStyle({
    bgColor:"none",
    radius:15,
    mainColor:"#CCCCCC",
    turnColor:"#318be3",
    border:"2px"
});
GV.previousPage=false;


//function lancée au document ready
$(document).ready(function()
{

    //gestion du mobile et du resize
    if($(window).width()<1000)
    {
        GV.isMobile=true;
    }
    GV.resizeFunction();
    $(window).resize(function()
    {
        GV.resizeFunction();
    });
    GV.launchFunction();//function qui est lancée sur toutes les pages






});

GV.resizeFunction=function()
{


};

GV.isInt=function(value) {
    var x = parseFloat(value);
    return !isNaN(value) && (x | 0) === x;
};


GV.getJsId=function(toErase,subject)
{
    var retour=subject.replace(toErase,"");
    return retour;
};

GV.getObjectFromJsonArray=function(json,propertyName,propertyValue)
{
    for(var i=0;i<json.length;i++)
    {
        if(json[i][propertyName]==propertyValue)
        {
            return json[i];
        }
    }

    return false;
};








