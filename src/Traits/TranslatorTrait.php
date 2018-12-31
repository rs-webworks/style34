<?php

namespace eRyseClient\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslatorTrait
 * @package eRyseClient\Service\Traits
 */
trait TranslatorTrait
{

    /** @var TranslatorInterface $translator */
    protected $translator;

    /**
     * @required
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}