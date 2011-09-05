<?php
namespace Reurbano\DealBundle\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/* Classe para manipulação de Upload do Deal */
class Upload
{
    
    protected $fileUploaded;
    protected $path;
    protected $fileName;
    
    public $validExtensions = array(
        'jpeg',
        'jpg',
        'pdf',
        'gif',
        'png',
     );

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
    
    public function upload(){
        
        if (!$this->fileUploaded instanceof UploadedFile){
            throw new NotFoundHttpException("Selecione um arquivo para enviar");
        }
        
        $fileName = uniqid(rand(), true) . '.' . $this->getFileExtension();;
        
        if ($this->getPath() != ""){
            while (file_exists($this->getPath().'/'.$fileName)){
                $fileName = uniqid(rand(), true) . '.' . $ext[count($ext)-1];
            }
            $this->getFileUploaded()->move($this->getPath(), $fileName);
        }else {
            throw new \Exception("Selecione um FolderPath");
        }
        
        $this->setFileName($fileName);
        
        return $this;
        
    }
    
    private function getFileExtension(){
        
        if (!$this->fileUploaded instanceof UploadedFile){
            throw new NotFoundHttpException("Selecione um arquivo para enviar");
        }
        
        $fileMimeType = $this->getFileUploaded()->getClientMimeType();
        $extMimeType = explode('/', $fileMimeType);
        $extMimeType = $extMimeType[count($extMimeType)-1];
        
        $fileExt = $this->getFileUploaded()->getClientOriginalName();
        $extFile = explode('.', $fileExt);
        $extFile = $extFile[count($extFile)-1];
        
        if (in_array($extMimeType, $this->validExtensions) || in_array($extFile, $this->validExtensions) ){
            return $extFile;
        }else {
            throw new NotFoundHttpException("O arquivo '". $this->getFileUploaded()->getClientOriginalName() ."' está em um formato inválido. Tipos permitidos: " . implode(', ', $this->validExtensions));
        }
        
    }
    
}