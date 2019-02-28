GV={};


//function lancée au document ready
$(document).ready(function()
{

    GV.launchFunction();//function qui est lancée sur toutes les pages
    //handleSubmenu();


});

GV.showLoading=function()
{
    //$("#mainWrapper").css('opacity',0.5);
    $("#mainWrapper").transition({ opacity: 0.2 });
    $("#mainLoader").transition({ opacity: 1 });
};

GV.hideLoading=function()
{
    //$("#mainWrapper").css('opacity',0.5);
    $("#mainWrapper").transition({ opacity: 1 });
    $("#mainLoader").transition({ opacity: 0 });
};


