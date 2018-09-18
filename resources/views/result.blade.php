<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Image Search</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            /* The grid: Four equal columns that floats next to each other */
            .column {
                float: left;
                width: 10%;
                padding: 10px;
            }

            /* Style the images inside the grid */
            .column img {
                opacity: 0.8; 
                cursor: pointer;
                height: 50px;
                width: 50px;
            }

            .column img:hover {
                opacity: 1;
            }

            /* Clear floats after the columns */
            .row:after {
                content: "";
                display: table;
                clear: both;
            }

            /* The expanding image container (positioning is needed to position the close button and the text) */
            .container {
                position: relative;
                display: none;
            }

            /* Expanding image text */
            #imgtext {
                position: absolute;
                bottom: 15px;
                left: 15px;
                color: white;
                font-size: 20px;
            }

            /* Closable button inside the image */
            .closebtn {
                position: absolute;
                top: 10px;
                right: 15px;
                color: white;
                font-size: 35px;
                cursor: pointer;
            }
        </style>
    <div class="flex-center position-ref full-height">
        <div class="row">
            <h3>Results</h3>
            @if(!empty($results['result']))
            @foreach($results['result'] as $img)
            <div class="column">
                <img src="{{ $img }}" style="width:100%" onclick="ExpandImages(this);">
            </div>
            @endforeach            
            @else
            <span>no result found</span>
            @endif
        </div>
        <div class="row">
            <h3>Similar visual</h3>
            @if(!empty($results['similar']))
            @foreach($results['similar'] as $img1)
            <div class="column">
                <img src="{{ $img1 }}" style="width:100%" onclick="ExpandImages(this);">
            </div>
            @endforeach            
            @else
            <span>no result found</span>
            @endif
        </div>

        <!-- The expanding image container -->
        <div class="container">
            <!-- Close the image -->
            <span onclick="this.parentElement.style.display = 'none'" class="closebtn">&times;</span>

            <!-- Expanded image -->
            <img id="expandedImg" style="width:100%">

            <!-- Image text -->
            <div id="imgtext"></div>
        </div>
    </div>

    <script>
        function ExpandImages(imgs) {
            // Get the expanded image
            var expandImg = document.getElementById("expandedImg");
            // Get the image text
            var imgText = document.getElementById("imgtext");
            // Use the same src in the expanded image as the image being clicked on from the grid
            expandImg.src = imgs.src;
            // Use the value of the alt attribute of the clickable image as text inside the expanded image
            imgText.innerHTML = imgs.alt;
            // Show the container element (hidden with CSS)
            expandImg.parentElement.style.display = "block";
        }
    </script>
</div>
</body>
</html>
