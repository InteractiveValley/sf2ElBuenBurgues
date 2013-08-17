<?php

namespace Richpolis\CategoriasGaleriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Galerias
 *
 * @ORM\Table(name="galerias")
 * @ORM\Entity(repositoryClass="Richpolis\CategoriasGaleriaBundle\Repository\GaleriasRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Galerias
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $archivo;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=255)
     */
    private $thumbnail;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_archivo", type="integer", length=1)
     */
    private $tipoArchivo;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="posicion", type="integer", length=1)
     */
    private $posicion;
    
    

     /**
     * @var \Categorias
     *
     * @ORM\ManyToOne(targetEntity="Categorias", inversedBy="galerias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * })
     */
    private $categoria;
    
    static public $IMAGEN=1;
    static public $LINK_VIDEO=2;
    static public $OTRO=3;
    
    
    static private $sTipoArchivos=array(
        1=>'Imagen',
        2=>'Link video',
        3=>'Otro',
    );

    public function getStringTipoArchivo(){
        return self::$sTipoArchivos[$this->getTipoArchivo()];
    }

    static function getArrayTipoArchivos(){
        return self::$sTipoArchivos;
    }

    static function getPreferedTipoArchivo(){
        return array(self::$IMAGEN);
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Galerias
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    
        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Galerias
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Galerias
     */
    public function setTipoArchivo($tipoArchivo)
    {
        $this->tipoArchivo = $tipoArchivo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipoArchivo()
    {
        return $this->tipoArchivo;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Galerias
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Galerias
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    
        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Galerias
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    
        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string 
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Galerias
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set posicion
     *
     * @param integer $posicion
     * @return Galerias
     */
    public function setPosicion($posicion)
    {
        $this->posicion = $posicion;
    
        return $this;
    }

    /**
     * Get posicion
     *
     * @return integer 
     */
    public function getPosicion()
    {
        return $this->posicion;
    }

    

    /**
     * Set categoria
     *
     * @param \Richpolis\CategoriasGaleriaBundle\Entity\Categorias $categoria
     * @return Galerias
     */
    public function setCategoria(\Richpolis\CategoriasGaleriaBundle\Entity\Categorias $categoria = null)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Richpolis\CategoriasGaleriaBundle\Entity\Categorias 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
    
    /*
     * Slugable
     */
    
    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setSlugAtValue()
    {
        $this->slug = \Richpolis\BackendBundle\Utils\Richsys::slugify($this->getTitulo());
    }
    
    /**
     * Regresa el titulo corto segun el maximo de caracteres solicitado
     * 
     * @return string
     * 
     */
    
    public function getTituloCorto($max=15){
        if($this->titulo)
            return substr($this->getTitulo(), 0, $max);
        else
            return "Sin titulo";
    }
    
    /*
     * Crea el thumbnail y lo guarda en un carpeta dentro del webPath thumbnails
     * 
     * @return void
     */
    public function crearThumbnail(){
        $imagine= new \Imagine\Gd\Imagine();
        $mode= \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $size=new \Imagine\Image\Box($this->getWidth(),$this->getHeight());
        $this->thumbnail=$this->archivo;
        
        $imagine->open($this->getAbsolutePath())
                ->thumbnail($size, $mode)
                ->save($this->getAbosluteThumbnailPath());
        
    }
    
    
    /*
     * 
     * 
     */
    
    /**
    ** @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preSaveGaleria()
    {
      if ($this->getTipoArchivo()==Galerias::$LINK_VIDEO) {
        $infoVideo=  \Richpolis\BackendBundle\Utils\Richsys::getTitleAndImageVideoYoutube($this->getArchivo());
        $this->setThumbnail($infoVideo['thumbnail']);
        $this->setArchivo($infoVideo['urlVideo']);
        $this->setTitulo($infoVideo['title']);
        $this->setDescripcion($infoVideo['description']);
      }
    }

    
    /*** uploads ***/
    
    public $file;
    
    /**
    ** @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function preUpload()
    {
      if (null !== $this->file) {
        // do whatever you want to generate a unique name
        $this->archivo       =   uniqid().'.'.$this->file->guessExtension();
        $this->thumbnail    =   $this->archivo;
      }
    }

    /**
    * @ORM\PostPersist
    * @ORM\PostUpdate
    */
    public function upload()
    {
      if (null === $this->file) {
        return;
      }

      // if there is an error when moving the file, an exception will
      // be automatically thrown by move(). This will properly prevent
      // the entity from being persisted to the database on error
      $this->file->move($this->getUploadRootDir(), $this->archivo);

      $this->crearThumbnail();
      
      unset($this->file);
    }

    /**
    * @ORM\PostRemove
    */
    public function removeUpload()
    {
      if ($file = $this->getAbsolutePath()) {
        if(file_exists($file)){
            unlink($file);
        }
      }
      if($thumbnail=$this->getAbosluteThumbnailPath()){
         if(file_exists($thumbnail)){
            unlink($thumbnail);
        }
      }
    }
    
    protected function getUploadDir()
    {
        return '/uploads/galerias';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }
    
    protected function getThumbnailRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir().'/thumbnails';
    }
        
    public function getWebPath()
    {
        if($this->getTipoArchivo()==Galerias::$IMAGEN){
            return null === $this->archivo ? null : $this->getUploadDir().'/'.$this->archivo;
        }else{
            return $this->getArchivo();
        }
    }

    public function getThumbnailWebPath()
    {
        if($this->getTipoArchivo()==Galerias::$IMAGEN){
            if(!$this->thumbnail){
                if(!file_exists($this->getAbosluteThumbnailPath()) && file_exists($this->getAbsolutePath())){
                    $this->crearThumbnail();
                }
            }
            return null === $this->thumbnail ? null : $this->getUploadDir().'/thumbnails/'.$this->thumbnail;
        }else{
            return $this->getThumbnail();
        }
    }
    
    public function getAbsolutePath()
    {
        return null === $this->archivo ? null : $this->getUploadRootDir().'/'.$this->archivo;
    }
    
    public function getAbosluteThumbnailPath(){
        return null === $this->thumbnail ? null : $this->getUploadRootDir().'/thumbnails/'.$this->thumbnail;
    }
    
    public function actualizaThumbnail()
    {
      if($thumbnail=$this->getAbosluteThumbnailPath()){
         if(file_exists($thumbnail)){
            unlink($thumbnail);
        }
      }
      $this->crearThumbnail();
    }
    
    public function getArchivoView(){
        $opciones=array(
            'tipo_archivo'  => \Richpolis\BackendBundle\Utils\Richsys::getTipoArchivo($this->getArchivo()),
            'archivo'   =>  $this->getArchivo(),
            'carpeta'   =>  'galerias',
            'width'     =>  700,
            'height'    =>  370,
        );
        
        return \Richpolis\BackendBundle\Utils\Richsys::getArchivoView($opciones);
    }
    public function getWidth(){
        switch($this->getCategoria()->getTipoCategoria()){
            case Categorias::$GALERIA: //300x225
            case Categorias::$RECOMENDACIONES: //300x225
                $resp= 300;
                break;
            case Categorias::$PUBLICACIONES: //
                $resp= 150;
                break;
            default :
                $resp= 300;
                break;
            
        }
        return $resp;
    }
    public function getHeight(){
        switch($this->getCategoria()->getTipoCategoria()){
            case Categorias::$GALERIA: //300x225
            case Categorias::$RECOMENDACIONES: //300x225
                $resp=225;
                break;
            case Categorias::$PUBLICACIONES:
                $resp= 100;
                break;
            default :
                $resp= 225;
                break;
        }
        return $resp;
    }
}
