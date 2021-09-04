<html>

<head>
    <meta charset="utf-8" />
    <title>Supernovae - Spektren zum Zeitpunkt der maximalen Helligkeit</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/switches.css">
    <link rel="stylesheet" href="css/range.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <!--        <script src="data/0005_Cepheid_light_curve.js"></script>//-->
    <script src="data/List_of_supernovae.js"></script>
    <script src="data/spectrallines.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/mpe_draw.js"></script>
    <script src="js/mpe_methods.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <script src="js/cache.js"></script>

</head>

<body style=" max-width:1280px; margin: 0 auto;">
    <div style="margin:0; padding:5px; border: solid 1px gray">
        <div class="row" style="margin:0; padding:0">
            <div id="divCan" class="col-sm-12">
                <center>
                    <img id="loader" style="left:45%; top: 40%; position:absolute; width:60px" src="images/loading-buffering.gif">
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
                <ul class="list-group" id="uloptions"></ul>
            </div>
        </div>
        <div id="referencesdiv" class="row" style="display:none;">
            <div class="col-sm-12" id="">
                <h4>Quellen</h4>
            </div>
            <div class="col-sm-6" id="refcol1"></div>
            <div class="col-sm-6" id="refcol2"></div>
            <div class="col-sm-12" id="refdata"></div>
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
        $.each(SuperNovaeData, function(SupernovaName, SupernovaData) {
            var o = new Option("option text", SupernovaName);
            $(o).html(SupernovaName);
            $("#SNSelect").append(o);
        });
        var Spektren;
        var subtitle;
        var xdiff = 0;
        var mjd = 0;
        var localCache = {
            data: {}
        };

        var references = {};
        var urls = {};
        urls.urlspectra =

            $(document).ready(function() {

                function drawImage() {
                    $("#loader").css("left", Math.round(canvasWidth / 2 - 30));
                    $("#loader").css("top", Math.round(canvasHeight / 2 - 30));
                    $("#loader").show();

                    var SupernovaName = $("#SNSelect").val()

                    urls.urlspectra = "https://api.astrocats.space/" + SupernovaName + "/spectra";
                    urls.urlredshift = "https://api.astrocats.space/" + SupernovaName + "/redshift";
                    urls.urldata = "https://api.astrocats.space/" + SupernovaName + "/redshift+maxabsmag+maxappmag+maxvisualabsmag+maxband+lumdist+comovingdist+kcorrected+scorrected+mcorrected+bandset+error+source+dec+ra+claimedtype";
                    urls.urlmaxlightdate = "https://api.astrocats.space/" + SupernovaName + "/maxdate";
                    urls.urlreferences = "https://api.sne.space/" + SupernovaName + "/sources/reference"

                    $.ajax({
                        url: urls.urldata,
                        dataType: "json",
                        beforeSend: function() {
                            if (get_localcache(urls.urldata) !== null) {
                                data = get_localcache(urls.urldata);
                                mjd = data.mjd;
                                redshift = data.redshift;
                                return false;
                            }
                            return true;
                        },
                        success: function(data) {
                            var dateparts = SuperNovaeData[SupernovaName].maxlightdate.split("/");
                            var maxlightdate = new Date(dateparts[0], dateparts[1] - 1, dateparts[2]);
                            mjd = maxlightdate.getMJD();
                            data.mjd = mjd;
                            redshift = calculate_average_redshift(data[SupernovaName].redshift);
                            data.redshift = redshift;
                            set_localcache(urls.urldata, data);
                        },
                    });

                    $.ajax({
                        url: urls.urlspectra,
                        dataType: "json",
                        cache: true,
                        beforeSend: function() {
                            if (get_localcache(urls.urlspectra) !== null) {
                                setUp(get_localcache(urls.urlspectra));
                                return false;
                            }
                            return true;
                        },
                        success: function(data) {
                            var spectra = filter_spectra(data[SupernovaName].spectra);
                            set_localcache(urls.urlspectra, spectra);
                            setUp(spectra);
                        },
                    });

                    $.ajax({
                        url: urls.urlreferences,
                        dataType: "json",
                        beforeSend: function() {
                            if (get_localcache(urls.urlreferences) !== null) {
                                references = get_localcache(urls.urlreferences);
                                prepare_references(references, urls);
                                return false;
                            }
                            return true;
                        },
                        async: false,
                        success: function(data) {
                            references = data[SupernovaName].sources;
                            set_localcache(urls.urlreferences, references);
                            prepare_references(references, urls);
                        },
                    });
                }

                function filter_spectra(spektra) {
                    if (JSON.stringify(spektra).length / (1024 * 1024) < 5) {
                        return spektra
                    }
                    var maxwavelength = 15000
                    var minwavelength = 2000
                    while (JSON.stringify(spektra).length / (1024 * 1024) >= 5) {
                        var spektrafiltered_temp = [];
                        $.each(spektra, function(i, spectrum) {
                            var first = spectrum.data.shift();
                            var last = spectrum.data.pop();
                            if (first[0] > minwavelength && last[0] < maxwavelength) {
                                spektrafiltered_temp.push(spectrum);
                            }
                        });
                        spektra = spektrafiltered_temp;
                        maxwavelength = 0.95 * maxwavelength;
                    }
                    return spektra
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

                function drawBox(snName) {

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

                    // Zum verschieben des Spektrums
                    clearCanvas(ctx, canvas);
                    drawBox(snName);

                    // Hilfslinien für Spektrallinien
                    $.each(spectrallines, function(id, group) {
                        if ($("#" + id).is(":checked")) {
                            $.each(group, function(index, value) {
                                var lineDash = [];
                                if (value.type == 'absorption') {
                                    lineDash = [10, 10];
                                } else {
                                    lineDash = [];
                                }
                                draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp + parseFloat($("#vl_" + id).val()), value, value.color, lineDash);
                                draw_vertical_line(margin_x + (value.lambda - lambdaMin) / dLambdapp, value, value.color, lineDash, 20);
                                draw_textbox(margin_x + (value.lambda - lambdaMin) / dLambdapp + parseFloat($("#vl_" + id).val()), value, value.color, lineDash, 20);
                                if (parseFloat($("#vl_" + id).val()) != 0) {
                                    $("#dl_" + id).html("Δλ = " + (Math.round(parseFloat($("#vl_" + id).val()) * dLambdapp * 10) / 10) + "Å");
                                } else {
                                    $("#dl_" + id).html("Δλ = 0");
                                }
                            })
                        }
                    });

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
                    return lambda - dlambda;
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
                            text(xPos, margin_y - 10, value.title, ctx, "15px Arial", Math.Pi / 2, color, "center");
                        }
                    }
                }

                function draw_textbox(xPos, value, color, lineDash = [], length = 0) {
                    if (xPos > margin_x && xPos < widthBox) {
                        if (value.textshift) {
                            shift = value.textshift;
                        } else {
                            shift = 0;
                        }
                        draw_rectFill(xPos - 10 + shift, margin_y + 10, xPos + 10 + shift, margin_y + 90, ctx, 2, 0, "#fff", 1)
                        text(xPos + 8 + shift, margin_y + 50, value.lambda + "Å", ctx, "18px Arial", -Math.PI / 2, color, "center");
                    }
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

                // Erstellen der li Elemente in den Optionen. Je einen Switch pro Spektrallinien-Gruppe.
                $.each(spectrallines, function(id, row) {
                    var linecolor = 'gray';
                    if (row[1].color.length > 0) {
                        linecolor = row[1].color;
                    }
                    $("#uloptions").append('<li class="list-group-item">' +
                        row[1].grouptitle +
                        '<div class="material-switch pull-right">' +
                        '<input id="' + id + '" type="checkbox" class="options_vl_cb" />' +
                        '<label for="' + id + '" class="label-success" style="margin-top: 10px;"></label>' +
                        '</div>' +
                        '<div class="col-xs-5 material-switch pull-right vl_diff_' + id + ' hidden">' +
                        '<input id="vl_' + id + '" type="range" class="RangeStyle1 ' + linecolor + ' options_vl RangeClsHor" ' +
                        'min="-400" max="200" step="0.01" value = 0>' +
                        '</div>' +
                        '<div id="dl_' + id + '"  class="col-xs-2 material-switch pull-right vl_diff_' + id + ' hidden">Δλ = 0</div>' +
                        '</li>');
                });

                // Hinzufügen der "Qellen anzeigen" Option.
                $("#uloptions").append('<li class="list-group-item">' +
                    'Quellen anzeigen' +
                    '<div class="material-switch pull-right">' +
                    '<input id="references" type="checkbox" class="options_references" value=1 />' +
                    '<label for="references" class="label-success" style="margin-top: 10px;"></label>' +
                    '</div>' +
                    '</li>');

                // Eventhandlers.
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
                $(".options_vl").on('change', function() {
                    drawImage();
                });
                $(".options_vl").on('input', function() {
                    drawImage();
                });
                $(".options_vl_cb").on('change', function() {
                    $('#vl').toggleClass('hidden');
                    $('.vl_diff_' + this.id).toggleClass('hidden');
                    drawImage();
                });
                $('#references').click(function() {
                    $("#referencesdiv").toggle(this.checked);
                });
                $("#SpektrumSelect").on('change', function() {
                    drawImage();
                });
                $("#ResetOffset").on('click', function() {
                    $.each(spectrallines, function(id, group) {
                        $("#dl_" + id).html('');
                        $("#vl_" + id).val(0);
                    });
                    drawImage();
                });

                drawImage();

            });
    </script>
</body>

</html>