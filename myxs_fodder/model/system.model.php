<?php

class SystemModelClass {

    public function index($data,$key){
        return array(
            "data" => $data,
            "key" => $key
        );
    }
}
