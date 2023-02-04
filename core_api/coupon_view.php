<?php
    include("dbconnection.php");
    $json = file_get_contents("php=>//input");
	$data = json_decode($json);
    $add = array();
    $mainArray = array();

	$user_id  = isset($_REQUEST['user_id ']) ? $_REQUEST['user_id '] : "";
    
    $msg = "";
    $flag = 1;

    if($userId == "")
    {
        $flag = 0;
        $msg .= "user_id required" ;
    }

    if($flag == 1)
    {
    
        $addressesQRY = "SELECT * FROM `coupons` WHERE `user_id` = '".$user_Id."'";

        if(mysqli_query($connection,$addressesQRY))
        {
            while($row = mysqli_fetch_array($addressesQRY))
            {
                $eServices = array();
                $categories = array();
                $eProviders = array();

                $selectDicountable = mysqli_query($connection,"SELECT DISTINCT(`discountable_type`) as `type` FROM `discountables` WHERE `coupon_id` = '".$row['id']."'");

                if(mysqli_num_rows($selectDicountable) > 0)
                {
                    while($res = mysqli_fetch_array($selectDicountable))
                    {
                        $selectTypeData = mysqli_query($connection,"SELECT `discountable_id` FROM `discountables` WHERE `discountable_type` = '".trim($res['type'])."' AND `coupon_id` = '".$row['id']."'");

                        if(mysqli_num_rows($selectTypeData) > 0)
                        {
                            while($newResult = mysqli_fetch_array($selectTypeData))
                            {
                                if($res['type'] == 'App\Models\Category')
                                {
                                    $categories[] = $newResult['discountable_id'];
                                }
                                else if($res['type'] == 'App\Models\EService')
                                {
                                    $eServices[] = $newResult['discountable_id'];
                                }
                                else
                                {
                                    $eProviders[] = $newResult['discountable_id'];
                                }
                            }
                        }
                    }

                   
                }

                $add[] = array("id" => $row['id'],
                "code"=> $row['code'],
                "discount"=> $row['discount'],
                "discount_type"=> $row['discount_type'],
                "description"=> $row['description'],
                "expires_at"=> $row['expires_at'],
                "enabled"=> $row['enabled'],
                "created_at"=> $row['created_at'],
                "updated_at"=> $row['updated_at'],
                "user_id"=> $row['user_id']);
                
                $mainArray[] = array("coupon" => $add, "eServices" => $eServices, "eProviders" => $eProviders, "categories" => $categories) ;
            }
        }

        $jsonArr = array("message" => "success","data" => $mainArray);
        header("Content-Type: application/json");
        echo json_encode($jsonArr);
    }
    else
    {
        $jsonArr = array("success" => true, "message" => $msg,"data" => $mainArray);
        header("Content-Type: application/json");
        echo json_encode($jsonArr);
    }

?>