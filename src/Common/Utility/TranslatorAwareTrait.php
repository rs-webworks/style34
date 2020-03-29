<?php declare(strict_types=1);

namespace EryseClient\Common\Utility;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslatorTrait
 *
 *
 */
trait TranslatorAwareTrait
{

    /** @var TranslatorInterface $translator */
    protected TranslatorInterface $translator;

    /**
     * @required
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator) : void
    {
        $this->translator = $translator;
    }
}
