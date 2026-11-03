<?php

function success($data=[],$message="Success")
{
    echo json_encode([
        "success"=>true,
        "message"=>$message,
        "data"=>$data
    ]);
    exit();
}

function error($message="Error",$code=400)
{
    http_response_code($code);

    echo json_encode([
        "success"=>false,
        "message"=>$message
    ]);

    exit();
}