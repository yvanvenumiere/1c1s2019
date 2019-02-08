function getTimeFromFormatedDate(format,sepa,value,caseType)
{

    var arrayDatas=value.split(sepa);
    var arrayFormat=format.split(sepa);
    var day;
    var month;
    var year;

    if(arrayFormat.length!=3 || arrayDatas.length!=3)
    {
        return false;
    }

    for(var i=0;i<arrayFormat.length;i++)
    {
        if(arrayFormat[i]=="dd"){day=arrayDatas[i];}
        if(arrayFormat[i]=="mm"){month=arrayDatas[i];}
        if(arrayFormat[i]=="yy" || arrayFormat[i]=="yyyy"){year=arrayDatas[i];}
    }

    var theDate=new Date();
    theDate.setFullYear(year);
    theDate.setDate(day);
    theDate.setMonth(month-1);
    if(caseType=="begin")
    {
        theDate.setHours(0);
        theDate.setMinutes(1);
    }
    if(caseType=="end")
    {
        theDate.setHours(23);
        theDate.setMinutes(59);
    }
    return Math.round(theDate.getTime()/1000);

}