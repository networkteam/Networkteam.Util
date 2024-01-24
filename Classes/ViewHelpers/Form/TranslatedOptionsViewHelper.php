<?php
namespace Networkteam\Util\ViewHelpers\Form;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;

class TranslatedOptionsViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper
{

    /**
     * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
     *
     * @see AbstractViewHelper::isOutputEscapingEnabled()
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var \Neos\Flow\I18n\Translator
     * @Flow\Inject
     */
    protected $translator;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('prefix', 'string', 'Prefix for translation ids (e.g. "options.foo")');
        $this->registerArgument('translateById', 'boolean', 'Use translateById or translateByOriginalLabel', false,
            true);
        $this->registerArgument('package', 'string', 'Key of the package containing the source file');
        $this->registerArgument('sourceName', 'string',
            'Name of file with translations, base path is $packageKey/Resources/Private/Locale/Translations/', false,
            'Main');
        $this->registerArgument('locale', 'string', 'A valid locale identifier according to UTS#35');
    }

    public function render()
    {
        $prefix = $this->arguments['prefix'];
        $translateById = $this->arguments['translateById'];
        $package = $this->arguments['package'];
        $sourceName = $this->arguments['sourceName'];
        $locale = $this->arguments['locale'];
        if ($package === null) {
            $package = $this->controllerContext->getRequest()->getControllerPackageKey();
        }

        $localeObject = null;
        if ($locale !== null) {
            try {
                $localeObject = new \Neos\Flow\I18n\Locale($locale);
            } catch (\Neos\Flow\I18n\Exception\InvalidLocaleIdentifierException $e) {
                throw new \Neos\FluidAdaptor\Core\ViewHelper\Exception('"' . $locale . '" is not a valid locale identifier.',
                    1372342505);
            }
        }

        $options = $this->renderChildren();
        foreach ($options as $value => &$label) {
            if ($translateById) {
                $labelId = (string)$prefix !== '' ? $prefix . '.' . $label : $label;
                $label = $this->translator->translateById($labelId, array(), null, $localeObject, $sourceName,
                    $package);
            } else {
                $label = $this->translator->translateByOriginalLabel($label, array(), null, $localeObject, $sourceName,
                    $package);
            }
        }

        return $options;
    }
}
