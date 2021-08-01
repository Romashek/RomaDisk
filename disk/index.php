<?php

require_once 'function.php';

if(isset($_SESSION['email'])) 
{
    require_once 'templates/header-enter.html';
    if(isset($_GET['page']))
    {
        switch($_GET['page'])
        {
            case "cameras":require_once 'templates/cameras.html';break;
            case "contacts":require_once 'templates/contacts.html';break;
            default: echo "<h5 class='text-center text-danger'>Страница не найдена</h5>";break;
        }
    }
    else require_once 'templates/person.html';
} 
else 
{
    require_once 'templates/header.html';

    if(isset($_POST['command']))
    {
        switch($_POST['command'])
        {
            case "register":registerUser($_POST);break;
            case "login":loginUser($_POST);break;
            case "update_pay": updatePay($_POST);break;
        }
    }

    // //Константы 12134, "asdsd", 's'
    // //if
    // $x = 10;

    // if($x==1) echo $x;
    // else if($x==2) echo $x+10;
    // else echo $x*2;

    // //switch case

    // $x=10;

    // switch($x)
    // {
    //     case 1:echo $x;break;
    //     case 2:echo $x+10;break;
    //     default: echo $x*2;
    // }


    if(isset($_GET['page']))
    {
        switch($_GET['page'])
        {
            case "login":require_once 'templates/login.html';break;
            case "register":require_once 'templates/register.html';break;
            case "contacts":require_once 'templates/contacts.html';break;
            default: echo "<h5 class='text-center text-danger'>Страница не найдена</h5>";break;
        }
    }
    else require_once 'templates/index.html';
}

require_once 'templates/footer.html';
?>