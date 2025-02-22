<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsReunion
 */
class CustomTagsReunion extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            'FAM:_UID'   => new PafUid(I18N::translate('Unique identifier')),
            'INDI:CITN'  => new CustomElement(I18N::translate('Citizenship')),
            'INDI:EMAL'  => new AddressEmail(I18N::translate('Email address')),
            'INDI:_LEGA' => new CustomElement(I18N::translate('Legatee')),
            'INDI:_MDCL' => new CustomElement(I18N::translate('Medical')),
            'INDI:_PURC' => new CustomElement('Land purchase'),
            'INDI:_SALE' => new CustomElement('Land sale'),
            'INDI:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'  => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,array<int,array<int,string>>>
     */
    public function customSubTags(): array
    {
        return [
            'FAM'  => [['_UID', '0:M']],
            'INDI' => [['_UID', '0:M']],
            'OBJE' => [['_UID', '0:M']],
            'REPO' => [['_UID', '0:M']],
            'SOUR' => [['_UID', '0:M']],
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Reunion™';
    }
}
