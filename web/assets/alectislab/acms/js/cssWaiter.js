/** @constructor cssWaiter*/
function cssWaiter(_options)
{
    var current=this;

////////////////////PROPERTIES/////////////////////////

    /**
     *
     */
    this.divCont=false;


    /**
     *
     */
    this.divLoader=false;


    /**
     *
     */
    this.styles=
        {
            bgColor:"#FFFFFF",
            radius:80,
            mainColor:"#CCCCCC",
            turnColor:"#d90611",
            border:"10px"
        };

    /**
     *
     */
    this.resultDiv='something';

    /**
     *
     */
    this.currentSelector='something';


    /**
     *
     */
    this.mainColor='#CCCCCC';


    /**
     *
     */
    this.turnColor='#d90611';


    /**
     *
     */
    this.border='10px';


    /**
     *
     */
    this.radius=80;


    /**
     *
     */
    this.speed='something';

    /**
     *
     */
    this.divContId=new Date().getTime()+"DivCont"+Math.round((Math.random()*100));

    /**
     *
     */
    this.divLoaderId=new Date().getTime()+"DivLoader"+Math.round((Math.random()*100));




////////////////////METHODS/////////////////////////
    /**
     *
     */
    this.show=function(theSelector,position)
    {

        current.currentSelector=theSelector;
        //current.hide();
        if(!current.divCont)
        {
            current.divCont=document.createElement("div");
            current.divCont.setAttribute("id",current.divContId);
            current.divCont.setAttribute("class","cssWaiterComponent");
        }


        $(current.divCont).css('opacity',"0");
        $(current.divCont).css('width',$(theSelector).width()+"px");
        $(current.divCont).css('height',$(theSelector).height()+"px");
        $(current.divCont).css('background-color',current.styles.bgColor);
        $(current.divCont).css('position',"absolute");
        $(current.divCont).css('top',"0px");
        $(current.divCont).css('left',"0px");
        $(current.divCont).css('opacity',0);

        $(current.divCont).appendTo(theSelector);

        $(current.divCont).animate({
            opacity:1
        }, 100, function()
        {
            if(!current.divLoader)
            {
                current.divLoader=document.createElement("div");
                current.divLoader.setAttribute("id",current.divLoaderId);
                current.divLoader.setAttribute("class","cssWaiterComponent");
            }



            $(current.divLoader).css('width',current.styles.radius+"px");
            $(current.divLoader).css('height',current.styles.radius+"px");
            $(current.divLoader).css('background-color',current.styles.bgColor);
            $(current.divLoader).css('position',"absolute");
            //$(current.divLoader).css('top',"0px");
            //$(current.divLoader).css('left',"0px");
            $(current.divLoader).css('border-radius',"50%");
            $(current.divLoader).css('border',current.styles.border+" solid "+current.styles.mainColor);
            $(current.divLoader).css('border-top',current.styles.border+" solid "+current.styles.turnColor);
            $(current.divLoader).css('animation',"spin 2s linear infinite");
            $(current.divLoader).appendTo(theSelector);

            if(typeof(position)=="undefined")
            {

                var left=($(current.divCont).width()/2)-(current.styles.radius/2);
                var top=($(current.divCont).height()/2)-(current.styles.radius/2);
                $(current.divLoader).css('top',top+"px");
                $(current.divLoader).css('left',left+"px");
            }

            if(typeof(position)!="undefined" && position=="top")
            {
                var left=($(current.divCont).width()/2)-(current.styles.radius/2);
                $(current.divLoader).css('top',"25px");
                $(current.divLoader).css('left',left+"px");
            }

            if(typeof(position)!="undefined" && position=="bottom")
            {

                var left=($(current.divCont).width()/2)-(current.styles.radius/2);
                $(current.divLoader).css('bottom',"25px");
                $(current.divLoader).css('left',left+"px");
            }

        });





    };

    /**
     *
     */
    this.hide=function(fromWhere)
    {
        if(typeof(fromWhere)!="undefined")
        {
            //console.log(fromWhere);
        }

        //console.log(current.divLoader);
        //console.log(current.divCont);
        console.log('hide this');
        $(current.divLoader).css('display',"none");
        $(current.divCont).css('display',"none");
        $(current.divLoader).remove();
        $(current.divCont).remove();
        current.divCont=null;
        current.divLoader=null;
        $(".cssWaiterComponent").css('display','none');
        $(".cssWaiterComponent").remove();
        for(var i=0;i<10;i++)
        {
            setTimeout(current.forceHide,i*200);
        }

    };

    this.forceHide=function()
    {
        $(".cssWaiterComponent").css('display','none');
        $(".cssWaiterComponent").remove();

    };

    /**
     *
     */
    this.showResult=function(message,type,position)
    {

        $(current.divLoader).remove();
        current.resultDiv=document.createElement("div");
        current.resultDiv.setAttribute("id",Math.random()+"ResultDiv");

        $(current.resultDiv).appendTo(current.currentSelector);
        var idAlert=Math.random()+"AlertCW";
        $(current.resultDiv).html("<div  class='alert alert-"+type+"' role='alert'>"+message+" <button id='"+idAlert+"' type='button' class='close' data-dismiss='alert' aria-label='Close'> <span aria-hidden='true'>&times;</span> </button></div>");
        $(current.resultDiv).css('position',"absolute");

        if(typeof(position)=="undefined")
        {

            var left=($(current.currentSelector).width()/2)-($(current.resultDiv).width()/2);
            var top=($(current.currentSelector).height()/2)-($(current.resultDiv).height()/2);
            $(current.resultDiv).css('top',top+"px");
            $(current.resultDiv).css('left',left+"px");
        }

        if(typeof(position)!="undefined" && position=="bottom")
        {

            var left=($(current.currentSelector).width()/2)-($(current.resultDiv).width()/2);
            var top=$(current.currentSelector).height()-($(current.resultDiv).height()*2);
            $(current.resultDiv).css('bottom',"25px");
            $(current.resultDiv).css('left',left+"px");
        }

        if(typeof(position)!="undefined" && position=="top")
        {

            var left=($(current.currentSelector).width()/2)-($(current.resultDiv).width()/2);
            var top=$(current.resultDiv).height()*2;
            $(current.resultDiv).css('top',"25px");
            $(current.resultDiv).css('left',left+"px");
        }



        $(current.resultDiv).click(function()
        {
            $(current.resultDiv).remove();
            $(current.divCont).remove();
        });


    };



    /**
     *
     */
    this.setStyle=function(options)
    {
        current.styles=options;
    };


////////////////////AFFECTATION/////////////////////////
    /*var options;//objject that contains all the parameters
    if (_options == null)
    {
        options=
            {
                _:'something'

            };
    }
    else
    {
        options=_options;
    }
    this.=options._;*/


}