<?php

namespace Mastop\SystemBundle\Locator;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\FileLocator as BaseFileLocator;

/**
* FileLocator usa o HttpKernel FileLocator para localizar Resources nos bundles
* e seguir um file path configurável.
*
* @author Fernando Santos <o@fernan.do>
*/
class FileLocator extends BaseFileLocator
{
    protected $kernel;
    protected $path;
    protected $basePaths = array();
    protected $theme;
    protected $activeTheme;

/**
* Construtor.
*
* @param KernelInterface $kernel Uma instancia de KernelInterface
* @param string $path O path para a pasta resource global
*
* @throws \InvalidArgumentException se o tema ativo não está na lista de temas
*/
    public function __construct(KernelInterface $kernel, $path = null, $paths = array())
    {
        $this->kernel = $kernel;
        $this->path = $path;
        $container = $kernel->getContainer();
        $this->theme = $container->get('mastop.themes');
        if(false == is_array($paths)){
            $this->basePaths = array($paths);
        }else{
            $this->basePaths = $paths;
        }
        $this->setActiveTheme($this->theme->getName());
    }
    
/**
* Seta o tema ativo.
*
* @param string $theme
*/
    private function setActiveTheme($theme)
    {
        $paths = $this->basePaths;
        $this->activeTheme = $theme;
        $paths[] = $this->path. "/" . $this->activeTheme . "/Bundles";

        $this->paths = $paths;
    }
    
/**
* Retorna o file path para o resource passado para a primeira pasta que tiver bater com o nome
*
* O nome precisa seguir o padrão:
*
* @BundleName/path/to/a/file.something
*
* em que BundleName é o nome do Bundle
* e o resto é o path relativo dentro do Bundle.
*
* @param string $name Um nome de resource para localizar
* @param string $dir Um diretório para procurar primeiro
* @param Boolean $first É para retornar o primeiro path ou os paths de todos os bundles encontrados?
*
* @return string|array O path absoluto do resource ou um array se $first = false
*
* @throws \InvalidArgumentException Se o arquivo não for encontrado ou nome for inválido
* @throws \RuntimeException Se o nome contiver caracteres inválidos/inseguros
*/
    public function locate($name, $dir = null, $first = true)
    {
        // atualiza o tema ativo se necessário.
        if($this->activeTheme !== $this->theme->getName()) {
            $this->setActiveTheme($this->theme->getName());
        }
        
        if(1 === 1){
            $this->paths[] = $this->path . "/" . $this->activeTheme . "/Backend";
        }else{
            $this->paths[] = $this->path . "/" . $this->activeTheme . "/Frontend";
        }
        if ('@' === $name[0]) {
            return $this->locateResource($name, $this->path, $first);
        }
        if($name == 'views/backend.html.twig'){
            return $this->path . "/" . $this->activeTheme . "/Backend/backend.html.twig";
        }elseif($name == 'views/frontend.html.twig'){
            return $this->path . "/" . $this->activeTheme . "/Frontend/frontend.html.twig";
        }
        return parent::locate($name, $dir, $first);
    }
    
/**
* Localizador de Resources
*
* Função xupinhada de Symfony\Component\Http\Kernel
*
* @param string $name
* @param string $dir
* @param bool $first
* @return string
*/
    public function locateResource($name, $dir = null, $first = true)
    {
        //$this->kernel->getContainer()->get('logger')->info('Dir: '.$dir);
        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('O arquivo "%s" contém caracteres inválidos (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }

        if (0 !== strpos($path, 'Resources')) {
            throw new \RuntimeException('Os arquivos de templates precisam estar em Resources.');
        }

        $overridePath = substr($path, 15); // remove Resources/views/
        $resourceBundle = null;
        $bundles = $this->kernel->getBundle($bundleName, false);
        $files = array();

        foreach ($bundles as $bundle) {
            $checkPaths = array();
            if ($dir) {
                $checkPaths[] = $dir.'/'.$this->activeTheme.'/Bundles/'.$bundle->getName().$overridePath;
                //$checkPaths[] = $dir.'/'.$bundle->getName().$overridePath;
                //exit(print_r($checkPaths));
            }
            //$checkPaths[] = $bundle->getPath() . '/Resources/themes/'.$this->activeTheme.substr($path, 15);
            foreach ($checkPaths as $checkPath) {
                if (file_exists($file = $checkPath)) {
                    if (null !== $resourceBundle) {
                        throw new \RuntimeException(sprintf('"%s" é ocultado por um recurso do bundle derivado "%s". Crie um arquivo "%s" para sobrescrever o recurso do bundle.',
                            $file,
                            $resourceBundle,
                            $checkPath
                        ));
                    }

                    if ($first) {
                        return $file;
                    }
                    $files[] = $file;
                }
            }

            if (file_exists($file = $bundle->getPath().'/'.$path)) {
                if ($first) {
                    return $file;
                }
                $files[] = $file;
                $resourceBundle = $bundle->getName();
            }
        }

        if (count($files) > 0) {
            return $first ? $files[0] : $files;
        }

        throw new \InvalidArgumentException(sprintf('Impossível encontrar o arquivo "%s".', $name));
    }
}