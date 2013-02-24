<?php

/**
 * NAME:    fileUpload.php
 * AUTHOR:  Paul Everton
 * DATE:    Sept 8, 2011
 * DESCRIPTION: Object describing a file upload process
 */


class fileUpload extends object {
    
    private $MAX_SIZE = 100;
    public $actualFile;
    function fileUpload(){
        $this->actualFile = "";
    }
    
    
    
    function uploadFile($p_passedFileArray, $p_fileLoc){
                //reads the name of the file the user submitted for uploading
                $image=$p_passedFileArray['name'];
                //if it is not empty
                if ($image) 
                {
                //get the original name of the file from the clients machine
                        $filename = stripslashes($p_passedFileArray['name']);
                //get the extension of the file in a lower case format
                        $extension = $this->getExtension($filename);
                        $extension = strtolower($extension);
                //if it is not a known extension, we will suppose it is an error and will not  upload the file,  
                //otherwise we will do more tests
         if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
                        {
                        //print error message
                        $this->errorOccured();
                        $this->addMessage("Unknown extension!");

                        }
                        else
                        {
        //get the size of the image in bytes
         //$_FILES['image']['tmp_name'] is the temporary filename of the file
         //in which the uploaded file was stored on the server
         $size=filesize($p_passedFileArray['tmp_name']);

        //compare the size with the maxim size we defined and print error if bigger
        if ($size > $this->MAX_SIZE*1024)
        {
            $this->errorOccured();
            $this->addMessage("You have exceeded the size limit!");

        }

        //we will give an unique name, for example the time in unix time format
        $image_name=time().'.'.$extension;
        //the new name will be containing the full path where will be stored (images folder)
        $newname= $p_fileLoc. "" .$image_name;
        $this->actualFile = $image_name;
        //we verify if the image has been uploaded, and print error instead
        $copied = copy($p_passedFileArray['tmp_name'], $newname);
        if (!$copied) 
        {
                        $this->errorOccured();
            $this->addMessage("Copy unsuccessfull!");
        }}}
    }
    
    
//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
    function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
    }
    
    
}



?>