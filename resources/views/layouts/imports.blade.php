<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!---Basic Bootstrap---->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
    <!--------Datatable-------->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js">
    </script>
    <script src="{{asset('assets/js/user/manga/versionTable/versionTable.js')}}"></script>
    <script src="{{asset('assets/js/user/manga/searchTable/searchTable.js')}}"> </script>
    <!-----fontawesome------>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
        integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
        crossorigin="anonymous" />
    <!---MDBOOSTRAP--->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/css/star-rating.min.css" media="all"
        rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-svg/theme.css"
        media="all" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/js/star-rating.min.js"
        type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-svg/theme.js">
    </script>
    <style>
        body,
        .list-background {
            background-color: rgb(230, 238, 255);
        }

        .list-background span {
            font-weight: bold;
            font-style: italic;
        }

        .list-background:hover {
            font-weight: bold;
            font-style: italic;
            border: 1px solid blue;
        }

        .card-border {
            border-radius: 15px;
            transition: box-shadow 0.2s;
            transition: transform 0.2s;
        }

        .card-group .card-border:hover {
            box-shadow: 6px 6px 0px rgba(51, 51, 153, 1);
            transform: translate(-5px, -5px)
        }
    </style>
    <style>
        .card:hover {
            border: 1px solid blue;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background-color: lightseagreen;
            border: 2px solid ghostwhite;
            opacity: 0.30;
        }

        .carousel {
            height: 485px;
        }

        .carousel .card-body {
            height: 100%;
        }

        .carousel-card .card-body {
            background: rgb(102, 102, 255, 0.2);

        }

        .carousel-card {
            transition: transform 0.2s;
        }

        .carousel-card:hover {
            transform: scale(1.2);
        }
    </style>
    <style>
        .icon {
            color: rgb(0, 204, 255);
            font-size: 2rem
        }

        .icon:hover {
            font-size: 2.2rem;
        }
    </style>
    <style>
        .resource-search-button {
            width: 100px;
        }
    </style>
    <style>
        /*Star rating*/

        .starrating>input {
            display: none;
        }

        /* Remove radio buttons */

        .starrating>label star {

            /* Star */
            margin: 2px;
            font-size: 8em;
            font-family: FontAwesome;
            display: inline-block;
        }

        .starrating>label {
            color: rgb(0, 153, 255);
            /* Start color when not clicked */
        }

        .starrating>input:checked~label {
            color: #ffca08;
        }

        /* Set yellow color when star checked */

        .starrating>input:hover~label {
            color: rgb(230, 184, 0);
        }

        /* Set yellow color when star hover */
    </style>
    <style>
        .notify-badge {
            position: absolute;
            right: -20px;
            top: 10px;
            background: rgb(0, 102, 255);
            text-align: center;
            border-radius: 30px 30px 30px 30px;
            color: white;
            padding: 5px 10px;
            font-size: 20px;
        }
    </style>
</head>

<body>
    @yield("cabecera")
    @yield("contenido")
    @yield("pie")
    <div class="container">
        <div class="row">
            <div class="col">
                <hr>

                <div class="row justify-content-center" style="height:120px;">
                    <div class="col-4 d-flex justify-content-center align-items-center"><a href=""><span
                                class="icon m-3" style=""><i class="fab fa-twitter"></i></span>twitter</a></div>
                    <div class="col-4 d-flex justify-content-center align-items-center"><a href=""><span
                                class="icon m-3" style=""><i class="fab fa-instagram"></i></span>instagram</a></div>
                    <div class="col-4 d-flex justify-content-center align-items-center"><a
                            href="https://github.com/sercopi/sercopiDownload"><span class="icon m-3" style=""><i
                                    class="fab fa-github-square"></i></span>github</a></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>