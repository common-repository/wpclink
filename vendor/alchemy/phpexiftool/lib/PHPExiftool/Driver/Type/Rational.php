<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Type;

use PHPExiftool\Driver\AbstractType;

class Rational extends AbstractType
{

    protected $ExiftoolName = 'rational';

    protected $PHPMap = 'int';

}
