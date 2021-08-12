<html>
    <head>
        <meta charset="utf-8"/> 
        <title>Cepheiden - Periode Helligkeit Beziehung</title>
        <link rel="stylesheet" href="css/bootstrap.min.css"> 
        <link rel="stylesheet" href="css/switches.css"> 
        <link rel="stylesheet" href="css/range.css"> 
        <script src="js/jquery-3.3.1.min.js"></script>
        <!--        <script src="data/0005_Cepheid_light_curve.js"></script>//-->
        <script src="data/List_of_light_curves.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/mpe_draw.js"></script>

    </head>
    <body style=" max-width:1280px; margin: 0 auto;">
        <div class="row" style="margin:0; padding:5px; border: solid 1px gray">
            <div id="divCan" class="col-sm-8">
                <center>
                    <canvas id="hdr" style="margin:0; background:light:blue; border: solid 1px black;"></canvas>
                </center>
            </div>
            <div class="col-sm-4">
                <h3>Cepheid auswählen: </h3>
                <select class="form-control" id="CephSelect">
                </select>
                <hr>
                <div  id="dist"></div>
                <div  id="metallicity"></div>
                <h3>Optionen</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        Hilfslinien zur Periodenbestimmung
                        <div class="material-switch pull-right">
                            <input id="vertLine" type="checkbox" class="options_vl_cb"/>
                            <label for="vertLine" class="label-success" style="margin-top: 10px;"></label>
                        </div>
                        <div id="vl" class="hidden">
                            <hr>
                            <div>
                                vertikale Hilfslinie 1
                                <input id="vl_1" type="range" class="RangeStyle1 Red options_vl RangeClsVert" min="-2" max="2" step="0.001">
                                vertikale Hilfslinie 2
                                <input id="vl_2" type="range" class="RangeStyle1 Red options_vl RangeClsVert" min="-2" max="2" step="0.001">
                            </div>
                        </div>

                    </li>
                    <li class="list-group-item">
                        Hilfslinien für Ermittlung der scheinbaren Helligkeit
                        <div class="material-switch pull-right">
                            <input id="vglLinie" type="checkbox" class="options_hl_cb"/>
                            <label for="vglLinie" class="label-success" style="margin-top: 10px;"></label>
                        </div>
                        <div id="hl" class="hidden">
                            <hr>
                            <div>
                                horizontale Hilfslinie 1
                                <input id="hl_1" type="range" class="RangeStyle1 Blue options_hl RangeClsHor" min="-2" max="2" step="0.001">
                                horizontale Hilfslinie 2
                                <input id="hl_2" type="range" class="RangeStyle1 Blue options_hl RangeClsHor" min="-2" max="2" step="0.001">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <script>
            var canvas=document.getElementById("hdr");
            canvasWidth=$( "#divCan" ).width()
            canvas.width=canvasWidth
            canvasHeight=canvasWidth*0.9;
            canvas.height=canvasHeight;
            var margin_y=70;
            var margin_y_bottom=60;
            var margin_x=60;
            var widthBox=canvasWidth-2*margin_x;
            var heightBox=canvasHeight-margin_y-margin_y_bottom;
            var ctx=canvas.getContext("2d");

            var arrayLength = cephFiles.length;
            for (var i = 0; i < arrayLength; i++) {
                var j=i+1;
                var o = new Option("option text", cephFiles[i]);
                /// jquerify the DOM object 'o' so we can use the html method
                $(o).html("Cepheid Nr. "+j);
                $("#CephSelect").append(o);
            }

            $( document ).ready(function() {
                // var cepheiden



                function drawImage(){
                    $.getJSON("data/"+$("#CephSelect").val(),function(json) {
                        var cepheiden= json;
                        var timeMin=0;               // Minimum auf der Zeit-Achse
                        var pf=parseFloat(cepheiden['pf']);
                        var timeMax=pf*2;               // Minimum auf der Zeit-Achse
                        var dtime=timeMax-timeMin;      // Zeitunterschied
                        var dtimepp=dtime/(canvasWidth-2*margin_x);  // Zeit pro Pixel

                        drawHRD(cepheiden,timeMax,dtimepp);
                        setStars(cepheiden,timeMax,timeMin,dtimepp,id="", yShift=0)
                    });
                }

                function drawHRD(cepheiden,timeMax,dtimepp){

                    // Box für HRD zeichnen
                    var ctx=canvas.getContext("2d");
                    ctx.strokeStyle="#000";
                    ctx.lineWidth=2;
                    ctx.strokeRect(margin_x,margin_y,widthBox,heightBox);
                    ctx.stroke();

                    // x-Achse (Zeit-Achse)
                    if(timeMax<=10){var incr=1}else if(timeMax<=20){var incr=2;}else{incr=5;}
                    for (t = 0; t <= timeMax; t+=incr) { 
                        var x_t=t/dtimepp+margin_x;
                        var y_t=(margin_y+heightBox);
                        line(x_t, y_t,x_t, y_t+5, ctx,"#000");
                        //                     console.log("x_t: "+x_t);
                        text(x_t-10,y_t+20,t,ctx,"10px Arial",Math.Pi/2,"#000")
                    }

                    // Beschriftung scheinbare Helligkeits-Achse
                    text(margin_x/2-10,(heightBox/2+margin_y), "scheinbare Helligkeit",ctx,"20px Arial",-Math.PI/2,"#000","center");

                    // Beschriftung Zeit-Achse
                    text(widthBox/2+margin_x,y_t+45, "Zeit in d",ctx,"20px Arial",Math.PI*2,"#000","center");

                    // Diagramm Titel
                    text(canvasWidth/2,margin_y/2-5,"Lichtkurve eines Cepheiden",ctx,"25px Arial",0,"black","center")

                    text(canvasWidth-5,canvasHeight-10,String.fromCharCode(169)+"  OStR Peter Mayer, 2017-2021",ctx,"10px Arial",0,"black","end")

                }

                function setStars(cepheiden,timeMax,timeMin,dtimepp,id="", yShift=0){
                    var a_g_val=parseFloat(cepheiden['a_g_val']);

                    var MagMin=cepheiden['MagMin']-cepheiden['extinction']+1;               // Minimum auf der T-Achse
                    var MagMax=cepheiden['MagMax']-cepheiden['extinction']-1;              // Maximum auf der T-Achse
                    var extinction=cepheiden['extinction'];
                    var dMag=MagMax-MagMin;      // Temperaturunterschied
                    var dMagpp=dMag/heightBox;  // Temperatur pro Pixel
                    dMagpp=dMagpp.toFixed(4);
                    var MagMaxAxis=Math.floor(MagMax)
                    var MagMinAxis=roundUp(MagMin, 0)
                    var dMagAxis=(MagMaxAxis-MagMinAxis)/10
                    var pf=parseFloat(cepheiden['pf']);
                    //                     $( "#dist" ).html( cepheiden['dist_pc'] );
                    //                     $( "#metallicity" ).html( cepheiden['metallicity'] );
                    console.log(a_g_val);
                    $('.RangeClsHor').attr('min', MagMaxAxis);
                    $('.RangeClsHor').attr('max', MagMinAxis);
                    $('.RangeClsHor').attr('value', (MagMin+MagMax)/2);

                    $('.RangeClsVert').attr('min', timeMin);
                    $('.RangeClsVert').attr('max', timeMax);
                    $('.RangeClsVert').attr('value', (MagMin+MagMax)/2);


                    clearCanvas(ctx, canvas);
                    drawHRD(cepheiden,timeMax,dtimepp);
                    MagShift=0

                    // Messzeitpunkte werden geladen
                    //                     console.log(cepheiden)
                    //                     console.log(cepheiden['LK'])
                    $json_object=cepheiden['LK'];
                    for(i=0;i<=1;i++){ // zur Verdopplung der Periode im graphischen Fenster
                        Object.keys($json_object).forEach(function(key) {
                            var x_t=$json_object[key]['PfPart']/dtimepp+margin_x;
                            //                             point(x_t+(pf*i)/dtimepp, (MagMax-(parseFloat($json_object[key]['VisMag'])))/dMagpp+margin_y,2, ctx,"gray","gray");
                            point(x_t+(pf*i)/dtimepp, (MagMax-(parseFloat($json_object[key]['VisMag'])-parseFloat(extinction)))/dMagpp+margin_y,2, ctx,"#000","#000");
                            //                             point(x_t+(pf*i)/dtimepp, (MagMax-$json_object[key]['VisMag']-MagShift+a_g_val)/dMagpp+margin_y,2, ctx,"#000","#000");
                            //                             console.log((parseFloat($json_object[key]['VisMag'])-parseFloat(extinction)));
                            console.log((parseFloat($json_object[key]['VisMag'])));
                        })
                    }

                    // y-Achse - scheinbare Helligkeit
                    for (i = MagMaxAxis; i <=MagMinAxis; i-=dMagAxis) { 
                        if(margin_y+(MagMax-i)/dMagpp<heightBox+2*margin_y && margin_y+(MagMax-i)/dMagpp>margin_y){
                            y_axis=margin_y+(MagMax-i)/dMagpp;
                            //                         console.log(y_axis);
                            line(margin_x-5, y_axis,margin_x, y_axis, ctx,"#000");
                            text(margin_x-30,y_axis+4, i.toFixed(2),ctx,"10px Arial",Math.Pi/2,"#000")
                        }
                    }



                    if($("#vglLinie").is(":checked")){
                        var newMag=$("#hl_1").val()
                        var act_y_Pos=Math.round((MagMax-newMag)/dMagpp+margin_y)
                        draw_horizontal_line(act_y_Pos,Math.round(newMag*100)/100);


                        var newMag=$("#hl_2").val()
                        var act_y_Pos=Math.round((MagMax-newMag)/dMagpp+margin_y)
                        draw_horizontal_line(act_y_Pos,Math.round(newMag*100)/100);
                    }

                    if($("#vertLine").is(":checked")){
                        var newPerPos=$("#vl_1").val()
                        var act_x_Pos=Math.round((newPerPos)/dtimepp+margin_x)
                        draw_vertical_line(act_x_Pos,Math.round(newPerPos*100)/100);


                        var newPerPos=$("#vl_2").val()
                        var act_x_Pos=Math.round((newPerPos)/dtimepp+margin_x)
                        draw_vertical_line(act_x_Pos,Math.round(newPerPos*100)/100);
                    }



                }

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

                function draw_horizontal_line(y_line,DisplVal){
                    line(margin_x, y_line,widthBox+margin_x+5, y_line, ctx,"blue")
                    draw_rectFill(widthBox+margin_x+5,y_line-10,widthBox+margin_x+55,y_line+10,ctx,2,0,"#fff",1)
                    draw_rect(widthBox+margin_x+5,y_line-10,widthBox+margin_x+55,y_line+10,ctx,2,0,"blue")
                    text(widthBox+margin_x+30,y_line+5,DisplVal,ctx,"15px Arial",Math.Pi/2,"blue","center")
                }

                function draw_vertical_line(t_Puls_Pos,t_val){
                    line(t_Puls_Pos, heightBox+margin_y,t_Puls_Pos, margin_y-5, ctx,"red")
                    draw_rectFill(t_Puls_Pos-25,margin_y-5,t_Puls_Pos+25,margin_y-25,ctx,2,0,"#fff",1)
                    draw_rect(t_Puls_Pos-25,margin_y-5,t_Puls_Pos+25,margin_y-25,ctx,2,0,"red")
                    text(t_Puls_Pos,margin_y-10,t_val,ctx,"15px Arial",Math.Pi/2,"red","center")
                }

                $( "#CephSelect" ).change(function() {
                    drawImage();
                });
                $( ".options_hl" ).mousemove(function() {
                    drawImage();
                });
                $( ".options_hl_cb" ).change(function() {
                    $('#hl').toggleClass('hidden');
                    drawImage();
                });
                $( ".options_vl" ).mousemove(function() {
                    drawImage();
                });
                $( ".options_vl_cb" ).change(function() {
                    $('#vl').toggleClass('hidden');
                    drawImage();
                });
                drawImage()

            });




        </script>
    </body>
</html>