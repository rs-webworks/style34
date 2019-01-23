<?php

namespace EryseClient\Utility;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslatorTrait
 * @package EryseClient\Service\Utility
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