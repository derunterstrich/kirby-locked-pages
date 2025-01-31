<?php

namespace JohannSchopplich;

use Kirby\Cms\Page;

final class LockedPages
{
    public const SESSION_KEY = 'johannschopplich.locked-pages.access';

    public static function isLocked(?Page $page): bool
    {
        if ($page === null) {
            return false;
        }

        if ($page->isDraft() || $page->isErrorPage()) {
            return false;
        }

        $protectedPage = static::find($page);
        if ($protectedPage === null) {
            return false;
        }

        $access = kirby()->session()->data()->get(LockedPages::SESSION_KEY, []);
        if (in_array($protectedPage->id(), $access)) {
            return false;
        }

        return true;
    }

    public static function find(Page $page): ?Page
    {
        if ($page->lockedPagesEnable()->exists() && $page->lockedPagesEnable()->toBool()) {
            return $page;
        }

        if ($parent = $page->parent()) {
            return static::find($parent);
        }

        return null;
    }
}
