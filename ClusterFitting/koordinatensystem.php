<html lang="de">
    <head>
        <title>HRD Cluster Fit</title>
        <meta charset="UTF-8">
    </head>
    <body style="margin:0;">
        <canvas id="hdr" style="margin:0; background:light:blue; border: solid 1px black;">


        </canvas>


        <script>
            var canvas=document.getElementById("hdr");
            canvas.width=window.innerWidth;
            canvas.height=window.innerHeight;
            var margin_y=30;
            var margin_x=30;
            var widthBox=400;
            var heightBox=400;
            var ctx=canvas.getContext("2d");
            ctx.strokeStyle="#000";
            ctx.lineWidth=2;
            ctx.strokeRect(margin_x,margin_y,margin_x+widthBox,margin_y+heightBox);
//              drawBoard();
ctx.stroke();
            for (i = 1; i < widthBox/10; i++) { 
            ctx.beginPath();
            ctx.moveTo(margin_x+i*10,heightBox + margin_y + 5);
            ctx.lineTo(margin_x+i*10,heightBox + margin_y - 5);
            ctx.stroke();
}

        </script>
    </body>
</html>