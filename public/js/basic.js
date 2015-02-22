function cl(mix){
    if (console && console.log) console.log(mix);
}

function setFormValidation(scope, name, res){
    if (res && res.errors && !jQuery.isEmptyObject(res.errors)) {
        scope[name].$invalid = true;

        for (var key in res.errors) {
            if (key in scope[name]) {
                for (var validationKey in res.errors[key]) {
                    scope[name][key].$dirty = true;
                    scope[name][key].$setValidity(validationKey.toLowerCase(), false);
                }
            }
        }
    }
}

function getPinIco(data){
    var url = $('base').attr('href') + 'img/pin/';

    time = Number(data);

    if (isNaN(time)){
        sign = data;
    } else {
        var lastReport = new Date(time);

        var v = new Date(strtotime('-5 min') * 1000);
        var x = new Date(strtotime('-2 hour') * 1000);

        var sign = 'Q';
        if (v <= lastReport)  sign = 'V';
        else if (x <= lastReport) sign = 'X';
    }
    return url + sign + '.png';
}

function getPinIcon(data, markerType){
    var url = $('base').attr('href') + 'img/pin/';

    time = Number(data);

    if (isNaN(time)){
        sign = data;
    } else {
        var lastReport = new Date(time);

        var v = new Date(strtotime('-5 min') * 1000);
        var x = new Date(strtotime('-2 hour') * 1000);

        var sign = 'empty';
        if(markerType){
            sign = 'Q_'+markerType;
            if (v <= lastReport)  sign = 'V_'+markerType;
            else if (x <= lastReport) sign = 'X_'+markerType;
        }

    }
    return url + sign + '.png';
}

function convertTimeStamp(timeStamp){
    var d = new Date(timeStamp);
    return d.toUTCString();
}