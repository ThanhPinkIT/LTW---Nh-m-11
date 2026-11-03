<?php

require_once "../middleware/admin.php";
require_once "../helper/upload.php";

if(!isset($_FILES['image'])){

    response(
        false,
        "Image not found"
    );

}

$image = uploadImage($_FILES['image']);

if(!$image){

    response(
        false,
        "Upload failed"
    );

}

response(
    true,
    "Upload success",
    [
        "image"=>$image
    ]
);