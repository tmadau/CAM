<?php

    if (isset($_POST['submit'])) {

        session_start();
        include_once('../classes/functionality.php');

        $upload = new control();

        $webcamimage = 0;

        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $filename = $_FILES['file']['name'];
            $filetmplocation = $_FILES['file']['tmp_name'];
            $filesize = $_FILES['file']['size'];
            $fileerror = $_FILES['file']['error'];

            $explodedname = explode('.', $filename);
            $import_file_ext = end($explodedname);
            $file_ext = array('png', 'jpeg', 'jpg');
        }
        else if ($_POST['webcampic'] != 'empty') {
            $webcamimage = 1;
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['webcampic']));
            $import_file_ext = 'png';
            $image_storage_name = "../uploads/" . uniqid('', true) . '.' . $import_file_ext;
            $filetmplocation = "$image_storage_name";
            file_put_contents($filetmplocation, $data);
            $filedest = realpath($filetmplocation);
            echo $filedest . "<br><br>" . $image_storage_name . "<br><br>". $filetmplocation . "<br><br>";
        }
        else {
            header("Location: post.php?upload=picture");
            exit();
        }

        if ($webcamimage == 1 || in_array($import_file_ext, $file_ext)) {
            if ($webcamimage == 1 || $filesize <= 10000000) {
                if ($webcamimage == 0) {
                    $image_storage_name = uniqid('', true) . '.' . $import_file_ext;
                    $filedest = '../uploads/' . $image_storage_name;
                    move_uploaded_file($filetmplocation, $filedest);
                }

                echo "$filedest" . "<br>";
                echo "$filetmplocation" . "<br>";

                list($dst_width, $dst_height, $dst_type, $dst_attr) = getimagesize(realpath("$filedest"));
                
                echo "dst Image width " .  $dst_width . "<br>";
                echo "dst Image height " . $dst_height . "<br>";
                echo "dst Image type " .   $dst_type . "<br>";
                echo "dst Attribute " .    $dst_attr . "<br><br>";
                
                $stickerdst0 = $_POST['sticker0'];
                $stickerdst1 = $_POST['sticker1'];
                $stickerdst2 = $_POST['sticker2'];
                
                $image = imagecreatefrompng("$filedest");
                
                if ($_POST['sticker0'] != 'none') {
                    $stickerimage0 = imagecreatefrompng("$stickerdst0");
                    list($src_width0, $src_height0, $src_type0, $src_attr0) = getimagesize("$stickerdst0");
                    imagecopyresized($image, $stickerimage0, 0, 0, 0, 0, $dst_width, $dst_height , $src_width0, $src_height0);
                }

                if ($_POST['sticker1'] != 'none') {
                    $stickerimage1 = imagecreatefrompng("$stickerdst1");
                    list($src_width1, $src_height1, $src_type1, $src_attr1) = getimagesize("$stickerdst1");
                    imagecopyresized($image, $stickerimage1, 0, 0, 0, 0, $dst_width, $dst_height , $src_width1, $src_height1);
                }
                
                if ($_POST['sticker2'] != 'none') {
                    $stickerimage2 = imagecreatefrompng("$stickerdst2");
                    list($src_width2, $src_height2, $src_type2, $src_attr2) = getimagesize("$stickerdst2");
                    imagecopyresized($image, $stickerimage2, 0, 0, 0, 0, $dst_width, $dst_height , $src_width2, $src_height2);
                }

                header("content-type: image/png");
                imagepng($image, $filedest);
                
                $username = $_SESSION['username'];

                $upload_pic = $upload->save($username, $image_storage_name);
                    
                header("location: post.php?success");
                exit();
            }
            else {
                header("Location: post.php?upload=size");
                exit();
            }
        }
        else {
            header("Location: post.php?upload=ext");
            exit();
        }
    }

?>
