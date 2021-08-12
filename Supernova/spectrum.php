<html>

<head>
    <meta charset="utf-8" />
    <title>Supernovae - Spektren zum Zeitpunkt der maximalen Helligkeit</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/switches.css">
    <link rel="stylesheet" href="css/range.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <!--        <script src="data/0005_Cepheid_light_curve.js"></script>//-->
    <script src="data/List_of_light_curves.js"></script>
    <script src="data/spectrallines.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/mpe_draw.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <script src="js/cache.js"></script>

</head>

<body style=" max-width:1280px; margin: 0 auto;">
    <div class="row" style="margin:0; padding:5px; border: solid 1px gray">
        <div id="divCan" class="col-sm-12">
            <center>
                <img id="loader" style="position:absolute; width:60px" src="images/loading-buffering.gif">
                <canvas id="sncanvas" style="margin:0; background:light:blue; border: solid 1px black;"></canvas>
            </center>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-12">
                <button id="ResetOffset" type="button" class="btn btn-outline-secondary col-sm-2 col-sm-push-10">Reset Offset</button>
            </div>
            <h3>Supernova auswählen: </h3>
            <select class="form-control" id="SNSelect">
            </select>
            <h3>Spektrum auswählen: </h3>
            <select class="form-control" id="SpektrumSelect">
            </select>
            <hr>
            <h3>Optionen</h3>
            <ul class="list-group">
                <li class="list-group-item">
                    Wasserstofflinien
                    <div class="material-switch pull-right">
                        <input id="hydrogen" type="checkbox" class="options_vl_cb" />
                        <label for="hydrogen" class="label-success" style="margin-top: 10px;"></label>
                    </div>
                </li>
                <li class="list-group-item">
                    Siliziumlinien
                    <div class="material-switch pull-right">
                        <input id="silicon" type="checkbox" class="options_vl_cb" />
                        <label for="silicon" class="label-success" style="margin-top: 10px;"></label>
                    </div>
                </li>
                <li class="list-group-item">
                    Heliumlinien
                    <div class="material-switch pull-right">
                        <input id="helium" type="checkbox" class="options_vl_cb" />
                        <label for="helium" class="label-success" style="margin-top: 10px;"></label>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <script>
        clear_localcache();
        var canvas = document.getElementById("sncanvas");
        canvasWidth = $("#divCan").width()
        canvas.width = canvasWidth
        canvasHeight = canvasWidth * 0.5;
        canvas.height = canvasHeight;
        var margin_y = 70;
        var margin_y_bottom = 60;
        var margin_x = 60;
        var widthBox = canvasWidth - 2 * margin_x;
        var heightBox = canvasHeight - margin_y - margin_y_bottom;
        var ctx = canvas.getContext("2d");
        var redshift = 0;
        var arrayLength = SuperNovae.length;
        for (var i = 0; i < arrayLength; i++) {
            var j = i + 1;
            var o = new Option("option text", SuperNovae[i]);
            $(o).html(SuperNovae[i]);
            $("#SNSelect").append(o);
        }
        var Spektren;
        var subtitle;
        var xdiff = 0;
        var mjd = 0;
        var localCache = {
            data: {}
        };

        $(document).ready(function() {

            function drawImage() {
                $("#loader").css("left", canvasWidth / 2 - 30);
                $("#loader").css("top", canvasHeight / 2 - 30);
                $("#loader").show();

                var SupernovaName = $("#SNSelect").val()

                var urlspectra = "https://api.astrocats.space/" + SupernovaName + "/spectra";
                var urlredshift = "https://api.astrocats.space/" + SupernovaName + "/redshift";
                var urlmaxlightdate = "https://api.astrocats.space/" + SupernovaName + "/maxdate";

                $.ajax({
                    url: urlmaxlightdate,
                    dataType: "json",
                    beforeSend: function() {
                        if (get_localcache(urlmaxlightdate) !== null) {
                            mjd = get_localcache(urlmaxlightdate);
                            // console.log("MJD from Cache: " + mjd);
                            return false;
                        }
                        return true;
                    },
                    success: function(data) {
                        console.log("MJD from URL");
                        var dateparts = data[SupernovaName].maxdate.shift().value.split("/");
                        var maxlightdate = new Date(dateparts[0], dateparts[1] - 1, dateparts[2]);
                        mjd = maxlightdate.getMJD();
                        set_localcache(urlmaxlightdate, mjd);
                    },
                });

                $.ajax({
                    url: urlredshift,
                    dataType: "json",
                    beforeSend: function() {
                        if (get_localcache(urlredshift) !== null) {
                            redshift = get_localcache(urlredshift);
                            // console.log("Redshift from Cache: " + redshift);
                            return false;
                        }
                        return true;
                    },
                    success: function(data) {
                        console.log("Redshift from URL");
                        redshift = calculate_average_redshift(data[SupernovaName].redshift);
                        set_localcache(urlredshift, redshift);
                    },
                });

                $.ajax({
                    url: urlspectra,
                    dataType: "json",
                    cache: true,
                    beforeSend: function() {
                        if (get_localcache(urlspectra) !== null) {
                            setUp(get_localcache(urlspectra));
                            console.log("Spectrum data from Cache");
                            return false;
                        }
                        return true;
                    },
                    success: function(data) {
                        // console.log("Spectrum data from URL");
                        var spectra = filter_spectra(data[SupernovaName].spectra);
                        set_localcache(urlspectra, spectra);
                        setUp(spectra);
                    },
                });

                // ================ Verschiebung durch Maus ermitteln ==== START
                const mouseReference = {
                    buttonDown: false,
                    x: false,
                    y: false,
                    xbound: false,
                    ybound: false
                }

                $('#sncanvas').on('mousedown mouseup touchstart', function(e) {
                    mouseReference.buttonDown = !mouseReference.buttonDown
                    var rect = canvas.getBoundingClientRect();
                    mouseReference.xbound = rect.left
                    mouseReference.ybound = rect.top
                    if (e.clientX !== undefined) {
                        mouseReference.x = e.clientX - rect.left
                        mouseReference.y = e.clientY - rect.top
                    } else {
                        mouseReference.x = e.touches[0].clientX - rect.left
                        mouseReference.y = e.touches[0].clientY - rect.top
                    }

                }).on('mousemove touchmove', function(e) {
                    if ((e.which === 1 && mouseReference.buttonDown) || typeof e.touches === 'object' && typeof e.touches[0] === 'object') {
                        if (e.pageX !== undefined) {
                            xdiff = (e.pageX - mouseReference.xbound) - mouseReference.x
                        } else {
                            xdiff = (e.touches[0].pageX - mouseReference.xbound) - mouseReference.x
                        }
                        // console.log("Diff = " + xdiff)
                        drawImage(xdiff);

                    }
                }).on('touchend', function(e) {
                    mouseReference.buttonDown = !mouseReference.buttonDown
                })
                // ================ Verschiebung durch Maus ermitteln ==== ENDE

            }

            function filter_spectra(spektra) {
                const spektrafiltered = [];
                $.each(spektra, function(i, spectrum) {
                    if (spectrum.u_fluxes != "Uncalibrated" && (!spectrum.hasOwnProperty("observer") || spectrum.observer != "Unknown")) {
                        spektrafiltered.push(spectrum);
                    }
                });
                return spektrafiltered;

            }

            function setUp(json) {
                var maxlightdistance = 1000000;
                var maxlightindex = 0;

                $.each(json, function(i, spectrum) {
                    if (spectrum.time - mjd > 0 && spectrum.time - mjd <= maxlightdistance) {
                        maxlightdistance = spectrum.time - mjd;
                        maxlightindex = i;
                    }

                    // Set Spektrum as an Option in a Select Box.
                    if ($("#SpektrumSelect option[value='" + i + "']").length == 0) {
                        var o = new Option("option text", (i));
                        var plussign = "";
                        if (spectrum.time - mjd > 0) {
                            plussign = "+";
                        }
                        $(o).html("Tag der maximalen Helligkeit " + plussign + roundUp(spectrum.time - mjd, 2) + "d");
                        $("#SpektrumSelect").append(o);
                    }
                });

                var spectraData = json[$("#SpektrumSelect").val()].data;
                if (json[$("#SpektrumSelect").val()].redshift) {
                    redshift = json[$("#SpektrumSelect").val()].redshift;
                }
                subtitle = $("#SpektrumSelect option:selected").text();
                var lambdaMin = 100000;
                var lambdaMax = 0;
                var fluxMin = 100000;
                var fluxMax = 0;

                $.each(spectraData, function(index, row) {
                    [lambda, flux] = row;
                    flux = convertFloatNumber(flux);
                    lambda = deredshift_wavelength(lambda, redshift)

                    if (lambda < lambdaMin) {
                        lambdaMin = lambda;
                    }
                    if (lambda > lambdaMax) {
                        lambdaMax = lambda;
                    }
                    if (flux < fluxMin) {
                        fluxMin = flux;
                    }
                    if (flux > fluxMax) {
                        fluxMax = flux;
                    }
                });
                fluxMin = fluxMin * 0.9;
                fluxMax = fluxMax * 1.1;
                fluxScale = getExponentOfFloat(fluxMin);
                setPoints($("#SNSelect").val(), spectraData, lambdaMin, lambdaMax, fluxMin, fluxMax, fluxScale);
                $("#loader").hide();
            }

            function drawBox(snName, lambdaMin, lambdaMax, fluxScale) {

                // Box zeichnen
                var ctx = canvas.getContext("2d");
                ctx.strokeStyle = "#000";
                ctx.lineWidth = 2;
                ctx.strokeRect(margin_x, margin_y, widthBox, heightBox);
                ctx.stroke();

                // Beschriftung y-Achse
                text(margin_x / 2 + 5, (heightBox / 2 + margin_y), "Fluss in erg/s/cm²/Å", ctx, "20px Arial", -Math.PI / 2, "#000", "center");

                // Beschriftung x-Achse
                text(widthBox / 2 + margin_x, heightBox + margin_y + 45, "λ in Å", ctx, "20px Arial", Math.PI * 2, "#000", "center");

                // Diagramm Titel
                text(canvasWidth / 2, margin_y / 2 - 8, "Spektrum von " + snName, ctx, "25px Arial", 0, "black", "center")
                text(canvasWidth / 2, margin_y / 2 + 10, subtitle, ctx, "16px Arial", 0, "black", "center")

                text(canvasWidth - 5, canvasHeight - 10, String.fromCharCode(169) + "  OStR Peter Mayer, 2017-2021", ctx, "10px Arial", 0, "black", "end")

            }

            function setPoints(snName, spectraData, lambdaMin, lambdaMax, fluxMin, fluxMax) {
                var dFlux = fluxMax - fluxMin;

                var dFluxpp = dFlux / heightBox; // Flux pro Pixel
                var fluxMaxAxis = fluxMax;
                var fluxMinAxis = fluxMin;
                var dfluxAxis = (fluxMaxAxis - fluxMinAxis) / 1

                var dLambda = lambdaMax - lambdaMin;
                var dLambdapp = dLambda / widthBox; // Flux pro Pixel

                var LambdaMaxAxis = Math.floor(lambdaMax)
                var LambdaMinAxis = roundUp(lambdaMin, 0)
                var dLambdaAxis = (LambdaMaxAxis - LambdaMinAxis) / 10

                $('.RangeClsHor').attr('min', fluxMaxAxis);
                $('.RangeClsHor').attr('max', fluxMinAxis);
                $('.RangeClsHor').attr('value', (fluxMin + fluxMax) / 2);

                $('.RangeClsVert').attr('min', lambdaMin);
                $('.RangeClsVert').attr('max', lambdaMax);
                $('.RangeClsVert').attr('value', (fluxMin + fluxMax) / 2);


                // Zum verschieben des Spektrums
                clearCanvas(ctx, canvas);
                drawBox(snName, lambdaMin, lambdaMax, fluxScale);

                var lineDash = [];
                if ($("#hydrogen").is(":checked")) {
                    $.each(spectrallines.hydrogen, function(index, value) {
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp + xdiff, value.title, "red", lineDash);
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp, value.title, "red", lineDash, 20);
                    })
                }
                if ($("#silicon").is(":checked")) {
                    $.each(spectrallines.silicon, function(index, value) {
                        if (value.type == 'absorption') {
                            lineDash = [10, 10];
                        }
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp + xdiff, value.title, "green", lineDash);
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp, value.title, "green", lineDash, 20);
                        lineDash = [];
                    })
                }
                if ($("#helium").is(":checked")) {
                    $.each(spectrallines.helium, function(index, value) {
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp + xdiff, value.title, "purple", lineDash);
                        draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp, value.title, "purple", lineDash, 20);
                    })
                }

                if (xdiff != 0) {
                    text(margin_x + widthBox - 52, margin_y + 20, "Δλ = " + (Math.round(xdiff * dLambdapp * 10) / 10) + "Å", ctx, "15px Arial", Math.Pi / 2, 'blue', "center")
                    // draw_rectFill(margin_x + widthBox - 75, margin_y - 5, margin_x + widthBox + 25, margin_y - 25, ctx, 2, 0, "#fff", 1)
                    draw_rect(margin_x + widthBox - 100, margin_y + 3, margin_x + widthBox - 4, margin_y + 25, ctx, 2, 0, "blue")
                }

                // Draw Spektrum
                drawLine(spectraData, lambdaMin, dLambdapp, fluxMin, dFluxpp, "blue", 1);

                // x-Achse Raster
                var tick = LambdaMinAxis;
                while (tick <= LambdaMaxAxis) {
                    xPos = margin_x + (tick - lambdaMin) / dLambdapp;
                    line(xPos, heightBox + margin_y, xPos, heightBox + margin_y + 10, ctx, "black");
                    text(xPos, heightBox + margin_y + 20, roundUp(tick, 0), ctx, "10px Arial", 0, "black", "center")

                    if (xdiff != 0) {
                        if (xPos + xdiff > margin_x && xPos + xdiff < widthBox + margin_x) {
                            line(xPos + xdiff, heightBox + margin_y, xPos + xdiff, heightBox + margin_y + 13, ctx, "blue");
                            text(xPos + xdiff, heightBox + margin_y + 30, roundUp(tick, 0), ctx, "10px Arial", 0, "blue", "center")
                        }
                    }
                    tick = tick + dLambdaAxis;
                }

                // y-Achse Raster
                var tick = fluxMinAxis;
                while (tick <= fluxMaxAxis) {
                    yPos = heightBox + margin_y - ((tick - fluxMin) / dFluxpp);
                    line(margin_x, yPos, margin_x - 5, yPos, ctx, "black");
                    text(margin_x - 8, yPos + 4, toFixedScientific(tick, 2), ctx, "10px Arial", 0, "black", "right")
                    tick = tick + dfluxAxis;
                }

            }

            Date.prototype.getMJD = function() {
                return this.getJulian() - 2400000.5;
            }

            Date.prototype.getJulian = function() {
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

            function calculate_average_redshift(array) {
                var sum = 0;
                for (var i = 0; i < array.length; i++) {
                    sum += parseFloat(array[i].value, 10); //don't forget to add the base
                }
                var avg_redshift = sum / array.length;
                return avg_redshift;
            }

            function deredshift_wavelength(lambda, redshift) {
                var dlambda = redshift * lambda;
                // console.log(lambda + " => " + (lambda - dlambda));
                return lambda - dlambda;
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

            function draw_horizontal_line(y_line, DisplVal) {
                line(margin_x, y_line, widthBox + margin_x + 5, y_line, ctx, "blue")
                draw_rectFill(widthBox + margin_x + 5, y_line - 10, widthBox + margin_x + 55, y_line + 10, ctx, 2, 0, "#fff", 1)
                draw_rect(widthBox + margin_x + 5, y_line - 10, widthBox + margin_x + 55, y_line + 10, ctx, 2, 0, "blue")
                text(widthBox + margin_x + 30, y_line + 5, DisplVal, ctx, "15px Arial", Math.Pi / 2, "blue", "center")
            }

            function draw_vertical_line(xPos, value, color, lineDash = [], length = 0) {
                if (xPos > margin_x && xPos < widthBox) {
                    var bottomup = margin_y;
                    if (length) {
                        bottomup = heightBox + margin_y - length;
                    }
                    line(xPos, heightBox + margin_y, xPos, bottomup, ctx, color, lineDash);
                    if (!length) {
                        text(xPos, margin_y - 10, value, ctx, "15px Arial", Math.Pi / 2, color, "center");
                    }
                }
                // draw_rectFill(t_Puls_Pos - 25, margin_y - 5, t_Puls_Pos + 25, margin_y - 25, ctx, 2, 0, "#fff", 1)
                // draw_rect(t_Puls_Pos - 25, margin_y - 5, t_Puls_Pos + 25, margin_y - 25, ctx, 2, 0, "red")
            }

            function convertFloatNumber(float) {
                return Math.abs(parseFloat(float));
                // eIndex = float.indexOf("e");
                // floatnumber = Math.abs(parseFloat(float.substr(0, eIndex - 1)));
                // floatexponent = parseInt(float.substr(eIndex + 1, float.length));
                // return floatnumber * 10 ^ floatexponent;
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

            function drawLine(data, LambdaMin, dLambdaPP, fluxMin, lFluxPP, color, width) {

                var ctx = this.ctx;
                ctx.save();
                // this.transformContext();
                ctx.lineWidth = width;
                ctx.strokeStyle = color;
                ctx.fillStyle = color;
                ctx.beginPath();
                var scale = 1;

                [lambda, flux] = data.shift();
                flux = convertFloatNumber(flux) * scale;
                lambda = deredshift_wavelength(lambda, redshift)
                ctx.moveTo(margin_x + (lambda - LambdaMin) / dLambdaPP, heightBox + margin_y - (flux - fluxMin) / lFluxPP);

                $.each(data, function(index, row) {
                    [lambda, flux] = row;
                    flux = convertFloatNumber(flux) * scale;
                    lambda = deredshift_wavelength(lambda, redshift)

                    // draw segment  
                    ctx.lineTo(margin_x + (lambda - LambdaMin) / dLambdaPP, heightBox + margin_y - (flux - fluxMin) / lFluxPP);
                    ctx.stroke();
                    ctx.closePath();
                    ctx.beginPath();
                    ctx.arc(margin_x + (lambda - LambdaMin) / dLambdaPP, heightBox + margin_y - (flux - fluxMin) / lFluxPP, this.pointRadius, 0, 2 * Math.PI, false);
                    ctx.fill();
                    ctx.closePath();

                    // position for next segment  
                    ctx.beginPath();
                    ctx.moveTo(margin_x + (lambda - LambdaMin) / dLambdaPP, heightBox + margin_y - (flux - fluxMin) / lFluxPP);
                });
                ctx.restore();
            };

            $("#SNSelect").on('change', function() {
                // Remove all outdated options from selectbox.
                $('#SpektrumSelect').children().remove().end();
                clear_localcache()
                drawImage();
            });
            $(".options_hl").on('mousemove', function() {
                drawImage();
            });
            $(".options_hl_cb").on('change', function() {
                $('#hl').toggleClass('hidden');
                drawImage();
            });
            $(".options_vl").on('mousemove', function() {
                drawImage();
            });
            $(".options_vl_cb").on('change', function() {
                $('#vl').toggleClass('hidden');
                drawImage();
            });
            $("#SpektrumSelect").on('change', function() {
                drawImage();
            });
            $("#ResetOffset").on('click', function() {
                xdiff = 0;
                drawImage();
            });
            drawImage();

        });
    </script>
</body>

</html>