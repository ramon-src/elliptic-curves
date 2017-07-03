<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Elliptic Curve Points - Algorithm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
</head>

<body>

<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Elliptic Curve Points - Algorithm</a>
        </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-1">

            <form id="form" name="elliptic">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="prime">Choose Prime</label>
                            <select id="prime" name="prime" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="a">Choose A</label>
                            <select id="a" name="a" class="form-control" disabled>
                            </select>
                            <!--                            <input id="a" name="a" type="text" class="form-control">-->
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="b">Choose B</label>
                            <select id="b" name="b" class="form-control" disabled>
                            </select>
                            <!--                            <input id="b" name="b" type="text" class="form-control">-->
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-4 pull-right">
                        <button class="btn btn-info btn-sm pull-right" id="genElipticGRoup">Generate Eliptic Group
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div id="criveNumbers"></div>
                    </div>
                    <div class="col-lg-12">
                        <div id="elipticGroups"></div>
                    </div>
                </div>
            </form>
            <div class="row">
                <br>
                <div class="col-lg-2">
                    <label>Product</label>
                    <input id="product" name="product" type="number" min="1" value="1" class="form-control">

                </div>
                <div class="col-lg-5">
                    <label>P: </label>
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="start">(</span>
                                        <input type="number" id="p1" name="p1" class="form-control col-lg-6"
                                               aria-describedby="start">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="number" id="p2" name="p2" class="form-control col-lg-6"
                                               aria-describedby="end">
                                        <span class="input-group-addon" id="end">)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <label>Q: </label>
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="start">(</span>
                                        <input type="number" id="q1" name="q1" class="form-control col-lg-6"
                                               aria-describedby="start">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <input type="number" id="q2" name="q2" class="form-control col-lg-6"
                                               aria-describedby="end">
                                        <span class="input-group-addon" id="end">)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-right" style="margin-top: 10px;">
                    <button class="btn btn-primary btn-sm" id="sum" value="sum">+ Sum</button>
                    <button class="btn btn-success btn-sm" id="sub" value="sub">- Subtract</button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" id="result" style="margin-top: 10px;">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <button class="btn btn-default" id="clean">Clean</button>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>

<script>


    //  var env = 'http://localhost/rsa-encrypter';
//    var env = 'http://localhost:8080';
      var env = 'https://elliptic-curves.herokuapp.com';

    var listprimes = [];
    var primesReceived = [];
    $(window).ready(function () {
        $.ajax({
            url: env + "/src/ViewModel.php?getPrimes",
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                primesReceived = response.primes;
                $.each(JSON.parse(primesReceived), function (index, value) {
                    listprimes += "<option value='" + value + "'>" + value + "</option>";
                });
                $('#prime').append(listprimes);
            }
        });
    });


    $('form select, form input').on('change', function (e) {
        e.preventDefault();

        var attrName = $(this).attr('name'),
            val = $(this).val();
        if (attrName == 'prime') {
            $('form select').removeAttr('disabled');
            var list = [];
            var listEl = "";
            for (var i = 1; i < val; i++) {
                list.push(i);
            }
            $('#a, #b').find('option').remove().end();
            listEl += "<option value=''>Escolha um valor</option>";
            $.each(list, function (index, value) {
                listEl += "<option value='" + value + "'>" + value + "</option>";
            });
            $('#a, #b').append(listEl);
        }
        $.ajax({
            url: env + "/src/ViewModel.php?" + attrName + "=" + val,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.criveNumbers != null) {
                    var crives = "[ ";
                    for (var crive in response.criveNumbers)
                        crives += response.criveNumbers[crive] + " ";
                    crives += "]";
                    $('#criveNumbers').html("<label>Crive Numbers: </label><p>" + crives + "</p>");
                }
                console.log(response);
            }
        });
    });

    $('#genElipticGRoup').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: env + "/src/ViewModel.php?genElipticGroup",
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status == true) {
                    console.log(response.elipticGroup);
                    var elipticGroups = "E" + response.prime + " = { P infinity, ";
                    response.elipticGroup.forEach(function (point) {
                        elipticGroups += "(" + point[0] + ", " + point[1] + ") ";
                    });
                    elipticGroups += "}";
                    $('#elipticGroups').html("<label>Eliptic Groups: </label><p>" + elipticGroups + "</p>")
                } else {
                    alert(response.message);
                }
            }
        });
    });

    $('#sum, #sub').click(function (e) {
        var p1 = $('#p1').val();
        var p2 = $('#p2').val();
        var q1 = $('#q1').val();
        var q2 = $('#q2').val();
        var product = $('#product').val();
        var type = e.target.value;
        $.ajax({
            url: env + "/src/ViewModel.php",
            method: 'POST',
            dataType: 'json',
            data: {p1: p1, p2: p2, q1: q1, q2: q2, type: type, product: product},
            success: function (response) {
                console.log(response);
                response.results.forEach(function (result) {

                    $('#result').append(
                        "<p><b>&lambda;:</b> " + result.lambda + "</p>" +
                        "<p><p><b>R:</b> (" + result.x3 + ", " + result.y3 + ")</p>"
                    );

                });
            }
        });
    });

    $('#clean').click(function (e) {
        $('#result').html('');
    });
</script>
</body>

</html>