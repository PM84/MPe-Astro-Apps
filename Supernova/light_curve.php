<html>

<head>
    <meta charset="utf-8" />
    <title>Supernova - Lichtkurve</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/switches.css">
    <link rel="stylesheet" href="css/range.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <!--        <script src="data/0005_Cepheid_light_curve.js"></script>//-->
    <script src="data/List_of_supernovae.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/mpe_draw.js"></script>
    <script src="js/mpe_methods.js"></script>
    <script src="js/cache.js"></script>

</head>

<body style=" max-width:1280px; margin: 0 auto;">
    <div style="margin:0; padding:5px; border: solid 1px gray">
        <div class="row" style="margin:0; padding:0;">
            <div id="divCan" class="col-sm-8">
                <center>
                    <img id="loader" style="left:45%; top: 40%; position:absolute; width:60px" src="images/loading-buffering.gif">
                    <canvas id="sncanvas" style="margin:0; background:light:blue; border: solid 1px black;"></canvas>
                </center>
            </div>
            <div class="col-sm-4">
                <h3>Supernova auswählen: </h3>
                <select class="form-control" id="SNSelect">
                </select>
                <h3>Beobachtungsband auswählen: </h3>
                <select class="form-control" id="BandSelect">
                </select>
                <hr>
                <h3>Optionen</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        Hilfslinien zum Vergleich der scheinbaren Helligkeit und der absoluten Helligkeit
                        <div class="material-switch pull-right">
                            <input id="vglLinie" type="checkbox" class="options_hl_cb" />
                            <label for="vglLinie" class="label-success" style="margin-top: 10px;"></label>
                        </div>
                        <div id="hl" class="hidden">
                            <hr>
                            <div>
                                Lichtkurve vertikal verschieben
                                <input id="hl_1" type="range" class="RangeStyle1 Blue options_hl RangeClsHor" min="0" max="10" step="0.001">
                            </div>
                            <div>
                                Lichtkurve horizontal verschieben
                                <input id="lc_1" type="range" class="RangeStyle1 Blue options_hl RangeClsHor" min="-100" max="100" step="0.001">
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Zeitpunkt der maximalen Helligkeit markieren
                        <div class="material-switch pull-right">
                            <input id="maxLight" type="checkbox" class="options_maxLight" />
                            <label for="maxLight" class="label-success" style="margin-top: 10px;"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Quellen anzeigen
                        <div class="material-switch pull-right">
                            <input id="references" type="checkbox" class="options_references" value=1 />
                            <label for="references" class="label-success" style="margin-top: 10px;"></label>
                        </div>
                    </li>
                </ul>
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
        var canvas = document.getElementById("sncanvas");
        canvasWidth = $("#divCan").width()
        canvas.width = canvasWidth
        canvasHeight = canvasWidth * 0.9;
        canvas.height = canvasHeight;
        var margin_y = 70;
        var margin_y_bottom = 60;
        var margin_x = 60;
        var widthBox = canvasWidth - 2 * margin_x;
        var heightBox = canvasHeight - margin_y - margin_y_bottom;
        var ctx = canvas.getContext("2d");

        $.each(SuperNovaeData, function(SupernovaName, SupernovaData) {
            var o = new Option("option text", SupernovaName);
            $(o).html(SupernovaName);
            $("#SNSelect").append(o);
        });

        var lightcurvedata = {};
        var objectdata = {};
        var min_max_mag = {};
        var min_max_time = {};
        var SupernovaName = $("#SNSelect").val();
        var mjd;
        var xDiff;
        var urllightcurve = "https://api.astrocats.space/" + SupernovaName + "/photometry/time+magnitude+band+e_magnitude";
        if (get_localcache(urllightcurve) !== null) {
            lightcurvedata = get_localcache(urllightcurve)
            $.each(lightcurvedata, function(band, data) {
                var o = new Option("option text", band);
                $(o).html(band);
                $("#BandSelect").append(o);
            });
        }
        var references = {};
        var urls = {}

        $(document).ready(function() {

            function drawImage() {
                $("#loader").css("left", Math.round(canvasWidth / 2 - 30));
                $("#loader").css("top", Math.round(canvasHeight / 2 - 30));
                $("#loader").show();
                setUp(lightcurvedata[$("#BandSelect").val()]);
                drawBox(SupernovaName);
                setPoints(lightcurvedata[$("#BandSelect").val()]);
            }

            function drawBox(snName) {
                // Box zeichnen
                var ctx = canvas.getContext("2d");
                ctx.strokeStyle = "#000";
                ctx.lineWidth = 2;
                ctx.strokeRect(margin_x, margin_y, widthBox, heightBox);
                ctx.stroke();

                // Beschriftung y-Achse
                text(margin_x / 2 - 5, (heightBox / 2 + margin_y), "scheinbare Helligkeit", ctx, "20px Arial", -Math.PI / 2, "#000", "center");

                if ($("#BandSelect").val() == "V" && $("#vglLinie").is(":checked")) {
                    text(widthBox + 3 * margin_x / 2 + 20, (heightBox / 2 + margin_y), "absolute Helligkeit", ctx, "20px Arial", -Math.PI / 2, "red", "center");
                }

                // Beschriftung x-Achse
                text(widthBox / 2 + margin_x, heightBox + margin_y + 45, "Zeit in d", ctx, "20px Arial", Math.PI * 2, "#000", "center");

                // Diagramm Titel
                text(canvasWidth / 2, margin_y / 2 - 8, "Photometrie von " + snName, ctx, "25px Arial", 0, "black", "center")
                text(canvasWidth / 2, margin_y / 2 + 10, $("#BandSelect").val() + "-Band", ctx, "16px Arial", 0, "black", "center")

                text(canvasWidth - 5, canvasHeight - 10, String.fromCharCode(169) + "  OStR Peter Mayer, 2017-2021", ctx, "10px Arial", 0, "black", "end")

            }

            function get_lightcurvedata() {
                SupernovaName = $("#SNSelect").val()

                urls.urldata = "https://api.astrocats.space/" + SupernovaName + "/maxdate+maxabsmag+maxappmag+maxvisualabsmag+maxband+lumdist+comovingdist+kcorrected+scorrected+mcorrected+bandset+error+source+dec+ra+claimedtype";
                $.ajax({
                    url: urls.urldata,
                    dataType: "json",
                    cache: true,
                    async: false,
                    beforeSend: function() {
                        if (get_localcache(urls.urldata) !== null) {
                            objectdata = get_localcache(urls.urldata)
                            mjd = objectdata.mjd;
                            return false;
                        }
                        return true;
                    },
                    success: function(data) {
                        objectdata = data[SupernovaName];
                        var dateparts = SuperNovaeData[SupernovaName].maxlightdate.split("/");
                        var maxlightdate = new Date(dateparts[0], dateparts[1] - 1, dateparts[2]);
                        mjd = maxlightdate.getMJD();
                        objectdata.mjd = mjd;
                        // lightcurvedata = sort_lightcurvedata(data[SupernovaName].photometry);
                        set_localcache(urls.urldata, objectdata);
                    },
                });

                urls.urllightcurve = "https://api.astrocats.space/" + SupernovaName + "/photometry/time+magnitude+band+e_magnitude";
                $.ajax({
                    url: urls.urllightcurve,
                    dataType: "json",
                    cache: true,
                    async: false,
                    beforeSend: function() {
                        if (get_localcache(urls.urllightcurve) !== null) {
                            console.log("Use cache");
                            lightcurvedata = get_localcache(urls.urllightcurve)
                            // setUp(lightcurvedata);
                            return false;
                        }
                        return true;
                    },
                    success: function(data) {
                        lightcurvedata = sort_lightcurvedata(data[SupernovaName].photometry);
                        set_localcache(urls.urllightcurve, lightcurvedata);
                    },
                });

                urls.urlreferences = "https://api.sne.space/" + SupernovaName + "/sources/reference"
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

            function set_BandSelect() {
                $.each(lightcurvedata, function(band, data) {
                    var o = new Option("option text", band);
                    $(o).html(band);
                    $("#BandSelect").append(o);
                });
            }

            function setPoints(lightcurvedata, mintime, maxtime) {
                min_max_time = get_minimum_maximum_time(lightcurvedata);
                min_max_mag = get_minimum_maximum_magnitude(lightcurvedata);
                var maxmag = 25;

                if (parseFloat(min_max_mag['maxmin']) < maxmag) {
                    min_max_mag['maxmagaxis'] = maxmag;
                }

                var dMag = min_max_mag['minmagaxis'] - min_max_mag['maxmagaxis']; // Helligkeitsunterschied
                var dMagpp = dMag / heightBox; // Temperatur pro Pixel
                var dMagAxis = (min_max_mag['maxmagaxis'] - min_max_mag['minmagaxis']) / 10;

                var dtime = parseFloat(min_max_time['maxtime']) - parseFloat(min_max_time['mintime']);
                var dtimepp = dtime / widthBox; // Time pro Pixel
                var dtimeAxis = (parseFloat(min_max_time['maxtime']) - parseFloat(min_max_time['mintime'])) / 10

                $.each(lightcurvedata, function(i, row) {
                    time = row[0];
                    mag = row[1];
                    mag = fix_lightcurve_data(row[1], row[2])
                    point(margin_x + (time - min_max_time['mintime']) / dtimepp, heightBox + margin_y - (min_max_mag['minmagaxis'] - mag) / dMagpp, 2, ctx, "#000", "#000");
                });

                // x-Achse Raster
                var tick = Math.round(min_max_time['mintime']);

                if (Math.round(dtimeAxis) == 0) {
                    dtimeAxis = 0.5;
                }
                // if (tick - mjd > 0) {
                while (tick <= min_max_time['maxtime']) {
                    var diff = Math.round(tick - mjd);
                    xPos = margin_x + (tick - min_max_time['mintime']) / dtimepp;
                    line(xPos, heightBox + margin_y, xPos, heightBox + margin_y + 10, ctx, "black");
                    text(xPos, heightBox + margin_y + 20, diff, ctx, "10px Arial", 0, "black", "center")
                    tick = tick + Math.round(dtimeAxis);
                    tick = tick + (dtimeAxis);
                }

                // Mark zero Point.
                if ($("#maxLight").is(":checked")) {
                    ctx.setLineDash([0]);
                    console.log(mjd - min_max_time['mintime'])
                    xPos = margin_x + (mjd - min_max_time['mintime']) / dtimepp;
                    line(xPos, heightBox + margin_y, xPos, heightBox + margin_y + 10, ctx, "red");
                    text(xPos, heightBox + margin_y + 20, 0, ctx, "10px Arial", 0, "red", "center");
                    ctx.setLineDash([5, 5]);
                    draw_vertical_line(xPos, Math.round((min_max_mag['minmagaxis'] - mag + 19.3) * 100) / 100);
                }

                // y-Achse Raster
                var MagMaxAxis = Math.floor(min_max_mag['maxmagaxis'])
                var MagMinAxis = roundUp(min_max_mag['minmagaxis'], 0)
                for (i = MagMaxAxis; i <= MagMinAxis; i -= dMagAxis) {
                    var y_axis = heightBox + margin_y - (min_max_mag['minmagaxis'] - i) / dMagpp
                    if (y_axis < heightBox + margin_y && y_axis > margin_y) {
                        line(margin_x - 5, y_axis, margin_x, y_axis, ctx, "#000");
                        text(margin_x - 30, y_axis + 4, i.toFixed(1), ctx, "10px Arial", Math.Pi / 2, "#000")
                    }
                }


                if ($("#vglLinie").is(":checked")) {

                    if ($("#BandSelect").val() == "V") {
                        var mag = parseFloat($("#hl_1").val());
                        var yPosMaxLight = heightBox + margin_y - ((min_max_mag['minmagaxis'] - (min_max_mag['minmagaxis'] - mag)) / dMagpp);
                        // y-Achse Raster (rechts)
                        for (i = -50; i <= 50; i += 1) {
                            var y_axis = yPosMaxLight + (i) / dMagpp - mag + 5;
                            if (y_axis < heightBox + margin_y && y_axis > margin_y) {
                                line(widthBox + margin_x + 5, y_axis, widthBox + margin_x, y_axis, ctx, "red");
                                text(
                                    widthBox + margin_x + 10,
                                    y_axis + 4,
                                    Math.round((i - 19.3) * 10) / 10,
                                    ctx,
                                    "10px Arial",
                                    Math.Pi / 2,
                                    "red"
                                )
                            }
                        }
                        draw_horizontal_line(yPosMaxLight, "Δm = " + (Math.round((min_max_mag['minmagaxis'] - mag + 19.3) * 10) / 10));
                        draw_SNIa(lightcurvedata, dMagpp, dtimepp, min_max_mag['minmagaxis'] - mag + 19.3);
                    } else {
                        text(margin_x + widthBox - 52, margin_y + 20, "Achtung! Der Fit steht lediglich im V-Band zur Verfügung!", ctx, "15px Arial", Math.Pi / 2, 'red', "right")
                    }

                }
                $("#loader").hide();
            }

            function draw_SNIa(lightcurvedata, dMagpp, dtimepp, yMagDiff) {
                min_max_mag = get_minimum_maximum_magnitude(lightcurvedata);
                min_max_time = get_minimum_maximum_time(lightcurvedata);

                // Hauptreihe im HRD zeichnen
                var fittingPoints = [{
                    t: -9,
                    m: -18.8
                }, {
                    t: 0,
                    m: -19.3
                }, {
                    t: 11.2,
                    m: -18.6
                }, {
                    t: 20.2,
                    m: -18.0
                }, {
                    t: 24.4,
                    m: -17.7
                }, {
                    t: 39,
                    m: -17.0
                }, {
                    t: 80,
                    m: -15.85
                }];
                var lines = [];
                xDiff = $("#lc_1").val();
                fittingPoints.forEach(function(key) {
                    y_hr = heightBox + margin_y - (min_max_mag['minmagaxis'] - key["m"] - yMagDiff) / dMagpp;
                    x_hr = (key["t"] - xDiff) / dtimepp + margin_x
                    p = {
                        x: x_hr,
                        y: y_hr
                    };
                    lines.push(p);
                })

                //draw smooth line
                ctx.beginPath();
                ctx.setLineDash([5]);
                ctx.lineWidth = 5;
                ctx.strokeStyle = "red";
                bzCurve(lines, 0.3, 1);
            }

            function sort_lightcurvedata(lightcurvedata) {
                var lightcurvedatatemp = {};
                $.each(lightcurvedata, function(i, row) {
                    var band = row[2];
                    if (!lightcurvedatatemp.hasOwnProperty(band)) {
                        lightcurvedatatemp[band] = [];
                    }
                    lightcurvedatatemp[band].push(row);
                });
                $.each(lightcurvedatatemp, function(band, row) {
                    if (row.length < 5) {
                        delete lightcurvedatatemp[band];
                    }
                });
                return lightcurvedatatemp;
            }

            function fix_lightcurve_data(mag, band) {
                // Atmospheric extinction
                // A.T. Young and W.M. Irvine, 1967, Astron. J, 72, pp945-950.
                //
                // -0.1 kommt von z.B. https://ned.ipac.caltech.edu/forms/calculator.html
                // var airextinction = 1 / (Math.cos((-0.0012 * ((1 / Math.cos(2 * Math.Pi / 360 * objectdata.dec[0].value)) ^ 2 - 1))));
                // console.log(mag + " vs. " + (mag / airextinction - 0.1))
                // mag = (mag / airextinction - 0.1);

                return mag - min_max_mag['maxmag_corr'];
            }

            function get_minimum_maximum_time(lightcurvedata) {
                var mintime = 1000000;
                var maxtime = 0;
                $.each(lightcurvedata, function(i, row) {
                    time = row[0];
                    if (time < mintime) {
                        mintime = time;
                        return;
                    }
                    if (time > maxtime) {
                        maxtime = time;
                        return;
                    }
                });
                return {
                    'mintime': mintime,
                    'maxtime': maxtime
                };
            }

            function get_minimum_maximum_magnitude(lightcurvedata) {
                var minmag = 0;
                var maxmag = 1000000;

                $.each(lightcurvedata, function(i, row) {
                    mag = parseFloat(row[1]);
                    if (mag > minmag) {
                        minmag = mag;
                        return;
                    }
                    if (mag < maxmag) {
                        maxmag = mag;
                        return;
                    }
                });

                $("#hl_1").attr("max", minmag - (maxmag - (maxmag - parseFloat(objectdata.maxappmag[0].value)) - 1) + 2);
                $("#hl_1").attr("value", 0);

                return {
                    'minmagaxis': minmag + 1,
                    'maxmagaxis': maxmag - (maxmag - parseFloat(objectdata.maxappmag[0].value)) - 1,
                    'minmag': minmag,
                    'maxmag': maxmag,
                    'maxmag_corr': maxmag - parseFloat(objectdata.maxappmag[0].value),
                    'maxmagabs': parseFloat(objectdata.maxabsmag[0].value),
                    'maxmagapp': parseFloat(objectdata.maxappmag[0].value)
                };
            }

            function draw_horizontal_line(y_line, DisplVal) {
                line(margin_x, y_line, widthBox + margin_x + 5, y_line, ctx, "red")
                draw_rectFill(widthBox + margin_x - 295, y_line - 10, widthBox + margin_x - 205, y_line + 10, ctx, 2, 0, "#fff", 1)
                draw_rect(widthBox + margin_x - 295, y_line - 10, widthBox + margin_x - 205, y_line + 10, ctx, 2, 0, "red")
                text(widthBox + margin_x - 247.5, y_line + 5, DisplVal, ctx, "15px Arial", Math.Pi / 2, "red", "center")

                draw_rectFill(widthBox + margin_x + 5, y_line - 10, widthBox + margin_x + 50, y_line + 10, ctx, 2, 0, "#fff", 1)
                draw_rect(widthBox + margin_x + 5, y_line - 10, widthBox + margin_x + 50, y_line + 10, ctx, 2, 0, "red")
                text(widthBox + margin_x + 27.5, y_line + 5, -19.3, ctx, "15px Arial", Math.Pi / 2, "red", "center")
            }

            function draw_vertical_line(t_Puls_Pos, t_val) {
                line(t_Puls_Pos, heightBox + margin_y, t_Puls_Pos, margin_y - 5, ctx, "red")
                // draw_rectFill(t_Puls_Pos - 25, margin_y - 5, t_Puls_Pos + 25, margin_y - 25, ctx, 2, 0, "#fff", 1)
                // draw_rect(t_Puls_Pos - 25, margin_y - 5, t_Puls_Pos + 25, margin_y - 25, ctx, 2, 0, "red")
                // text(t_Puls_Pos, margin_y - 10, t_val, ctx, "15px Arial", Math.Pi / 2, "red", "center")
            }

            function setUp() {
                clearCanvas(ctx, canvas);
                get_lightcurvedata();
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

            $("#SNSelect").on('change', function() {
                $('#BandSelect').children().remove().end();
                clear_localcache();
                set_BandSelect();
                drawImage();
            });
            $(".options_hl").on('mousemove', function() {
                drawImage();
            });
            $(".options_hl_cb").on('change', function() {
                $('#hl').toggleClass('hidden');
                drawImage();
            });
            $(".options_maxLight").on('change', function() {
                drawImage();
            });
            $('#references').click(function() {
                $("#referencesdiv").toggle(this.checked);
            });
            $("#BandSelect").on('change', function() {
                drawImage();
            });
            $("#ResetOffset").on('click', function() {
                xdiff = 0;
                drawImage();
            });
            $("#hl_1").on('click', function() {
                drawImage();
            });
            $("#lc_1").on('mousemove', function() {
                drawImage();
            });

            setUp();
            drawImage();

        });
    </script>
</body>

</html>