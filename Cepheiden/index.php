
<html>
    <head>
        <meta charset="utf-8"/> 
        <title>Cepheiden - Periode Helligkeit Beziehung</title>
        <link rel="stylesheet" href="css/bootstrap.min.css"> 
        <link rel="stylesheet" href="css/switches.css"> 
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="data/0001_Cepheid_light_curve.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/mpe_draw.js"></script>
    </head>
    <!--    <body style="width: 900px; margin: 0 auto;">//-->
    <body style=" max-width:1280px; margin: 0 auto;">
        <div class="row" style="margin:0; padding:5px; border: solid 1px gray">
            <div id="divCan" class="col-sm-6">
                <center>
                    <canvas id="hdr" style="margin:0; background:light:blue; border: solid 1px black;"></canvas>
                </center>
            </div>
            <div class="col-sm-6">
                <h3>Sternhaufen: </h3>
                <select class="form-control" id="clusterSelect">
                </select>
                <hr>
                <h3>Teleskopaufnahme: </h3>
                <img id="previewImg" style="width:100%;">
                <hr>
                <h3>Optionen</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        Verlauf der Hauptreihe <br>(absolute Helligkeit)
                        <div class="material-switch pull-right">
                            <input id="HRabsMag" type="checkbox" class="options"/>
                            <label for="HRabsMag" class="label-success"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Vergleichslinie scheinbare<br>und absolute Helligkeit
                        <div class="material-switch pull-right">
                            <input id="vglLinie" type="checkbox" class="options"/>
                            <label for="vglLinie" class="label-success"></label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <script>

