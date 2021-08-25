function clearCanvas(context, canvas) {
    context.clearRect(0, 0, canvas.width, canvas.height);
    var w = canvas.width;
    canvas.width = 1;
    canvas.width = w;
}

function roundUp(num, precision) {
    precision = Math.pow(10, precision)
    return Math.ceil(num * precision) / precision
}

function prepare_references(references, urls) {
    var size = roundUp(references.length / 2, 0);
    var arrayOfArrays = [];
    for (var i = 0; i < references.length; i += size) {
        arrayOfArrays.push(references.slice(i, i + size));
    }
    $.each(arrayOfArrays, function (key, partreference) {
        $("#refcol" + (key + 1)).html("");
        if (partreference.length > 0) {
            $("#refcol" + (key + 1)).append("<ul>");
        }
        $.each(partreference, function (innerkey, reference) {
            $("#refcol" + (key + 1)).append("<li>" + reference + "</li>");
        });
        if (partreference.length > 0) {
            $("#refcol" + (key + 1)).append("</ul>");
        }
    });
    if (references.length > 0) {
        $("#refdata").html("<h4>Verwendete Daten abgerufen von:</h4>");
        $("#refdata").append("<ul>");
        $.each(urls, function (innerkey, url) {
            $("#refdata").append("<li>" + url + "</li>");
        });
        $("#refdata").append("</ul>");
    }
}

Date.prototype.getMJD = function () {
    return this.getJulian() - 2400000.5;
}

Date.prototype.getJulian = function () {
    return (this / 86400000) - (this.getTimezoneOffset() / 1440) + 2440587.5;
}

function toFixedScientific(x, numDecimals) {
    e = parseInt(x.toString().split('e')[1]);
    float = parseFloat(x.toString().split('e')[0]);
    float = float.toFixed(numDecimals);
    if (e) {
        float = (float.toString()) + "e" + e;
    }
    return float;
}

function convertFloatNumber(float) {
    return Math.abs(parseFloat(float));
}

function getExponentOfFloat(float) {
    // return -15;
    float = float.toString();
    eIndex = float.indexOf("e");
    if (eIndex === -1) {
        return 0;
    }
    return parseInt(float.substr(eIndex + 1, float.length));
}