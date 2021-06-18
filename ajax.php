<?php
include "dbhelper.php";
include "credentialhelper.php";
$dbhelper = new dbhelper("root","moj","","localhost");
if (isset($_POST['key'])){
    switch ($_POST['key']){
        case "getord":
            $conn = mysqli_connect('localhost','root','','moj');
            $sql = "SELECT * FROM tbl_ord";
            $result = $conn->query($sql);
            $data = null;
            $response = null;
            if ($result->num_rows > 0){

                while($row = $result->fetch_assoc()){
                    $data[] = array(
                        "id"=>$row['ORD_ID'],
                        "year"=>$row['YEAR'],
                        "ord_num"=>$row['ORD_NUM'],
                        "title"=>$row['TITLE'],
                        "doa"=>$row['DATE_APPROVED'],
                        "file"=>$row['FILE'],
                    );
                }

                $response = array(
                    "data"=>$data
                );

            }
            echo json_encode($response);
            break;

        case "insertFeedback":
            $name = $_POST['name'];
            $comment = $_POST['comment'];
            $dbhelper->setFields("name","comment");
            $dbhelper->pushDB("feedback", ($name == "" ? "Anonymous" : $name), $comment);
            $response[] = array(
                "error"=>false,
                "message"=>"Feedback Successfully Sent",
            );
            $data = array(
                "data"=>$response
            );
            echo json_encode($data);
            break;

        case "fetchFeedback":
            $dbhelper->setFields("*");
            $row = $dbhelper->getData("feedback", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "name"=>$row[$i]['name'],
                    "comment"=>$row[$i]['comment'],
                    "status"=>$row[$i]['status'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;
        case "fetchFeedbackHomepage":
            $dbhelper->setFields("*");
            $row = $dbhelper->getData("feedback WHERE status='Approved' LIMIT 5", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "name"=>$row[$i]['name'],
                    "comment"=>$row[$i]['comment'],
                    "status"=>$row[$i]['status'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;

        case "editFeedback":
            if(isset($_POST['mode'])){
               $id = $_POST['id'];
               switch ($_POST['mode']){
                   case "currentreject":
                       $dbhelper->setFields("status");
                       $edit = $dbhelper->editData('feedback', $id, "id", "Rejected");

                       if($edit){
                           $response[] = array(
                               "error"=>false,
                               "message"=>"Feedback Record updated!!",
                               "id"=>$id
                           );
                       }else{
                           $response[] = array(
                               "error"=>true,
                               "message"=>"Something went wrong",
                           );
                       }

                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
                   case "currentaccept":
                       $dbhelper->setFields("status");
                       $edit = $dbhelper->editData('feedback', $id, "id", "Approved");

                       if($edit){
                           $response[] = array(
                               "error"=>false,
                               "message"=>"Feedback Record updated!!",
                               "id"=>$id
                           );
                       }else{
                           $response[] = array(
                               "error"=>true,
                               "message"=>"Something went wrong",
                           );
                       }

                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
                   case "allreject":
                       $dbhelper->setFields("status");
                       $edit = $dbhelper->editData('feedback', "1", "1", "Rejected");
                       if($edit){
                           $response[] = array(
                               "error"=>false,
                               "message"=>"All Feedbacks rejected"
                           );
                       }else{
                           $response[] = array(
                               "error"=>true,
                               "message"=>"Something went wrong",
                           );
                       }

                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
                   case "allaccept":
                       $dbhelper->setFields("status");
                       $edit = $dbhelper->editData('feedback', "1", "1", "Approved");
                       if($edit){
                           $response[] = array(
                               "error"=>false,
                               "message"=>"All Feedbacks Approved"
                           );
                       }else{
                           $response[] = array(
                               "error"=>true,
                               "message"=>"Something went wrong",
                           );
                       }

                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
                   case "checkedreject":
                       $dbhelper->setFields("status");
                       $items = $_POST['items'];

                       for($i = 0; $i < count($items); ++$i){
                           $dbhelper->editData('feedback', $items[$i], "id", "Rejected");
                       }

                       $response[] = array(
                           "error"=>false,
                           "message"=>"Checked Feedbacks Reject"
                       );
                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
                   case "checkedaccept":
                       $dbhelper->setFields("status");
                       $items = $_POST['items'];

                       for($i = 0; $i < count($items); ++$i){
                           $dbhelper->editData('feedback', $items[$i], "id", "Approved");
                       }

                       $response[] = array(
                           "error"=>false,
                           "message"=>"Checked Feedbacks Approved"
                       );
                       $data = array(
                           "data"=>$response
                       );
                       echo json_encode($data);
                       break;
               }
            }
            break;

        case "saveOrdinance":
            $id = $_POST['ORD_ID'];
            $year = $_POST['YEAR'];
            $doa = $_POST['DATE_APPROVED'];
            $title = $_POST['TITLE'];
            $document = $_POST['FILE'];
            $dbhelper->setFields("YEAR", "TITLE", "DATE_APPROVED", "FILE");
            $dbhelper->editData("tbl_ord", $id, "ORD_ID", $year, $title, $doa, $document);
            break;
        case "addDocuments":
            $dbhelper->getMultipleFiles($_FILES["FILE"]["name"]);
            $dbhelper->setFields("name", "upload_date", "uploader");
            foreach($_FILES["FILE"]["name"] as $files){
                $dbhelper->pushDB("documents", $files, date("Y-m-d"), "Admin");
            }
            break;
        case "getDocuments":
            $dbhelper->setFields("*");
            $row = $dbhelper->getData("documents", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "filename"=>$row[$i]['name'],
                    "date"=>$row[$i]['upload_date'],
                    "uploader"=>$row[$i]['uploader'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;
        case "addMedias":
            $dbhelper->getMultipleFiles($_FILES["FILE"]["name"]);
            $dbhelper->setFields("filename", "upload_date", "uploader");
            foreach($_FILES["FILE"]["name"] as $files){
                $dbhelper->pushDB("medias", $files, date("Y-m-d"), "Admin");
            }
            break;
        case "getMedias":
            $dbhelper->setFields("*");
            $row = $dbhelper->getData("medias", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "filename"=>$row[$i]['filename'],
                    "date"=>$row[$i]['upload_date'],
                    "uploader"=>$row[$i]['uploader'],
                    "album"=>$row[$i]['album'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;
        case "getMediasAlbum":
            $row = $dbhelper->getData("medias ORDER BY album ASC", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "filename"=>$row[$i]['filename'],
                    "date"=>$row[$i]['upload_date'],
                    "uploader"=>$row[$i]['uploader'],
                    "album"=>$row[$i]['album'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;
        case "getMediasHomepage":
            $dbhelper->setFields("*");
            $row = $dbhelper->getData("medias WHERE status='Show' LIMIT 5", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "filename"=>$row[$i]['filename'],
                    "uploader"=>$row[$i]['uploader'],
                    "upload_date"=>$row[$i]['upload_date'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;

        case "getCurrentMedia":
            $row = $dbhelper->getCurrentData("medias", $_POST['id'], "id", "*");
            $length = count($row);
            $data = null;
            for($i = 0; $i < $length; ++$i){

                $data[] = array(
                    "id"=>$row[$i]['id'],
                    "filename"=>$row[$i]['filename'],
                    "date"=>$row[$i]['upload_date'],
                    "uploader"=>$row[$i]['uploader'],
                    "album"=>$row[$i]['album'],
                );
            }
            $response = array(
                "data"=>$data
            );
            echo json_encode($response);
            break;

        case "editAlbum":
            $dbhelper->setFields("album");
            $edit = $dbhelper->editData('medias', $_POST['id'], "id", $_POST['album']);
            if($edit){
                $response[] = array(
                    "error"=>false,
                    "message"=>"Media Record updated!!",
                    "id"=>$_POST['id']
                );
            }else{
                $response[] = array(
                    "error"=>true,
                    "message"=>"Something went wrong",
                );
            }

            $data = array(
                "data"=>$response
            );
            echo json_encode($data);
            break;
        case "editMedia":
            if(isset($_POST['mode'])){
                $id = $_POST['id'];
                switch ($_POST['mode']){
                    case "currenthide":
                        $dbhelper->setFields("status");
                        $edit = $dbhelper->editData('medias', $id, "id", "Hide");

                        if($edit){
                            $response[] = array(
                                "error"=>false,
                                "message"=>"Media Record updated!!",
                                "id"=>$id
                            );
                        }else{
                            $response[] = array(
                                "error"=>true,
                                "message"=>"Something went wrong",
                            );
                        }

                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                    case "currentshow":
                        $dbhelper->setFields("status");
                        $edit = $dbhelper->editData('medias', $id, "id", "Show");

                        if($edit){
                            $response[] = array(
                                "error"=>false,
                                "message"=>"Media Record updated!!",
                                "id"=>$id
                            );
                        }else{
                            $response[] = array(
                                "error"=>true,
                                "message"=>"Something went wrong",
                            );
                        }

                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                    case "allhide":
                        $dbhelper->setFields("status");
                        $edit = $dbhelper->editData('medias', "1", "1", "Hide");
                        if($edit){
                            $response[] = array(
                                "error"=>false,
                                "message"=>"All Media Hidden"
                            );
                        }else{
                            $response[] = array(
                                "error"=>true,
                                "message"=>"Something went wrong",
                            );
                        }

                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                    case "allshow":
                        $dbhelper->setFields("status");
                        $edit = $dbhelper->editData('medias', "1", "1", "Show");
                        if($edit){
                            $response[] = array(
                                "error"=>false,
                                "message"=>"All Media Shown"
                            );
                        }else{
                            $response[] = array(
                                "error"=>true,
                                "message"=>"Something went wrong",
                            );
                        }

                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                    case "checkedhide":
                        $dbhelper->setFields("status");
                        $items = $_POST['items'];

                        for($i = 0; $i < count($items); ++$i){
                            $dbhelper->editData('medias', $items[$i], "id", "Hide");
                        }

                        $response[] = array(
                            "error"=>false,
                            "message"=>"Checked Media Hidden"
                        );
                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                    case "checkedshow":
                        $dbhelper->setFields("status");
                        $items = $_POST['items'];

                        for($i = 0; $i < count($items); ++$i){
                            $dbhelper->editData('medias', $items[$i], "id", "Show");
                        }

                        $response[] = array(
                            "error"=>false,
                            "message"=>"Checked Media Shown"
                        );
                        $data = array(
                            "data"=>$response
                        );
                        echo json_encode($data);
                        break;
                }
            }
            break;
    }
}
