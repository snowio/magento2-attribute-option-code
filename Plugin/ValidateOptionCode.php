<?php

namespace SnowIO\AttributeOptionCode\Plugin;

use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\Controller\Result\Json;
use Magento\Catalog\Controller\Adminhtml\Product\Attribute\Validate;

class ValidateOptionCode
{
    /** @var FormData  */
    private $formData;

    /**
     * ValidateOptionCode constructor.
     * @param FormData $formData
     */
    public function __construct(FormData $formData)
    {
        $this->formData = $formData;
    }

    /**
     * @param Validate $subject
     * @param Json $response
     * @return Json
     */
    public function afterExecute(Validate $subject, Json $response)
    {
        $jsonResponse = $this->getJson($response);

        if (strpos($jsonResponse, 'The value of Admin must be unique.') > 0) {
            $response->setJsonData('{"error":false}');
        }

        $values = $this->formData->unserialize($subject->getRequest()->getParam('serialized_options', '[]'));
        $this->checkUniqueOption($response, $values['option']);

        return $response;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function getJson($response)
    {
        $json = function() {
            return $this->json;
        };
       return $json->call($response);
    }

    /**
     * @param array $optionsValues
     * @param array $deletedOptions
     * @return array
     */
    private function isUniqueOptionCodeValues(array $optionsValues, array $deletedOptions)
    {
        $optionCodeValues = [];
        foreach ($optionsValues as $optionKey => $values) {
            if (!(isset($deletedOptions[$optionKey]) && $deletedOptions[$optionKey] === '1')) {
                $optionCodeValues[] = $values;
            }
        }
        $uniqueValues = array_unique($optionCodeValues);
        return array_diff($optionCodeValues, $uniqueValues);
    }

    /**
     * @param $response
     * @param array|null $options
     * @return $this
     */
    private function checkUniqueOption($response, array $options = null)
    {
        if (is_array($options)
            && isset($options['code'])
            && isset($options['delete'])
            && !empty($options['code'])
            && !empty($options['delete'])
        ) {
            $duplicates = $this->isUniqueOptionCodeValues($options['code'], $options['delete']);
            if (!empty($duplicates)) {
                $message = [__('The value of Option Code must be unique. (%1)', implode(', ', $duplicates))];
                $jsonData = ['error' => true, 'message' => $message];
                $response->setData($jsonData);
            }
        }
        return $this;
    }
}
