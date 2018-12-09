<?php

namespace Style34\Service\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslatorTrait
 * @package Style34\Service\Traits
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