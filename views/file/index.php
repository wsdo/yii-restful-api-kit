<!DOCTYPE html>
<html>
<head>
	<title>upload</title>
</head>
<body>
	<form action="/file/index" method="post"
		enctype="multipart/form-data">
		<label for="file">Filename:</label>
        <input type="hidden" name="UploadForm[imageFile]" value="">
		<input type="file" name="UploadFile[imageFile]" id="file" /> 
		<input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
		<br />
		<input type="submit" name="submit" value="Submit" />
	</form>
</body>
</html>