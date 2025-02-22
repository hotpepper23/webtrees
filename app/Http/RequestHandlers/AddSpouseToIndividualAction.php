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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Add a new spouse to an individual, creating a new family.
 */
class AddSpouseToIndividualAction implements RequestHandlerInterface
{
    private GedcomEditService $gedcom_edit_service;

    /**
     * AddChildToFamilyAction constructor.
     *
     * @param GedcomEditService $gedcom_edit_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $params     = (array) $request->getParsedBody();
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        // Create the new spouse
        $levels = $params['ilevels'] ?? [];
        $tags   = $params['itags'] ?? [];
        $values = $params['ivalues'] ?? [];
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom('INDI', $levels, $tags, $values);
        $spouse = $tree->createIndividual("0 @@ INDI\n" . $gedcom);

        // Create the new family
        $levels = $params['flevels'] ?? [];
        $tags   = $params['ftags'] ?? [];
        $values = $params['fvalues'] ?? [];
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom('FAM', $levels, $tags, $values);
        $i_link = "\n1 " . ($individual->sex() === 'F' ? 'WIFE' : 'HUSB') . ' @' . $individual->xref() . '@';
        $s_link = "\n1 " . ($individual->sex() !== 'F' ? 'WIFE' : 'HUSB') . ' @' . $spouse->xref() . '@';
        $family = $tree->createFamily("0 @@ FAM\n" . $gedcom . $i_link . $s_link);

        // Link the individual to the family
        $individual->createFact('1 FAMS @' . $family->xref() . '@', false);

        // Link the spouse to the family
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', false);

        $base_url = Validator::attributes($request)->string('base_url');
        $url      = Validator::parsedBody($request)->isLocalUrl($base_url)->optionalString('url') ?? $spouse->url();

        return redirect($url);
    }
}
