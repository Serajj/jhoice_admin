<?php
    include("dbconnection.php");
    $json = file_get_contents("php://input");
	$data = json_decode($json);
    $add = array();

	$user_id  = isset($data->user_id ) ? $data->user_id  : "";
    $address = isset($data->address) ? $data->address : "";
    $description = isset($data->description) ? $data->description : "";
    $default = isset($data->default) ? $data->default : 0;
    $latitude = isset($data->latitude) ? $data->latitude : 0;
    $longitude = isset($data->longitude) ? $data->longitude : 0;
    $type = isset($data->type) ? $data->type : 'provider';

    $msg = "";
    $flag = 1;

    if($user_id == "")
    {
        $flag = 0;
        $msg .= "user_id required " ;
    }

    if(trim($address) == "")
    {
        $flag = 0;
        $msg .= "address required ";
    }

    if(trim($description) == "")
    {
        $flag = 0;
        $msg .= "description required";
    }


    if($flag == 1)
    {
    
        $addressesQRY = "INSERT INTO `addresses` SET `user_id` = '".$user_id."', `address` = '".$address."', `description` = '".$description."', `default` = '".$default."', `latitude` = '".$latitude."', `longitude` = '".$longitude."'";
        
        if(mysqli_query($connection,$addressesQRY))
        {
            if($provider == "customer")
            {
                $userData = mysqli_query($connection,"SELECT * FROM `addresses` WHERE `user_id` = '".$user_id."' ");
                if(mysqli_num_rows($userData) > 0)
                {
                    while($row = mysqli_fetch_array($userData))
                    {
                        $add[] = array("id" => $row['id'],
                        "description"=> $row['description'],
                        "address"=> $row['address'],
                        "latitude"=> $row['latitude'],
                        "longitude"=> $row['longitude'],
                        "default"=> $row['default'],
                        "user_id"=> $row['user_id'],
                        "custom_fields"=> []);
                    }
                }
            }
            else
            {
                $lastId = mysqli_insert_id($connection) ;
                $qry = mysqli_query($connection,"INSERT INTO `e_provider_addresses` SET `e_provider_id` = '".$user_id."', `address_id` = '".$lastId."'");

                $userData = mysqli_query($connection,"SELECT `address_Id` FROM `e_provider_addresses` SET WHERE `e_provider_id` = '".$user_id."'");

                if(mysqli_num_rows($userData) > 0)
                {
                    while($dt = mysqli_fetch_array($userData))
                    {
                        $row = mysqli_fetch_array(mysqli_query($connection,"SELECT * FROM `addresses` WHERE `id` = '".$userData['address_Id']."'")) ;

                        $add[] = array("id" => $row['id'],
                        "description"=> $row['description'],
                        "address"=> $row['address'],
                        "latitude"=> $row['latitude'],
                        "longitude"=> $row['longitude'],
                        "default"=> $row['default'],
                        "user_id"=> $row['user_id'],
                        "custom_fields"=> []);
                    }
                }
            }

            $jsonArr = array("success" => true,"message" => "Address Saved successfully","data" => $add);
            header("Content-Type: application/json");
            echo json_encode($jsonArr);
        }
        else
        {
            $jsonArr = array("success" => false, "message" => "please try again.","data" => $add);
            header("Content-Type: application/json");
            echo json_encode($jsonArr);
        }
    }
    else
    {
        $jsonArr = array("success" => false, "message" => $msg,"data" => $add);
        header("Content-Type: application/json");
        echo json_encode($jsonArr);
    }

?>