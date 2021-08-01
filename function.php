<?php
if(isset($_POST['folder_name']))
{
    $currentfolder = $_POST['currentfolder'];
    $folder_name = $_POST['folder_name'];
    $filename = $currentfolder.'/'.$folder_name;
    if(file_exists($filename))
    {
        for($i=1;;$i++)
        {
            $filename = $currentfolder.'/'.$folder_name;
            if(file_exists($filename."_копия$i"))
            {
                $filename = $filename."_копия".($i+1);
            }
            else {$filename=$filename."_копия$i";break;}
        }
    }
    mkdir($filename);
    getDiskFiles($currentfolder);
    
}
if(isset($_POST['check']))
{
    $array = $_POST['check'];
    $currentfolder = $_POST['currentfolder'];
    foreach($array as $poorname=>$value)
    {
        if(is_dir($poorname))
        {
            RDir($poorname);
        }
        else{
            unlink($poorname);
        }
        getDiskFiles($currentfolder);
    }
}
if(isset($_FILES['file']))
{
    $currentfolder = $_POST['currentfolder'];
    $uploadfile =  $currentfolder.'/'.basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

    } else {
        echo "Возможная атака с помощью файловой загрузки!\n";
    }
    getDiskFiles($currentfolder);
}

function RDir( $currentfolder ) {
    // если путь существует и это папка
    if ( file_exists( $currentfolder) AND is_dir( $currentfolder ) ) {
      // открываем папку
       $dir = opendir($currentfolder);
       while ( false !== ( $element = readdir( $dir ) ) ) {
         // удаляем только содержимое папки
         if ( $element != '.' AND $element != '..' )  {
           $tmp = $currentfolder . '/' . $element;
           chmod( $tmp, 0777 );
          // если элемент является папкой, то
          // удаляем его используя нашу функцию RDir
           if ( is_dir( $tmp ) ) {
            RDir( $tmp );
          // если элемент является файлом, то удаляем файл
           } else {
             unlink( $tmp );
          }
        }
      }
      // закрываем папку
       closedir($dir);
       // удаляем саму папку
      if ( file_exists( $currentfolder ) ) {
        rmdir( $currentfolder );
      }
    }
   }

if (isset($_GET['command']))
{
    if($_GET['command']=='delete')
    {
        $currentfolder = $_GET['currentfolder'];
        $poorname = $_GET['poorname'];
        if(is_dir($poorname))
        {
            RDir($poorname);
        }
        else{
            unlink($poorname);
        }
        getDiskFiles($currentfolder);
        // unlink($poorname);
        // header('Location: index.php');//??
    }
    if($_GET['command']=='openfolder')
    {
        $poorname = $_GET['poorname'];
        getDiskFiles($poorname);
    }
    if($_GET['command']=='search')
    {
        $currentfolder = $_GET['currentfolder'];
        $keyword = $_GET['keyword'];
        $files = scandir($currentfolder);
   
        foreach($files as $key => $value)
        {
            if (stripos($value, $keyword) !== false) {
                echo $value.", ";
              }
        }
  
        // $currentfolder = $_GET['currentfolder'];
        // echo $keyword.$currentfolder;
    }

}

function getFileTime($name,$disk)
{
    $file_date = filemtime("$disk/$name");
    return date('d-m-Y', $file_date);
}
function getFileSize($name,$disk)
{
    $size = filesize("$disk/$name");
    if($size>=0&&$size<1000)
    {
        $size = $size." б";
    }
    if($size>1000&&$size<1000000) 
    {
        $size = (int)($size/1000)." Кб";
    }
    if($size>1000000&&$size<1000000000)
    {
        $size = (int)($size/1000000)." Мб";
    }
    if($size>1000000000)
    {
        $size = $size." Гб";
    }
    return $size;
}
function getDiskFiles($currentfolder)
{
    require_once 'templates/header.html';
    echo '<form action="function.php" method="post">';
    echo "<input type='hidden' name='currentfolder' value='$currentfolder'";
    $files = scandir($currentfolder);
    foreach($files as $id => $name)
    {
        $poorname = $name;
        if($name == "."||$name == "..")
        {
            continue;
        }
        
       $size = getFileSize($name,$currentfolder);
       $file_date = getFileTime($name,$currentfolder);
       $is_folder=false;
       $poorname = $currentfolder.'/'.$name;
        switch(getExtention($name))
        {
            case "docx": $name = '<i class="fas fa-file-word fa-2x text-primary"></i> '.$name;break;
            case "prdx": $name = '<i class="fas fa-file-powerpoint fa-2x text-warning"></i> '.$name;break;
            case "": $name = '<i class="fas fa-folder fa-2x text-warning"></i> '.$name; $is_folder=true; break;//папка
            case "png":
            case "jpg":
            case "jpeg": $name = '<i class="fas fa-image fa-2x text-info"></i> '.$name;break;
            case "zip": 
            case "rar": $name = '<i class="fas fa-file-archive fa-2x"></i> '.$name;break;
            default : $name = '<i class="fas fa-file-alt fa-2x"></i> '.$name;
        }
        // $is_folder = getExtention($name)==""?true:false;
        echo "<tr>";
        echo "<td><input type='checkbox' name='check[$poorname]'></td>";
        if($is_folder) echo "<td><a href='function.php?command=openfolder&poorname=$poorname'>$name</a></td>";
        else echo "<td>$name</td>";
        echo <<<_TR
            <td>$file_date</td>
            <td>$size</td>
            <td>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <a href="$poorname" class="btn btn-success" download><i class="fas fa-arrow-circle-down"></i></a>
                </div>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal_$id"> <i class="fas fa-trash"></i></button>
               
                 <!-- Modal -->
                <div class="modal fade" id="modal_$id" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Уточнение</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что безвозвратно хотите удалить данный файл($poorname)
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <a class="btn btn-danger"  href="function.php?command=delete&poorname=$poorname&currentfolder=$currentfolder"> Удалить</a>
                    </div>
                    </div>
                </div>
                </div>
                <!-- Modal -->
            </td>
        </tr>
_TR;
    }
    require_once 'templates/footer.html';
    // echo '<input type="submit">';
    echo '</form>';
}
function getExtention($name)
{
    $path_info = pathinfo($name);
    return $path_info['extension'];
}
?>