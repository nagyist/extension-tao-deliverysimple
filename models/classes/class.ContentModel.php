<?php

/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * the simplest delivery model representing a single test
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes_simple
 */
class taoSimpleDelivery_models_classes_ContentModel implements taoDelivery_models_classes_ContentModel
{

    /**
     * The simple delivery content extension
     *
     * @var common_ext_Extension
     */
    private $extension;

    public function __construct()
    {
        // ensures the constants are loaded
        $this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoSimpleDelivery');
    }

    public function getClass()
    {
        return new core_kernel_classes_Class(CLASS_SIMPLE_DELIVERYCONTENT);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring(core_kernel_classes_Resource $content)
    {
        common_Logger::i('Generating form for delivery content ' . $content->getUri());
        $widget = new Renderer($this->extension->getConstant('DIR_VIEWS') . 'templates' . DIRECTORY_SEPARATOR . 'authoring.tpl');
        $form = new taoSimpleDelivery_actions_form_ContentForm($this->getClass(), $content);
        $widget->setData('formContent', $form->getForm()
            ->render());
        $widget->setData('saveUrl', _url('save', 'Authoring', 'taoSimpleDelivery'));
        $widget->setData('formId', $form->getForm()->getName());
		return $widget->render();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function createContent($tests = array()) {
        $content = $this->getClass()->createInstance();
        return $content;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function delete(core_kernel_classes_Resource $content) {
    	$content->delete();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent(core_kernel_classes_Resource $content) {
        return $content->duplicate();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeDeliveryLabel(core_kernel_classes_Resource $delivery) {
        // nothing to do
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_ContentModel::compile()
     */
    public function compile( core_kernel_classes_Resource $content, core_kernel_file_File $directory, core_kernel_classes_Resource $resultServer) {
        try {
            $compiler = taoSimpleDelivery_models_classes_DeliveryCompiler::singleton();
            $serviceCall = $compiler->compileDelivery($content, $directory, $resultServer);            
            return $serviceCall;
        } catch (common_Exception $e) {
            throw new taoDelivery_models_classes_CompilationFailedException('Compilation failed: '.$e->getMessage());
        }
    }

    protected function getCompilationFolder( core_kernel_classes_Resource $delivery)
    {
        $returnValue = (string) '';
    
        $fs = taoDelivery_models_classes_RuntimeAccess::getFileSystem();
        $basePath = $fs->getPath();
        $relPath = substr($delivery->getUri(), strpos($delivery->getUri(), '#') + 1).DIRECTORY_SEPARATOR;
        $absPath = $fs->getPath().$relPath;
    
        if (! is_dir($absPath)) {
            if (! mkdir($absPath)) {
                throw new taoDelivery_models_classes_CompilationFailedException('Could not create delivery directory \'' . $absPath . '\'');
            }
        }
    
        return $fs->createFile('', $relPath);
    }
}