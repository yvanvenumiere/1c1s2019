GV={};


//function lancée au document ready
$(document).ready(function()
{

    GV.launchFunction();//function qui est lancée sur toutes les pages
    //handleSubmenu();

    $(".customcheckbox").click(function()
    {
        if($(this).attr('ischecked')=="false")
        {
            $(this).attr('ischecked',"true");
        }
        else
        {
            $(this).attr('ischecked',"false");
        }
    });


});

GV.showLoading=function()
{
    //$("#mainWrapper").css('opacity',0.5);
    $("#mainWrapper").transition({ opacity: 0.2 });
    $("#mainLoader").transition({ opacity: 1 });
};

GV.getJsId=function(toErase,subject)
{
    var retour=subject.replace(toErase,"");
    return retour;
};

GV.hideLoading=function()
{
    //$("#mainWrapper").css('opacity',0.5);
    $("#mainWrapper").transition({ opacity: 1 });
    $("#mainLoader").transition({ opacity: 0 });
};

GV.hightlightError=function(theselector)
{
    $(theselector).css('color',"red !important");
};




