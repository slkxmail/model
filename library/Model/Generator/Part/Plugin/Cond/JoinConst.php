<?php

namespace Model\Generator\Part\Plugin\Cond;
use Model\Cluster\Schema\Table;
use Model\Generator\Part\PartInterface;
use Model\Schema\Table\Link\AbstractLink;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\PropertyGenerator;

class JoinConst extends AbstractCond
{
    /**
     * Состоит из
     * 'name' => array('name' => ''
     *                 'defaultValue' => ''
     *                 'type' => \Zend\Code\Generator\PropertyGenerator::*)
     *
     * @var array|PropertyGenerator[]
     */
    protected $_data = array();

	public function __construct()
	{
 		$this->_setName('JoinConst');
	}

    /**
     * @param \Model\Generator\Part\Model|\Model\Generator\Part\PartInterface $part
     */
    public function preRun(PartInterface $part)
	{
        /**
         * @var Table $table
         */
        $table = $part->getTable();

        /**
         * @var $file \Zend\Code\Generator\FileGenerator
         */
        $file = $part->getFile();

        /** @var array|AbstractLink[] $linkList */
        $linkList = $table->getLink();

        foreach ($linkList as $link) {
            $_name = strtoupper($link->getForeignEntity());
            $name = 'JOIN_' . $_name;


            $property = new \Zend\Code\Generator\PropertyGenerator($name, strtolower($_name), \Zend\Code\Generator\PropertyGenerator::FLAG_CONSTANT);

            $tags = array(
                array(
                    'name'        => 'const'
                ),
                array(
                    'name'        => 'var',
                    'description' => 'string',
                ),
            );

            $docblock = new \Zend\Code\Generator\DocBlockGenerator('JOIN сущность ' . $_name);
            $docblock->setTags($tags);

            $property->setDocBlock($docblock);
            $this->_data[$table->getName()][$name] = $property;
        }
	}

    /**
     * @param \Model\Generator\Part\Model|\Model\Generator\Part\PartInterface $part
     */
	public function postRun(PartInterface $part)
	{
        /**
         * @var FileGenerator $file
         */
        $file = $part->getFile();

        /**
         * @var Table $table
         */
        $table = $part->getTable();

        if (isset($this->_data[$table->getName()])) {
            foreach ($this->_data[$table->getName()] as $property) {
                $file->getClass()->addPropertyFromGenerator($property);
            }
        }
    }
}