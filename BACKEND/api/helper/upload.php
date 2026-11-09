<?php

function uploadImage($file,$folder="../../uploads/")
{

    if($file['error']!=0){
        return false;
    }

    $ext=pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    );

    $filename=uniqid().".".$ext;

    if(move_uploaded_file(
        $file['tmp_name'],
        $folder.$filename
    )){

        // copy thêm sang FRONTEND/uploads
        $fe = __DIR__."/../../../uploads/";
        if(is_dir($fe)){
            @copy($folder.$filename, $fe.$filename);
        }

        return $filename;

    }

    return false;

}

function deleteImage($image,$folder="../../uploads/")
{

    $file=$folder.$image;

    if(file_exists($file)){

        unlink($file);

    }

    $fe = __DIR__."/../../../uploads/".$image;
    if($image!="" && file_exists($fe)){
        unlink($fe);
    }

}