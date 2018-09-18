<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Upload Image Search</title>
    </head>

<body>
<form name="search_upload_form" action="{{ route('imgstore') }}" method="post" enctype="multipart/form-data" class="search-image-upload">
 	          {{ csrf_field() }}

        	<label class="label">You can select multiple image:</label>
        
	        <input type="file" name="upload[]" class="upload" multiple>
	   
<button class="submitBtn">submit</button>

</form>
</body>
</html>
