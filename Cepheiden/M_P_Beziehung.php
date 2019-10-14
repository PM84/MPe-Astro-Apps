<html>
    <head>
        <meta charset="utf-8"/> 
        <title>Cepheiden - Periode Helligkeit Beziehung</title>
        <link rel="stylesheet" href="css/bootstrap.min.css"> 
        <link rel="stylesheet" href="css/switches.css"> 
        <link rel="stylesheet" href="css/range.css"> 
        <script src="js/jquery-3.3.1.min.js"></script>
        <!--        <script src="data/0005_Cepheid_light_curve.js"></script>//-->
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
                <h3>Anzahl Cepheiden auswählen: </h3>
                <select class="form-control" id="CephSelect">
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="350" selected>350</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
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
                        Hilfslinien für Ermittlung der mittleren absoluten Helligkeit
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
            drawImage()
            var canvas=document.getElementById("hdr");
            canvasWidth=$( "#divCan" ).width()
            canvas.width=canvasWidth
            canvasHeight=canvasWidth*0.9;
            canvas.height=canvasHeight;
            var margin_y=70;
            var margin_y_bottom=50;
            var margin_x=60;
            var widthBox=canvasWidth-2*margin_x;
            var heightBox=canvasHeight-margin_y-margin_y_bottom;
            var ctx=canvas.getContext("2d");

            //             var arrayLength = cephFiles.length;

            //             console.log("start");
            function drawImage(){
                $.getJSON("data/cepheiden_korrigiert.js",function(json) {
                    var cepheiden= json;
                    var pfMin=0;               // Minimum auf der Zeit-Achse

                    var pfMax=29;               // Minimum auf der Zeit-Achse
                    var dpfpp=Math.log10(pfMax)/(canvasWidth-2*margin_x);  // Zeit pro Pixel
                    //                     console.log("Cephs:"+cepheiden);
                    clearCanvas(ctx, canvas);
                    //                     cepheiden.forEach(function(key) {
                    //                             console.log(key);
                    drawHRD(cepheiden,pfMax,dpfpp);
                    //                     })
                    setStars(cepheiden,pfMax,pfMin,dpfpp,id="", yShift=0)
                });
            }

            function drawHRD(cepheiden,pf_Max,dpfpp){
                //                 console.log(cepheiden);
                // Box für HRD zeichnen
                var ctx=canvas.getContext("2d");
                ctx.strokeStyle="#000";
                ctx.lineWidth=1;
                ctx.strokeRect(margin_x,margin_y,widthBox,heightBox);
                ctx.stroke();

                // x-Achse (Zeit-Achse)
                var incr=1
                //                     if(timeMax<=10){var incr=1}else if(timeMax<=20){var incr=2;}else{incr=5;}
                for (pf = 0; pf <= pf_Max; pf+=incr) {
                    if(pf<10){incr=1}else if(pf<20){var incr=2;}else{incr=5;}
                    var x_t=Math.log10(pf)/dpfpp+margin_x;
                    var y_t=(margin_y+heightBox);
                    line(x_t, y_t,x_t, y_t+5, ctx,"#000");
                    //                     console.log("x_t: "+x_t);
                    text(x_t-10,y_t+20,pf,ctx,"10px Arial",Math.Pi/2,"#000")
                }

                // Beschriftung scheinbare Helligkeits-Achse
                text(margin_x/2-10,(heightBox/2+margin_y), "absolute Helligkeit",ctx,"16px Arial",-Math.PI/2,"#000","center");

                // Beschriftung Zeit-Achse
                text(widthBox/2+margin_x,y_t+45, "Periode in d",ctx,"16px Arial",Math.PI*2,"#000","center");

                // Diagramm Titel
                text(canvasWidth/2,margin_y/2-10,"Periode-Helligkeit-Beziehung",ctx,"20px Arial",0,"black","center")
                text(canvasWidth/2,margin_y/2+5,"von Cepheiden der großen Magellanschen Wolke",ctx,"12px Arial",0,"black","center")

                text(canvasWidth-5,canvasHeight-10,String.fromCharCode(169)+" StR Peter Mayer,2018",ctx,"10px Arial",0,"black","end")

            }

            function setStars(cepheiden,pfMax,pfMin,dpfpp,id="", yShift=0){
                var pf=parseFloat(cepheiden['pf']);
                var MagMin=1;               // Minimum auf der T-Achse
                var MagMax=-8;              // Maximum auf der T-Achse
                var dMag=MagMax-MagMin
                var dMagShift=18.64         // Bereinigung der Messwerte um die Entfernung der Mangelanschen Wolke
                var dMagpp=dMag/heightBox;  // Helligkeit pro Pixel
                dMagpp=dMagpp.toFixed(4);
                var MagMaxAxis=Math.floor(MagMax)
                var MagMinAxis=roundUp(MagMin, 0)

                var dMagAxis=(MagMaxAxis-MagMinAxis)/10
                //                     $( "#dist" ).html( cepheiden['dist_pc'] );
                //                     $( "#metallicity" ).html( cepheiden['metallicity'] );
                //                                 console.log(a_g_val);
                $('.RangeClsHor').attr('min', MagMax);
                $('.RangeClsHor').attr('max', MagMin);
                $('.RangeClsHor').attr('value', (MagMin+MagMax)/2);

                $('.RangeClsVert').attr('min', pfMin+1);
                $('.RangeClsVert').attr('max', pfMax);
                $('.RangeClsVert').attr('value', (MagMin+MagMax)/2);

                $json_object=cepheiden;
                jLauf=0;

                for(jLauf=0;jLauf<$("#CephSelect").val();jLauf++){
                    //                     var x_t=Math.log10($json_object[key]['pf'])/dpfpp+margin_x;
                    //                     var y_t=(MagMax-(parseFloat($json_object[key]['m_g'])-dMagShift))/dMagpp+margin_y
                    var x_t=Math.log10(cepheiden[jLauf]['pf']+1)/dpfpp+margin_x;
                    //                         console.log(cepheiden[jLauf]['pf']);
                    var y_t=(MagMax-(parseFloat(cepheiden[jLauf]['m_g'])-dMagShift))/dMagpp+margin_y
                    //                             point(x_t+(pf*i)/dtimepp, (MagMax-(parseFloat($json_object[key]['VisMag'])))/dMagpp+margin_y,2, ctx,"gray","gray");
                    if(x_t>margin_x && x_t<margin_x+widthBox){
                        point(x_t, y_t,2, ctx,"#000","#000");
                    }

                    if(jLauf>$("#clusterSelect").val()){break;}
                    //                             point(x_t+(pf*i)/dtimepp, (MagMax-$json_object[key]['VisMag']-MagShift+a_g_val)/dMagpp+margin_y,2, ctx,"#000","#000");
                    //                             console.log((parseFloat($json_object[key]['VisMag'])-parseFloat(extinction)));
                    //                         console.log($json_object[key]['M_g']);
                    //                 })
                }

                // y-Achse - absolute Helligkeit
                for (i = MagMaxAxis; i <=MagMinAxis; i-=dMagAxis) { 
                    if(margin_y+(MagMax-i)/dMagpp<heightBox+2*margin_y && margin_y+(MagMax-i)/dMagpp>margin_y){
                        y_axis=margin_y+(MagMax-i)/dMagpp;
                        //                         console.log(y_axis);
                        line(margin_x-5, y_axis,margin_x, y_axis, ctx,"#000");
                        text(margin_x-30,y_axis+4, i.toFixed(2),ctx,"10px Arial",Math.Pi/2,"#000")
                    }
                }



                if($("#vglLinie").is(":checked")){
                    var newMag_1=$("#hl_1").val()
                    var act_y_Pos_1=Math.round((MagMax-newMag_1)/dMagpp+margin_y)
                    draw_horizontal_line(act_y_Pos_1,Math.round(newMag_1*100)/100);


                    var newMag_2=$("#hl_2").val()
                    var act_y_Pos_2=Math.round((MagMax-newMag_2)/dMagpp+margin_y)
                    draw_horizontal_line(act_y_Pos_2,Math.round(newMag_2*100)/100);
                }

                if($("#vertLine").is(":checked")){
                    var newPerPos_1=$("#vl_1").val()
                    //                     console.log(newPerPos);
                    var act_x_Pos_1=Math.round((Math.log10(newPerPos_1))/dpfpp+margin_x)
                    if(act_x_Pos_1>=margin_x){
                        draw_vertical_line(act_x_Pos_1,Math.round((newPerPos_1)*100)/100);
                    }

                    var newPerPos_2=$("#vl_2").val()
                    var act_x_Pos_2=Math.round((Math.log10(newPerPos_2))/dpfpp+margin_x)
                    if(act_x_Pos_2>=margin_x){
                        draw_vertical_line(act_x_Pos_2,Math.round((newPerPos_2)*100)/100);
                    }
                }

                var steig=-(newMag_1-newMag_2)/(Math.log10(newPerPos_1/newPerPos_2))
                var yAAS=((newMag_1-steig*Math.log10(newPerPos_1)))

                x_start=Math.log10(newPerPos_1)/dpfpp+margin_x
                y_start=(MagMax-newMag_1)/dMagpp+margin_y
                x_end=Math.log10(newPerPos_2)/dpfpp+margin_x
                y_end=(MagMax-newMag_2)/dMagpp+margin_y
                line(x_start, y_start,x_end, y_end, ctx,"green")

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

            //             });




        </script>
    </body>
</html>