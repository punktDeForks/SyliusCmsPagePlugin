<?php

/*
 * This file is part of Monsieur Biz' Cms Page plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCmsPagePlugin\Repository;

use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use MonsieurBiz\SyliusCmsPagePlugin\Entity\PageInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PageRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder;

    public function existsOneByChannelAndSlug(ChannelInterface $channel, ?string $locale, string $slug, array $excludedPages = []): bool;

    /** @deprecated Use existsOneEnabledAndPublishedByChannelAndSlug */
    public function existsOneEnabledByChannelAndSlug(ChannelInterface $channel, ?string $locale, string $slug): bool;

    /** @deprecated Use findOneEnabledAndPublishedBySlugAndChannelCode */
    public function findOneEnabledBySlugAndChannelCode(string $slug, string $localeCode, string $channelCode): ?PageInterface;

    /**
     * With a $fallbackLocaleCode, a page without a translation in $locale also matches by its slug in the fallback locale.
     */
    public function existsOneEnabledAndPublishedByChannelAndSlug(ChannelInterface $channel, ?string $locale, string $slug, DateTimeInterface $dateTime, ?string $fallbackLocaleCode = null): bool;

    /**
     * With a $fallbackLocaleCode, a page without a translation in $localeCode is resolved by its slug in the fallback
     * locale; its content then renders through the translatable fallback locale.
     */
    public function findOneEnabledAndPublishedBySlugAndChannelCode(string $slug, string $localeCode, string $channelCode, DateTimeInterface $dateTime, ?string $fallbackLocaleCode = null): ?PageInterface;

    /**
     * With a $fallbackLocaleCode, a page without a translation in $localeCode is resolved by its fallback locale translation.
     */
    public function findOneEnabledAndPublishedByPageCodeAndChannelCode(string $code, string $localeCode, ChannelInterface $channel, DateTimeInterface $dateTime, ?string $fallbackLocaleCode = null): ?PageInterface;
}
