tinymce.init({
	selector: '#tinyMCEtextarea',
	plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table paste"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	paste_data_images: true,
	image_title: true,
	images_upload_url: 'view/uploadTinyMCE.php',
    convert_urls:true,
    remove_script_host:false,
    relative_urls:false,
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
      
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'view/uploadTinyMCE.php');
      
        xhr.onload = function() {
            var json;
        
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
        
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            success(json.location);
        };
        
        formData = new FormData();

        if(typeof(blobInfo.blob().name) !== undefined)
            fileName = blobInfo.blob().name;
        else
            fileName = blobInfo.filename();
        
        formData.append('file', blobInfo.blob(), fileName);
      
        xhr.send(formData);
    }
});