<?php

namespace Richpolis\PublicacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categorias
 *
 * @ORM\Table(name="categorias_publicacion")
 * @ORM\Entity(repositoryClass="Richpolis\PublicacionesBundle\Repository\CategoriasPublicacionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CategoriasPublicacion
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
     * @ORM\Column(name="categoria", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $categoria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $descripcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_categoria", type="integer", length=1)
     */
    private $tipoCategoria;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="posicion", type="integer", nullable=false)
     */
    private $posicion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_categoria", type="boolean")
     */
    private $isCategoria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Publicacion", mappedBy="categoria")
     */
    protected $publicaciones;
    
    static public $PLATILLOS=1;
    
    static private $sCategorias=array(
        1=>'Platillos',
    );
    
    public function __construct() {
        $this->isActive = true;
        $this->isCategoria = true;
        $this->tipoCategoria=self::$PLATILLOS;
        $this->publicaciones =new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getStringTipoCategoria(){
        return self::$sCategorias[$this->getTipoCategoria()];
    }

    static function getArrayTipoCategorias(){
        return self::$sCategorias;
    }

    static function getPreferedTipoCategoria(){
        return array(self::$PLATILLOS);
    }

    public function __toString()
    {
        return $this->getCategoria();
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
     * Set categoria
     *
     * @param string $categoria
     * @return Categorias
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
        
        return $this;
    }

    /**
     * Get categoria
     *
     * @return string 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }


    /**
     * Set tipoCategoria
     *
     * @param integer $tipoCategoria
     * @return Categorias
     */
    public function setTipoCategoria($tipoCategoria)
    {
        $this->tipoCategoria = $tipoCategoria;
    
        return $this;
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
     * Get tipoCategoria
     *
     * @return integer 
     */
    public function getTipoCategoria()
    {
        return $this->tipoCategoria;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Categorias
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
     * Set posicion
     *
     * @param integer $posicion
     * @return Categorias
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Categorias
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isCategoria
     *
     * @param boolean $isCategoria
     * @return Categorias
     */
    public function setIsCategoria($isCategoria)
    {
        $this->isCategoria = $isCategoria;
    
        return $this;
    }

    /**
     * Get isCategoria
     *
     * @return boolean 
     */
    public function getIsCategoria()
    {
        return $this->isCategoria;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Categorias
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Categorias
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
        $this->slug = RpsStms::slugify($this->getCategoria());
    }
    
    
    /*
     * Timestable
     */
    
    /**
     ** @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
        if(!$this->getUpdatedAt())
        {
          $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Add publicaciones
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones
     * @return CategoriasPublicacion
     */
    public function addPublicacione(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones)
    {
        $this->publicaciones[] = $publicaciones;
    
        return $this;
    }

    /**
     * Remove publicaciones
     *
     * @param \Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones
     */
    public function removePublicacione(\Richpolis\PublicacionesBundle\Entity\Publicacion $publicaciones)
    {
        $this->publicaciones->removeElement($publicaciones);
    }

    /**
     * Get publicaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPublicaciones()
    {
        return $this->publicaciones;
    }
}
