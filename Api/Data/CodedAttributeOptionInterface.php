<?php
namespace SnowIO\AttributeOptionCode\Api\Data;

interface CodedAttributeOptionInterface
{
    /**
     * Constants used as data array keys
     */
    public const LABEL = 'label';

    public const VALUE = 'value';

    public const SORT_ORDER = 'sort_order';

    public const STORE_LABELS = 'store_labels';

    public const IS_DEFAULT = 'is_default';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set option label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get option value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set option value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get option order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set option order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * is default
     *
     * @return bool|null
     */
    public function getIsDefault();

    /**
     * set is default
     *
     * @param bool $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * Get option label for store scopes
     *
     * @return \SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionLabelInterface[]|null
     */
    public function getStoreLabels();

    /**
     * Set option label for store scopes
     *
     * @param \SnowIO\AttributeOptionCode\Api\Data\CodedAttributeOptionLabelInterface[] $storeLabels
     * @return $this
     */
    public function setStoreLabels(array $storeLabels = null);
}
