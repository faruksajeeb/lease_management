<?php
#### File Text Project ###
### created by Golam Mohiuddin (razualam00@gmail.com) @ 2017.10.24 ###
### Version: 1.0 ###
## please create a text file in this file's path name ft.db

class FT{
### EXAMPLES
#ft->set($parent='division', $key=6, $attribute='country', $value='bangladesh');
#ft->set($parent='division', $key=$id, $attribute=null, $value='Sylhet');
#ft->del('division', 6);
#ft->set($parent='division', $key=6, null, $attribute=(object) array('country'=>'India', 'name'=>'Mumbai', 'code'=>'00006'));
#ft->set($parent='district', $key=$id, $attribute=null, $value='Chandpur');
#ft->set($parent='division', $key=7, $attribute=null, $value='bangladesh');
#dd(ft->get('division'), 'c');
#dd(ft->search('division', 'country', 'bangladesh'));
/*
[parent] => stdClass Object
(
    [key] => Dhaka
    [key] => Chittagong
    [key] => Rajshahi
    [key] => stdClass Object
            (
	            [attribute] => bangladesh
	            [attribute] => Khulna
	            [attribute] => 0004
            )
)
*/


function FT(){
	# constructor
}

# settings
function settings(){
	$settings = new stdClass();
	$settings->ft_data_path = FCPATH.'application/libraries/ft.db';
	
	return $settings;
}

function get($parent=null, $key=null){
	try{
		$ft_data_path = $this->settings()->ft_data_path;
		$file_text = json_decode(file_get_contents($ft_data_path));
		
		if( !$parent && !$key ){
			return $file_text; # return all data -- need to re-think, is it OK or not?
		}
		
		if( !$parent && $key ){
			return 101; # no parent found
		}
		
		if( $parent && !isset($file_text->$parent) ){
			return array(); # no parent found
		}
		
		if( $parent && $key && !isset($file_text->$parent->$key) ){
			return 102; # no child found
		}
		
		if( $parent && !$key ){
			return $file_text->$parent; # return all parent data
		}elseif( $parent && $key ){
			return $file_text->$parent->$key; # return child data
		}
		
		return false; # nothing matched
		
	}catch(Exception $e){
		return 404; # execution faild
	}
}
function set($parent, $key, $attribute=null, $value){
	# $key must be an integer
	# value can be a single value or an object like (object) array('country'=>'India', 'name'=>'Mumbai', 'code'=>'00006')
	# regarding the above line, multiple attributes will be created within a single key
	# if key already exist then the key will be updated otherwise new row will be created
	$key = (int)$key;
	
	# parent, attribute and value can not be an array
	if(is_array($parent) || is_array($value) || is_array($attribute)){
		return 404; # could not execute
	}
	
	try{
		$ft_data_path = $this->settings()->ft_data_path;
		$file_text = json_decode(file_get_contents($ft_data_path));
		
		# if the parent has not been created, then create
		if( !isset($file_text->$parent) ){
			$file_text->$parent = new stdClass();
		}
		
		# if the key has not been created, then create
		if( !isset($file_text->$parent->$key) ){
			$file_text->$parent->$key = new stdClass();
		}
		
		# if the attribute has not been created, then create
		if( $attribute && !isset($file_text->$parent->$key->$attribute) ){
			$file_text->$parent->$key->$attribute = new stdClass();
		}
		
		if( $attribute ){
			# set attribute
			$file_text->$parent->$key->$attribute = $value;
		}else{
			# set key
			$file_text->$parent->$key = $value;
		}

		$new_text = json_encode($file_text);
		$myfile = fopen($ft_data_path, "w") or die("Unable to open file!");
		fwrite($myfile, $new_text);
		fclose($myfile);
		return 100; # successfully set
		
	}catch(Exception $e){
		return 404; # execution faild
	}
}
function iid($parent){
	# get insert id
	try{
		$ft_data_path = $this->settings()->ft_data_path;
		$file_text = json_decode(file_get_contents($ft_data_path));
		
		if( !isset($file_text->$parent) ){
			
			return 0; # no parent found
		}
		
		$select_parent = (array)$file_text->$parent;

		end($select_parent); # move pointer
		$key = key($select_parent);
		
		return (int)$key;
		
	}catch(Exception $e){
		return 404; # could not execute
	}
}
function del($parent, $key=null, $attribute=null){
	# ftdel('division'); # remove entire parent
	# ftdel('division', 6); # remove entire key with all attribute under that key
	# ftdel('division', 6, 'country'); # remove specific attribute under that key
	
	try{
		$ft_data_path = $this->settings()->ft_data_path;
		$file_text = json_decode(file_get_contents($ft_data_path));
		
		if( !isset($file_text->$parent) ){
			return 101; # no parent found
		}
		
		if( ($key===0 || $key) && !isset($file_text->$parent->$key) ){
			return 102; # no key found
		}
		
		if( ($key===0 || $key) && $attribute && !isset($file_text->$parent->$key->$attribute) ){
			return 103; # no attribute found
		}

		if( $parent && ($key===0 || $key) && $attribute ){
			unset($file_text->$parent->$key->$attribute);
		}elseif($parent && ($key===0 || $key)){
			unset($file_text->$parent->$key);
		}elseif($parent){
			unset($file_text->$parent);
		}else{
			return 404; # could not execute
		}
		
		$new_text = json_encode($file_text);
		$myfile = fopen($ft_data_path, "w") or die("Unable to open file!");
		fwrite($myfile, $new_text);
		fclose($myfile);
		return 100; # successfully deleted
		
	}catch(Exception $e){
		return 404; # execution faild
	}
}
function search($parent, $attribute=null, $value=null, $return_value=false){
	# if there is attribute then search with attribute otherwise search only value
	# ftsearch('division', 'country', 'bangladesh') -- search by attribute and value
	# ftsearch('division', null, 'bangladesh') -- search by value
	# if all parameter is given with $return_value=true then it will return the key
	
	try{
		$ft_data_path = $this->settings()->ft_data_path;
		$file_text = json_decode(file_get_contents($ft_data_path));
		
		if( !isset($file_text->$parent) ){
			return 101; # no parent found
		}
		
		if( !$attribute && !$value ){
			return 404; # must given either attribute or value
		}
		
		$temp_value = $file_text->$parent;

		$result = array();
		foreach($temp_value as $k=>$v){
			if( is_object($v) ){
				$v = (array)$v;
				foreach($v as $k1=>$v1){
					if($attribute && $k1==$attribute && strtolower($v1)==strtolower($value) && !$return_value){ 
						$result[] = $temp_value->$k; 
					}elseif( !$attribute && strtolower($v1)==strtolower($value)){
						$result[] = $temp_value->$k;
					}elseif(!$value && $attribute && $k1==$attribute){
						$result[] = $v1;
					}elseif($return_value && $attribute && $k1==$attribute && strtolower($v1)==strtolower($value)){
						$result[] = $k;
					}
				}
				
			}else{
				if( !$attribute && strtolower($v)==strtolower($value) ){ $result[] = $temp_value->$k; }
			}
		}
		
		return $result;
		
	}catch(Exception $e){
		return 404; # execution faild
	}
}

}
?>