/*             for(var cluster in starList)
            {
                $('<option value="'+cluster+'">'+starList[cluster]['Cluster']+'</option>').appendTo('#clusterSelect');
            }
 */

            var canvas=document.getElementById("hdr");
            //             canvasWidth=window.innerWidth;
            canvasWidth=$( "#divCan" ).width()
            //             canvas.width=canvasWidth
            //             canvasHeight=window.innerHeight;
            //             canvas.height=canvasHeight;
            //             canvasWidth=350;
            canvas.width=canvasWidth
            //             canvasHeight=500;
            canvasHeight=canvasWidth*1.4;
            canvas.height=canvasHeight;
            var margin_y=70;
            var margin_y_bottom=40;
            var margin_x=50;
            var widthBox=canvasWidth-3*margin_x;
            var heightBox=canvasHeight-margin_y*2-margin_y_bottom;
            var MagMin=20;               // Minimum auf der T-Achse
            var MagMax=-10;              // Maximum auf der T-Achse
            var dMag=MagMax-MagMin;      // Temperaturunterschied
            var dMagpp=dMag/heightBox;  // Temperatur pro Pixel
            heightBox=Math.abs((Math.abs(MagMax)+Math.abs(MagMin)+1)/dMagpp+margin_y);
            var ctx=canvas.getContext("2d");
            var PulsMin=0.1;               // Minimum auf der T-Achse
            var PulsMinLog10=Math.log10(PulsMin);
            var PulsMax=100;               // Minimum auf der T-Achse
            var PulsMaxLog10=Math.log2(PulsMax);
            //             var TeffMax=Math.log2(17000);              // Maximum auf der T-Achse
            var dPuls=PulsMaxLog10-PulsMinLog10;      // Temperaturunterschied
            var dPulspp=dPuls/widthBox;  // Temperatur pro Pixel

            /*            console.log( Math.round(dTeff));
            console.log(dTeffpp);
            console.log(TeffMinLog2);
            console.log(TeffMaxLog2);
 */

            function drawHRD(){

                // Box für HRD zeichnen
                var ctx=canvas.getContext("2d");
                ctx.strokeStyle="#000";
                ctx.lineWidth=2;
                ctx.strokeRect(margin_x,margin_y,margin_x+widthBox,margin_y+heightBox);
                ctx.stroke();



                // x-Achse
/*                 for (i = PulsMaxLog10; i > Math.round(PulsMaxLog10-dPuls); i--) { 
                    var x_Puls=(PulsMaxLog10-i)/dPulspp+margin_x+10;
                    var y_Teff=(2*margin_y+heightBox);
                    line(x_Puls, y_Teff,x_Puls, y_Teff+5, ctx,"#000");
                                         console.log("x_Puls: "+x_Puls);
                    text(x_Puls-10,y_Teff+20, Math.round(2**i),ctx,"10px Arial",Math.Pi/2,"#000")
                } */
                for (i = PulsMaxLog10; i > Math.round(PulsMaxLog10-dPuls); i--) { 
                    var x_Puls=(PulsMaxLog10-i)/dPulspp+margin_x+10;
                    var y_Teff=(2*margin_y+heightBox);
                    line(x_Puls, y_Teff,x_Puls, y_Teff+5, ctx,"#000");
                                         console.log("x_Puls: "+x_Puls);
                    text(x_Puls-10,y_Teff+20, Math.round(2**i),ctx,"10px Arial",Math.Pi/2,"#000")
                }
                
                
                text(margin_x/2-5,(heightBox/2+margin_y)+20, "scheinbare Helligkeit",ctx,"20px Arial",-Math.PI/2,"#000","center");
                text(widthBox/2+margin_x*1.5,y_Teff+45, "Puls-Periode in d",ctx,"20px Arial",Math.PI*2,"#000","center");
//                 text(widthBox/2+margin_x+90,y_Teff+48, "eff",ctx,"10px Arial",Math.PI*2,"#000","center");
                text(canvasWidth/2,margin_y/2-5,"Hertzsprung Russel Diagramm",ctx,"25px Arial",0,"black","center")
//                 text(canvasWidth/2,margin_y/2+25,$("#clusterSelect :selected").text(),ctx,"20px Arial",0,"black","center")
                text(canvasWidth-5,canvasHeight-10,String.fromCharCode(169)+" StR Peter Mayer,2018",ctx,"10px Arial",0,"black","end")


                if($("#HRabsMag").is(":checked")){
                    draw_main_sequence()
                    // 2. y-Achse - Absolute Helligkeiten
/*                     for (i = 0; i <= Math.abs(MagMax)+Math.abs(MagMin); i++) { 
                        line(widthBox+2*margin_x+5, margin_y+(MagMax-(i+1)/dMagpp),widthBox+2*margin_x, margin_y+(MagMax-(i+1)/dMagpp), ctx,"red");
                        text(widthBox+2*margin_x+10,margin_y+(MagMax-(i+1)/dMagpp)+4, MagMax+(i),ctx,"10px Arial",Math.Pi/2,"red")
                    }
                    text(widthBox+2*margin_x+40,(heightBox/2+margin_y)+20, "absolute Helligkeit",ctx,"20px Arial",-Math.PI/2,"red","center");
 */                }



            }

            function draw_main_sequence(){
                // Hauptreihe im HRD zeichnen
                var fittingPoints=[{P:0.1, m:1.46},{P:10, m:-4.1}];
                var lines = [];
                fittingPoints.forEach(function(key) {
                    //                     console.log(key);
                    y_hr=(MagMax-(key["m"]))/dMagpp+margin_y;//+110;
                    x_hr=(PulsMaxLog10-Math.log10(key["P"]))/dPulspp+margin_x+10
                    p = { x: x_hr, y: y_hr };
                    lines.push(p);
                })

                //draw smooth line
                ctx.beginPath();
                ctx.setLineDash([5]);
                ctx.lineWidth = 5;
                ctx.strokeStyle = "red";
                bzCurve(lines, 0.3, 1);
                ctx.setLineDash([0]);
                ctx.lineWidth = 1;

            }

            function draw_horizontal_line(absMag=5,diffMag=0){
                var calcMag=-MagMax+absMag
                //                 console.log(diffMag);
                var calcVisMag=Math.round((absMag+diffMag)*10)/10
                line(margin_x-5, margin_y+(MagMax-(calcMag+1)/dMagpp),widthBox+2*margin_x, margin_y+(MagMax-(calcMag+1)/dMagpp), ctx,"blue")
                draw_rectFill(margin_x-40,margin_y+(MagMax-(calcMag+1)/dMagpp)-10,margin_x-5,margin_y+(MagMax-(calcMag+1)/dMagpp)+10,ctx,2,0,"#fff",1)
                draw_rect(margin_x-40,margin_y+(MagMax-(calcMag+1)/dMagpp)-10,margin_x-5,margin_y+(MagMax-(calcMag+1)/dMagpp)+10,ctx,2,0,"blue")
                text(margin_x-22.5,margin_y+(MagMax-(calcMag+1)/dMagpp)+5,calcVisMag,ctx,"15px Arial",Math.Pi/2,"blue","center")
                draw_rectFill(widthBox+2*margin_x+40,margin_y+(MagMax-(calcMag+1)/dMagpp)-10,widthBox+2*margin_x+5,margin_y+(MagMax-(calcMag+1)/dMagpp)+10,ctx,2,0,"#fff",1)
                draw_rect(widthBox+2*margin_x+40,margin_y+(MagMax-(calcMag+1)/dMagpp)-10,widthBox+2*margin_x+5,margin_y+(MagMax-(calcMag+1)/dMagpp)+10,ctx,2,0,"red")
                text(widthBox+2*margin_x+22.5,margin_y+(MagMax-(calcMag+1)/dMagpp)+5,absMag,ctx,"15px Arial",Math.Pi/2,"red","center")
            }

            // Sterne des Clusters einzeichnen
            function setStars(id="", yShift=0){
//                 console.log("Start");
                //                 var imgSrc=starList[id]['img']
//                 console.log(starList)
//                 $("#previewImg").attr("src","data/"+starList[id]['img']);
                //                 console.log($("#clusterSelect").val());
                clearCanvas(ctx, canvas);
                drawHRD();
                // MagShift
        /*         var MagShift
                if (typeof starList[id]["MagShift"] !== 'undefined') {
                    MagShift=starList[id]["MagShift"]
                }else{
                    MagShift=0
                }
 */
                MagShift=0
                //                 console.log(id);
                $json_object=cepheiden;
//                 console.log($json_object)
                //                                  $json_object=starList["hyaden"]["starList"];
                //                 $json_object=starList["Plej"]["starList"];
                Object.keys($json_object).forEach(function(key) {
                    if((MagMax-$json_object[key]['AbsMag_g']-MagShift)/dMagpp+margin_y+yShift<heightBox+2*margin_y-10 && (MagMax-$json_object[key]['AbsMag_g']-MagShift)/dMagpp+margin_y+yShift>margin_y-7){

                        var x_Puls=(PulsMaxLog10-Math.log2($json_object[key]['pf']))/dPulspp+margin_x+10;
                        //                     point(x_Teff, (MagMax-$json_object[key]['VisMag'])/dMagpp+margin_y,2, ctx,"#000","#000");
                        //                         point(x_Teff, (MagMax-$json_object[key]['VisMag'])/dMagpp+margin_y+yShift,2, ctx,"#000","#000");
                        point(x_Puls, (MagMax-$json_object[key]['AbsMag_g']-MagShift)/dMagpp+margin_y+yShift,2, ctx,"#000","#000");
//                         console.log((MagMax-$json_object[key]['AbsMag_g']-MagShift)/dMagpp+margin_y+yShift);
                        //                     point((TeffMaxLog2-Math.log2($json_object[key]['Teff']))/dTeffpp+margin_x, (MagMax-$json_object[key]['VisMag'])/dMagpp+margin_y,2, ctx,"#000","#000");
                    }
                })
                // y-Achse - scheinbare Helligkeit
                for (i = 0; i <= Math.abs(MagMax)+Math.abs(MagMin+50); i++) { 
                    if(margin_y+(MagMax-(i+1)/dMagpp)+yShift<heightBox+2*margin_y && margin_y+(MagMax-(i+1)/dMagpp)+yShift>margin_y){
                        line(margin_x-5, margin_y+(MagMax-(i+1)/dMagpp)+yShift,margin_x, margin_y+(MagMax-(i+1)/dMagpp)+yShift, ctx,"#000");
                        text(margin_x-20,margin_y+(MagMax-(i+1)/dMagpp)+4+yShift, MagMax+(i),ctx,"10px Arial",Math.Pi/2,"#000")
                    }
                }

/*                 if($("#vglLinie").is(":checked")){
                    draw_horizontal_line(-3,yShift*dMagpp)
                 }*/
            }

