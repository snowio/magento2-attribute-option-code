<?php
namespace SnowIO\AttributeOptionCode\Api\Data;

interface CodedAttributeOptionLabelInterface
{
    const LABEL = 'label';

    const STORE_CODE = 'store_code';

    /**
     * Get store code
     *
     * @return string|null
     */
    public function getStoreCode();

    /**
     * Set store code
     *
     * @param int $storeCode
     * @return $this
     */
    public function setStoreCode($storeCode);

    /**
     * Get option label
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Set option label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);
}
