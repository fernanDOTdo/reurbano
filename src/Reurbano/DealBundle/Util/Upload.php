<?php
namespace Reurbano\DealBundle\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/* Classe para manipulação de Upload do Deal */
class Upload
{
    
    protected $fileUploaded;
    protected $path;
    protected $fileName;

    public function __construct(UploadedFile $file){
        
        $this->setFileUploaded($file);
        
    }
    
    public function getFileUploaded() {
        return $this->fileUploaded;
    }

    public function setFileUploaded($fileUploaded) {
        $this->fileUploaded = $fileUploaded;
    }
    
    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getDeafaultPath(){
        return "/home/www/andre/reurbano/web/bundles/uploads/reurbanodeal";
    }
    
    public function upload(){
        
        if (!$this->fileUploaded instanceof UploadedFile){
            throw new NotFoundHttpException("Please, set a file to be uploaded");
        }
        
        $ext = $this->getFileUploaded()->getClientMimeType();
        $ext = explode('/', $ext);
        
        $fileName = uniqid(rand(), true) . '.' . $ext[count($ext)-1];
        
        if ($this->getPath() != ""){
            while (file_exists($this->getPath().'/'.$fileName)){
                $fileName = uniqid(rand(), true) . '.' . $ext[count($ext)-1];
            }
            $this->getFileUploaded()->move($this->getPath(), $fileName);
        }else {
            while (file_exists($this->getDeafaultPath().'/'.$fileName)){
                $fileName = uniqid(rand(), true) . '.' . $ext[count($ext)-1];
            }
            $this->getFileUploaded()->move($this->getDeafaultPath(), $fileName);
        }
        
        $this->setFileName($fileName);
        
        return $this;
        
    }
    
}