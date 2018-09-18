</!DOCTYPE html>
<html>
<head>
    <title></title>

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
</head>
<body>

        <div class="box-header with-border">
          <h3 class="box-title">Uploaded images</h3>
          <div style="margin: 0 15px 15px 0;" class="btn-group pull-right create-btn">
            <button><a style="padding: 4px 10px;" class="btn btn-primary btn-flat" href="{{ route('search.image') }}">
              Add Images
            </a></button>
          </div>
        </div>
        <div class="row">
            @if(!empty($images))
                @foreach($images as $img)
                    <?php

                        $image_name = explode('/', $img);
                        $image = $image_name[9];

                    ?>
                    <div class="column">
                      <img src="<?php echo url(''); ?>/images/search/{{ $image }}" style="width:100%;" class="searched_img">
                    </div>
                @endforeach            
            @else
            <span>no result found</span>
            @endif
        </div>
        
</body>
</html>    