//             setStars($("#clusterSelect").val());
            setStars();

            // ================ Verschiebung durch Maus ermitteln

            const mouseReference = {
                buttonDown: false,
                x: false,
                y: false,
                xbound: false,
                ybound: false
            }

            $('#hdr').on('mousedown mouseup touchstart', function (e) {
                mouseReference.buttonDown = !mouseReference.buttonDown
                var rect = canvas.getBoundingClientRect();
                mouseReference.xbound = rect.left
                mouseReference.ybound = rect.top
                if(e.clientX!==undefined){
                    mouseReference.x = e.clientX - rect.left
                    mouseReference.y = e.clientY - rect.top
                }else{
                    mouseReference.x = e.touches[0].clientX - rect.left
                    mouseReference.y = e.touches[0].clientY - rect.top
                }

            }).on('mousemove touchmove', function (e) {
                // 				console.log(e.touches);
                // 				console.log(typeof e.touches === 'object')
                if ((e.which === 1 &&  mouseReference.buttonDown) || typeof e.touches === 'object' && typeof e.touches[0] === 'object' ) {
//                     console.log(e);
                    // The location (x, y) the mouse  was originally pressed
                    //                     console.log('refernce x: ', mouseReference.x)
                    //                     console.log('refernce y: ', mouseReference.y)

                    // The new location of the mouse while being moved
                    //                     console.log('new x: ', e.pageX-mouseReference.xbound)
                    //                     console.log('new y: ', e.pageY-mouseReference.ybound)
                    if(e.pageY!==undefined){
                        var diff=(e.pageY-mouseReference.ybound)-mouseReference.y
                        }else{
                            var diff=(e.touches[0].pageY-mouseReference.ybound)-mouseReference.y

                            }
                    //                     console.log("Diff = " + diff)
                    // Calculate distance here using the e.pageX / e.pageY and reference location

//                     console.log(diff);
                    setStars($("#clusterSelect").val(), diff) // HRD neu zeichnen

                }
            }).on('touchend', function (e) {
                mouseReference.buttonDown = !mouseReference.buttonDown
                // console.log(e);
            })

            // ================ Verschiebung durch Maus ermitteln - ENDE

            function clearCanvas(context, canvas) {
                context.clearRect(0, 0, canvas.width, canvas.height);
                var w = canvas.width;
                canvas.width = 1;
                canvas.width = w;
            }

            $( "#clusterSelect" ).change(function() {
                setStars($("#clusterSelect").val());
            });
            $( ".options" ).change(function() {
                setStars($("#clusterSelect").val());
            });

        </script>

    </body>
</html>
