function point(x, y,r, canv,colorLine,colorFill){
    canv.beginPath();
    canv.lineWidth = 2;
    canv.arc(x, y, r, 0, 2 * Math.PI, true);
    canv.fillStyle = colorLine;
    canv.strokeStyle=colorFill;
    canv.fill();
    canv.stroke();
}

function line(x_start, y_start,x_end, y_end, canv,colorLine){
    canv.beginPath();
    canv.moveTo(x_start,y_start);
    canv.lineTo(x_end, y_end);
    canv.strokeStyle=colorLine;
    canv.stroke();
}

function draw_rectFill(x1,y1,x2,y2,canv,lineThickness="2",rotation,colorFill="#FF0000",transp=0){
    canv.translate(x1,y1);
    canv.beginPath();
    canv.globalAlpha = transp;
    canv.fillStyle=colorFill;
    canv.fillRect(0,0,x2-x1,y2-y1);
    canv.rotate(rotation);
    canv.stroke();
    canv.translate(-x1,-y1);
}

function draw_rect(x1,y1,x2,y2,canv,lineThickness="2",rotation,colorLine="black"){
    canv.translate(x1,y1);
    canv.beginPath();
    canv.lineWidth=lineThickness;
    canv.strokeStyle=colorLine;
    canv.rect(0,0,x2-x1,y2-y1);
    canv.rotate(rotation);
    canv.stroke();
    canv.translate(-x1,-y1);
}

function text(x,y, text,canv,fontStyle,rotation,colorLine,align="start"){
    canv.translate(x,y);
    canv.rotate(rotation);
    canv.font = fontStyle;
    canv.fillStyle = colorLine;
    canv.textAlign=align;
    canv.fillText(text,0,0);
    canv.rotate(-rotation);
    canv.translate(-x,-y);
}


function gradient(a, b) {
    return (b.y-a.y)/(b.x-a.x);
}

function bzCurve(points, f, t) {
    //f = 0, will be straight line
    //t suppose to be 1, but changing the value can control the smoothness too
    if (typeof(f) == 'undefined') f = 0.3;
    if (typeof(t) == 'undefined') t = 0.6;

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    var m = 0;
    var dx1 = 0;
    var dy1 = 0;

    var preP = points[0];
    for (var i = 1; i < points.length; i++) {
        var curP = points[i];
        nexP = points[i + 1];
        if (nexP) {
            m = gradient(preP, nexP);
            dx2 = (nexP.x - curP.x) * -f;
            dy2 = dx2 * m * t;
        } else {
            dx2 = 0;
            dy2 = 0;
        }
        ctx.bezierCurveTo(preP.x - dx1, preP.y - dy1, curP.x + dx2, curP.y + dy2, curP.x, curP.y);
        dx1 = dx2;
        dy1 = dy2;
        preP = curP;
    }
    ctx.stroke();
}