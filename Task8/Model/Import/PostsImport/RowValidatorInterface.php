<?php
namespace Speroteck\Task8\Model\Import\PostsImport;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
    const ERROR_TITLE_IS_EMPTY= 'InvalidValueTITLE';
    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
