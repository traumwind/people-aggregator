<?php
/** !
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* [filename] is a part of PeopleAggregator.
* [description including history]
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* @author [creator, or "Original Author"]
* @license http://bit.ly/aVWqRV PayAsYouGo License
* @copyright Copyright (c) 2010 Broadband Mechanics
* @package PeopleAggregator
*/
?>
<?php
$login_required = TRUE;
$use_theme = 'Beta'; //TODO : Remove this when new UI is completely implemented.
include_once("web/includes/page.php");
require_once "api/Message/Message.php";
require_once "api/Tag/Tag.php";
require_once "api/ContentCollection/ContentCollection.php";
include_once "api/ModuleSetting/ModuleSetting.php";
include_once "api/Theme/Template.php";
include_once "ext/NetworkLinks/NetworkLinks.php";
include_once "api/Validation/Validation.php";
include_once "web/includes/network.inc.php";
if ( PA::$network_info ) {
  $db_extra = unserialize(PA::$network_info->extra);
  $form_data['extra'] = $db_extra;  
}
$authorization_required = TRUE;
$error = FALSE;

if ($_POST) {
  filter_all_post($_POST);  
} 

if (@$_POST['link_categories']) {
    $tmp_array = explode(':', $_POST['link_categories']);
    $_POST['category_id'] = $tmp_array[0];
}
$set_net = FALSE;
$error_array = array();
if (@$_POST['btn_apply_name']) {
  $_POST['category_name'] = htmlspecialchars($_POST['category_name']);
    if ($_POST['form_action'] == "update") {
          if (!empty($_POST['category_name'])) { // updating a category name.
              try {
                  $Links = new NetworkLinks();
                  //$tmp_array = explode(':', $_POST['link_categories']);
                  $_POST['category_id'] = $tmp_array[0];
                  $param_array = array('category_name'=>$_POST['category_name'], 'category_id'=>$tmp_array[0], 'changed'=>time(), 'user_id'=> $_SESSION['user']['id']);                  
                  $Links->set_params($param_array);
                  $Links->update_category ();
                  $set_net = TRUE;   
                  $error_array[] = "Category updated successfully";
              } catch (PAException $e) {
                  $error_array[] = $e->message;
              }
          
          } else {
              $error_array[] = "Please select a category";
          }
                  
    } else {    // adding new category.
        if (empty($_POST['category_name'])) {
            $error_array[] = "Please enter a category name";
            
        } else if (!Validation::validate_alpha_numeric($_POST['category_name'], 1)) {
            $error_array[] = "Please enter a valid category name";
            
        } else if (!empty($_POST['category_name'])) {
            try {
                $Links = new NetworkLinks();
                $param_array = array('user_id'=>$_SESSION['user']['id'], 'category_name'=> $_POST['category_name'], 'created'=> time(), 'changed'=> time());
                $Links->set_params ($param_array);
                $Links->save_category ();
                $set_net = TRUE;   
                $error_array[] = "Category added successfully";
            } catch (PAException $e) {
                  $error_array[] = $e->message;
            }  
        
        }
    
    }
    
}//... end of $_POST['btn_apply_name']
 else if (@$_POST['btn_save_link']) {
   $_POST['title'] = htmlspecialchars($_POST['title']);
   $_POST['url'] = htmlspecialchars($_POST['url']);
      if (empty($_POST['link_categories'])) {
          $error_array[] = "Please select a category";
      }
      
      if (empty($_POST['title'])) {
          $error_array[] = "Please enter a title for the link";
      }
      
      if (empty($_POST['url'])) {
           $error_array[] = "Please enter the URL for the link";
      } 
      $_POST['url'] = validate_url ($_POST['url']); 
      if (!Validation::isValidURL($_POST['url'])) {
        $error_array[] = "Please enter a valid URL for the link";
      }
                                 
      if (count($error_array) == 0) {
         //$tmp_array = explode(':', $_POST['link_categories']);
         //$_POST['category_id'] = $tmp_array[0];
            try {
                  if ($_POST['form_action'] == "update") {
                      $id_array = $_POST['link_id'];
                      $temp = explode(':', $id_array[0]);
                      $link_id = $temp[1];
                      
                      $param_array = array('user_id'=> $_SESSION['user']['id'], 'category_id'=> $tmp_array[0], 'title'=> $_POST['title'], 'url'=> $_POST['url'], 'changed'=> time(), 'link_id'=> $link_id);
                      $Links = new NetworkLinks();
                      $Links->set_params ($param_array);  
                      $Links->update_link ();  
                      unset($_POST['title']);
                      unset($_POST['url']);
                      $set_net = TRUE;   
                      $error_array[] = 'Link updated successfully';
                  
                  } else {
                      $param_array = array('user_id'=> $_SESSION['user']['id'], 'category_id'=> $tmp_array[0], 'title'=> $_POST['title'], 'url'=> $_POST['url'], 'changed'=> time(), 'created'=> time());
                      //p($param_array);
                      $Links = new NetworkLinks();
                      $Links->set_params ($param_array);
                    
                      $Links->save_link ();
                      unset($_POST['title']);
                      unset($_POST['url']);
                      $set_net = TRUE;   
                      $error_array[] = 'Link added successfully';
                      
                  } 
              
                
              
            } catch (PAException $e) {
                $error_array[] = $e->message;
            }         
        
        
              
      }             
} else if (@$_POST['form_action'] == "delete_links" && count($_POST['link_id']) > 0) {
    $link_id_array = $_POST['link_id'];
    for ($counter = 0; $counter < count($link_id_array); $counter++) {
        $temp_array = explode(':', $link_id_array[$counter]);
        $link_ids[] = $temp_array[1];
    }
    
   
    $Links = new NetworkLinks();
    $param_array = array('user_id'=> $_SESSION['user']['id'], 'changed'=> time(), 'link_id'=> $link_ids);  
    $Links->set_params($param_array);
    try {
        $Links->delete_link();
        $set_net = TRUE;
        $error_array[] = "Links deleted successfully";
    } catch (PAException $e) {
        $error_array[] = $e->message;
    }
    
} else if (@$_POST['form_action'] == "delete_category" && !empty($_POST['link_categories'])) {
    
    $param_array = array('user_id'=> $_SESSION['user']['id'], 'category_id'=> $_POST['category_id'], 'changed'=> time());
    
    $Links = new NetworkLinks();
    $Links->set_params ($param_array);
    try {
        $Links->delete_category();
        $set_net = TRUE;
        $error_array[] = "Category deleted successfully";
    } catch (PAException $e) {
        $error_array[] = $e->message;
    }
}

function setup_module($column, $moduleName, $obj) {
    global $uid;
    switch ($column) {    
    case 'middle': 
        $obj->get_link_categories ();
        $obj->uid = $uid;
    break;
    }
    $obj->mode = PUB;
}

$page = new PageRenderer("setup_module", PAGE_NETWORK_LINKS, "Network links", "container_two_column.tpl", "header.tpl", PUB, HOMEPAGE, PA::$network_info);

$page->html_body_attributes ='class="no_second_tier network_config"';
$message = NULL;
if (count(@$error_array) > 0) {
  for ($counter = 0; $counter < count($error_array); $counter++) {
      $message .= $error_array[$counter]."<br>";
  }
  uihelper_error_msg($message);
}

uihelper_get_network_style();

echo $page->render();
?>