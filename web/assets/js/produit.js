function sendQuestion()
{
    GV.showLoading();
    $.ajax({
        type: 'POST',
        url:pathQuestion,
        data:{email:$("#email").val(),message:$("#message").val()},
        success: function(retour)
        {

            $(".fieldQuestionInput").removeClass("is-invalid");
            GV.hideLoading();
            if(retour.result=="ok")
            {

                $(".fieldQuestionInput").val('');
                $(".fieldQuestionInput").removeClass("is-invalid");

                //$(".container-scroller").scrollTop(0);
                new Noty({

                    text: retour.message,
                    timeout:4000,
                    type:"success"

                }).show();

            }
            else
            {

                var messageRetour="<strong>"+retour.message+"</strong>";
                if(retour.errors)
                {
                    for(var erreur in retour.errors)
                    {

                        if(retour.errors[erreur].isValid==false)
                        {$('#'+erreur).addClass("is-invalid");}
                        //messageRetour+="<br/>"+retour.errors[erreur.];
                    }
                }
                new Noty({

                    text: messageRetour,
                    timeout:4000,
                    type:"error"

                }).show();
            }


        },
        dataType: 'json'
    });
}

function saveLeadAndCreate(domElem)
{
    $("#modalTry").modal('show');
    if(typeof(labelsTryModal[$(domElem).attr('id')])!="undefined")
    {
        $("#modalTryLabel").html(labelsTryModal[$(domElem).attr('id')]);
    }
    else
    {
        $("#modalTryLabel").html(labelsTryModal['default']);
    }
    CURRENT_PLAN=GV.getJsId("try",$(domElem).attr('id'));



}

function createWebsite()
{
    $(".fieldCreateInput").removeClass("is-invalid");
    GV.showLoading();
    $.ajax({
        type: 'POST',
        url:pathCreate,
        data:{emailTry:$("#emailTry").val(),formule:CURRENT_PLAN,conditionsTry:$("#crwcb").attr('ischecked'),idTemplate:ID_TEMPLATE},
        success: function(retour)
        {

            GV.hideLoading();
            if(retour.result=="ok")
            {

                $(".fieldCreateInput").val('');
                $(".fieldCreateInput").removeClass("is-invalid");

                //$(".container-scroller").scrollTop(0);
                new Noty({

                    text: retour.message,
                    timeout:4000,
                    type:"success"

                }).show();

            }
            else
            {

                var messageRetour="<strong>"+retour.message+"</strong>";
                if(retour.errors)
                {
                    for(var erreur in retour.errors)
                    {

                        if(retour.errors[erreur].isValid==false)
                        {$('#'+erreur).addClass("is-invalid");}
                        //messageRetour+="<br/>"+retour.errors[erreur.];
                    }
                }
                new Noty({

                    text: messageRetour,
                    timeout:4000,
                    type:"error"

                }).show();
            }


        },
        dataType: 'json'
    });
}