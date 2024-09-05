<?php        
$servername = "localhost";
$username = "tecmmedu_paginaWeb";
$password = "w3bp1zz42019";
$dbname = "tecmmedu_dashboard";
header("Access-Control-Allow-Origin: *");
    
    
    switch ($_REQUEST['action']) {
        case 'login': login($servername, $username, $password, $dbname); break;
    	case 'postBanner': postBanner($servername, $username, $password, $dbname); break;
    	case 'getBanners': getBanners($servername, $username, $password, $dbname); break;
    	case 'deleteBanners': deleteBanners($servername, $username, $password, $dbname); break;
    	case 'postNoticia': postNoticia($servername, $username, $password, $dbname); break;
    	case 'getNoticias': getNoticias($servername, $username, $password, $dbname); break;
    	case 'postConvocatoria': postConvocatoria(); break;
    	case 'prueba': echo $_REQUEST['campus']; break;
    }
    
    function login($servername, $username, $password, $dbname){
        $userName = $_POST["userName"];
        $passWord = $_POST["passWord"];
        
        $con = mysqli_connect($servername, $username, $password, $dbname);

        $sql = "SELECT * FROM users WHERE user= BINARY'$userName' AND password='$passWord'";
        
        $result = mysqli_query($con,$sql);
        
        if($result->num_rows != 0){
           //echo "Bienvenido"; 
            if (!$id) echo '[';
                for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
                    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
                }
            if (!$id) echo ']';
        }else{
            echo "El usuario no existe";
        }
        
        
        
    }
  
    function postNoticia($servername, $username, $password, $dbname){
        
        
        $upload_noticiaImagenes_dir = 'imagenesNoticiero/';
        $server_url = 'http://dashboard.tecmm.edu.mx';
        
        $titulo = $_POST["titulo"];
        $pathTitulo = $_POST["pathTitulo"];
        $contenido = $_POST["contenido"];
        $link = "http://tecmm.edu.mx/Noticia/".$pathTitulo;
        $campus = $_POST["campus"];
        
        $imgPrincipal_name = $_FILES["imagen"]["name"];
        $imgPrincipal_tmp_name = $_FILES["imagen"]["tmp_name"];
        
        $imgExtra1_name = $_FILES["imagenExtra1"]["name"];
        $imgExtra1_tmp_name = $_FILES["imagenExtra1"]["tmp_name"];
        
        $imgExtra2_name = $_FILES["imagenExtra2"]["name"];
        $imgExtra2_tmp_name = $_FILES["imagenExtra2"]["tmp_name"];
        
        $imgExtra3_name = $_FILES["imagenExtra3"]["name"];
        $imgExtra3_tmp_name = $_FILES["imagenExtra3"]["tmp_name"];
        
        $imgExtra4_name = $_FILES["imagenExtra4"]["name"];
        $imgExtra4_tmp_name = $_FILES["imagenExtra4"]["tmp_name"];
        
        $imgExtra5_name = $_FILES["imagenExtra5"]["name"];
        $imgExtra5_tmp_name = $_FILES["imagenExtra5"]["tmp_name"];
        
        
        $url_imgPrincipal = fileMover($imgPrincipal_name, $imgPrincipal_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        $url_imgExtra1 = fileMover($imgExtra1_name, $imgExtra1_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        $url_imgExtra2 = fileMover($imgExtra2_name, $imgExtra2_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        $url_imgExtra3 = fileMover($imgExtra3_name, $imgExtra3_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        $url_imgExtra4 = fileMover($imgExtra4_name, $imgExtra4_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        $url_imgExtra5 = fileMover($imgExtra5_name, $imgExtra5_tmp_name, $upload_noticiaImagenes_dir, $server_url);
        
        $urls_imgsExtras = [
            $url_imgExtra1,
            $url_imgExtra2,
            $url_imgExtra3,
            $url_imgExtra4,
            $url_imgExtra5,
        ];
        
        $json_urls_imgsExtras=json_encode($urls_imgsExtras);
        
        $sql = "insert into noticiero(titulo, pathTitulo, link, contenido, imagenPrincipal, imagenesExtra, campus) values ('$titulo', '$pathTitulo', '$link' ,'$contenido', '$url_imgPrincipal', '$json_urls_imgsExtras', '$campus')";
        ejecutarQuery($servername, $username, $password, $dbname, $sql);
        
        
        
    }
    function getNoticias($servername, $username, $password, $dbname){
        
        $con = mysqli_connect($servername, $username, $password, $dbname);
        
        $id = 0;
        $sql = "select * from noticiero  WHERE campus='".$_REQUEST['campus']."' OR campus='direccionGeneral' ORDER BY id ASC;";
        
        $result = mysqli_query($con,$sql);
        
        if (!$id) echo '[';
        for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
            echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
        }
        if (!$id) echo ']';
    }
    
    function postBanner($servername, $username, $password, $dbname){
        
        $upload_banner_dir = 'bannersCarousel/';
        $upload_documento_dir = 'documentosCarousel/';
        $server_url = 'http://dashboard.tecmm.edu.mx';
        
        /*variables que reciben el banner*/
        $banner_name = $_FILES["banner"]["name"];
        $banner_tmp_name = $_FILES["banner"]["tmp_name"];
        
        /*variables que reciben el documento*/
        $documento_name = $_FILES["documento"]["name"];
        $documento_tmp_name = $_FILES["documento"]["tmp_name"];
        
        /*variable que recibe el link*/
        $link = $_POST["enlace"];
        
        $campus = $_POST["campus"];
        
        
    
        //echo $link;
        $url_banner = fileMover($banner_name, $banner_tmp_name, $upload_banner_dir, $server_url);
        
        
        if($link == "" && $_FILES["documento"]==null){
            $sql = "insert into carousel(ref_banner, link, campus) values ('$url_banner', '/', '$campus')";
            ejecutarQuery($servername, $username, $password, $dbname, $sql);
        }else if($link != ""){
            $sql = "insert into carousel(ref_banner, link, campus) values ('$url_banner', '$link', '$campus')";
            ejecutarQuery($servername, $username, $password, $dbname, $sql);
        }else if($_FILES != null){
            $url_documento = fileMover($documento_name, $documento_tmp_name, $upload_documento_dir, $server_url);
            $sql = "insert into carousel(ref_banner, link, campus) values ('$url_banner', '$url_documento', '$campus')";
            ejecutarQuery($servername, $username, $password, $dbname, $sql);
        }
    }
    function getBanners($servername, $username, $password, $dbname){
        
        $con = mysqli_connect($servername, $username, $password, $dbname);
        
        $id = 0;
        $sql = "SELECT * FROM carousel WHERE campus='".$_REQUEST['campus']."' OR campus='direccionGeneral' ORDER BY id DESC ;";
        
        $result = mysqli_query($con,$sql);
        
        if (!$id) echo '[';
        for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
            echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
            
        }
        if (!$id) echo ']';
        
        
    }
    function deleteBanners($servername, $username, $password, $dbname){
         $sql = "DELETE FROM carousel WHERE id='".$_REQUEST['id']."';";
         ejecutarQuery($servername, $username, $password, $dbname, $sql);
    }
    
    
    function fileMover($file_name, $temporal_path, $upload_dir, $server_url){
        
        $dt = new DateTime();
        $fecha = $dt->format('Y-m-d');
        
        $upload_name = $upload_dir.$fecha."-".strtolower($file_name);
        
        if(move_uploaded_file($temporal_path, $upload_name)) {
            $url_banner=$server_url."/".$upload_name;
            return $url_banner;
        }else{
            return "Hubo un error al subir el archivo!";
        }
        
    }
    function ejecutarQuery($servername, $username, $password, $dbname, $sql){
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        if ($conn->query($sql) === TRUE) { 
            echo "Query OK"; 
        }else { 
            echo "Query Error: " . $sql . "<br>" . $conn->error; 
        }
        
        $conn->close();
        
    } 

?>