<?php

namespace MonsieurBiz\SyliusCmsPagePlugin\Twig\Component;

use MonsieurBiz\SyliusCmsPagePlugin\Entity\PageInterface;
use MonsieurBiz\SyliusCmsPagePlugin\Repository\PageRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class PageLink
{
    use HookableComponentTrait;

    public string $pageCode;

    public int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;

    public function __construct(
        protected LocaleContextInterface $localeContext,
        protected ChannelContextInterface $channelContext,
        protected PageRepositoryInterface $pageRepository,
        protected UrlGeneratorInterface $urlGenerator,
        protected TranslationLocaleProviderInterface $translationLocaleProvider,
    ) {
    }

    #[ExposeInTemplate('href')]
    public function getHref(): string
    {
        $page = $this->getPage();
        if (!($page instanceof PageInterface)) {
            return '';
        }
        $url = $this->urlGenerator->generate(
            'monsieurbiz_cms_page_show',
            [
                '_locale' => $this->localeContext->getLocaleCode(),
                'slug' => $page->getSlug(),
            ],
            $this->referenceType
        );
        return $url;
    }

    #[ExposeInTemplate(name: 'page_title')]
    public function getPageTitle(): string
    {
        $page = $this->getPage();
        if (!($page instanceof PageInterface)) {
            return '';
        }
        return $page->getTitle();
    }

    /**
     * A page without a translation in the current locale falls back to the default translation locale; its slug and
     * title then come from that translation and the link keeps the current locale prefix.
     */
    private function getPage(): ?PageInterface
    {
        $currentLocaleCode = $this->localeContext->getLocaleCode();
        $channel = $this->channelContext->getChannel();
        $now = new \DateTime();
        return $this->pageRepository->findOneEnabledAndPublishedByPageCodeAndChannelCode(
            $this->pageCode,
            $currentLocaleCode,
            $channel,
            $now,
            $this->translationLocaleProvider->getDefaultLocaleCode()
        );
    }
}
