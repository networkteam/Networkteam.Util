<?php

namespace Networkteam\Util\Translation;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Symfony\Component\Translation\Dumper\FileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class XliffFileDumper extends FileDumper
{

    /**
     * {@inheritDoc}
     */
    public function formatCatalogue(MessageCatalogue $messages, $domain, array $options = array())
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->createElement('xliff');
        $xliff = $dom->appendChild($xliff);

        $xliff->setAttribute('version', '1.2');
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');

        $xliffFile = $xliff->appendChild($dom->createElement('file'));
        $xliffFile->setAttribute('source-language', $messages->getLocale());
        $xliffFile->setAttribute('datatype', 'plaintext');
        $xliffFile->setAttribute('original', 'file.ext');

        $xliffBody = $xliffFile->appendChild($dom->createElement('body'));
        foreach ($messages->all($domain) as $source => $target) {
            $translation = $dom->createElement('trans-unit');

            $translation->setAttribute('id', $source);
            $translation->setAttribute('resname', $source);

            $s = $translation->appendChild($dom->createElement('source'));
            $s->appendChild($dom->createTextNode($target));

            $t = $translation->appendChild($dom->createElement('target'));
            $t->appendChild($dom->createTextNode($target));

            $xliffBody->appendChild($translation);
        }

        $xmlString = $dom->saveXML();
        // Replace whitespaces with tabs
        return str_replace('  ', "\t", $xmlString);
    }

    /**
     * {@inheritDoc}
     */
    protected function format(MessageCatalogue $messages, $domain)
    {
        return $this->formatCatalogue($messages, $domain);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtension()
    {
        return 'xlf';
    }
}
